<?php

// Функция для проверки Basic Auth
function checkAuthorization() {
    // Проверяем, передан ли заголовок авторизации
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(["success" => false, "errors" => ["Authorization required"]]);
        exit;
    }

    // Проверяем логин и пароль (замените на свои реальные данные)
    $validUsername = "admin";
    $validPassword = "password";

    if ($_SERVER['PHP_AUTH_USER'] !== $validUsername || $_SERVER['PHP_AUTH_PW'] !== $validPassword) {
        header('HTTP/1.0 403 Forbidden');
        echo json_encode(["success" => false, "errors" => ["Invalid credentials"]]);
        exit;
    }
}
?>