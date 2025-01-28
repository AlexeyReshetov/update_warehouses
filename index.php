<?php

// Подключаем файл с функцией для авторизации
require_once 'auth.php';

// Загружаем данные из файла (или создаём пустую структуру)
function loadDataFromFile() {
    if (file_exists('stocks_data.json')) {
        $jsonData = file_get_contents('stocks_data.json');
        return json_decode($jsonData, true);
    }
    return ['warehouses' => [], 'globalStocks' => 0];
}

// Функция для записи данных в файл
function saveDataToFile($warehouses, $globalStocks) {
    $data = [
        'warehouses' => $warehouses,
        'globalStocks' => $globalStocks
    ];

    // Преобразуем данные в строку JSON и сохраняем в файл
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents('stocks_data.json', $jsonData);
}

// Функция для обработки остатков
function processStocks($item, &$warehouses, &$globalStocks) {
    $warehouseUuid = $item['uuid'];
    $stocks = $item['stocks'];
    $isWarehouseFound = isset($warehouses[$warehouseUuid]);

    // Если склад найден, обновляем остатки
    if ($isWarehouseFound) {
        foreach ($stocks as $stock) {
            $warehouses[$warehouseUuid]['stocks'][$stock['uuid']] = $stock['quantity'];
        }
    } else {
        // Если склад не найден, суммируем остатки и обновляем globalStocks
        $sum = 0;
        foreach ($stocks as $stock) {
            $sum += $stock['quantity'];
        }
        $globalStocks = $sum;  // Обновляем globalStocks на сумму остатков для ненайденного склада
    }
}

// Основной блок обработки POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка авторизации
    checkAuthorization();

    // Загружаем текущие данные из файла
    $data = loadDataFromFile();
    $warehouses = $data['warehouses'];
    $globalStocks = $data['globalStocks'];

    // Получаем данные из POST-запроса
    $requestData = json_decode(file_get_contents("php://input"), true);

    if ($requestData) {
        // Обрабатываем данные остатков
        foreach ($requestData as $item) {
            processStocks($item, $warehouses, $globalStocks);
        }

        // Сохраняем обновлённые данные в файл
        saveDataToFile($warehouses, $globalStocks);

        // Отправляем успешный ответ
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "errors" => ["Invalid JSON body"]]);
    }
}
?>
