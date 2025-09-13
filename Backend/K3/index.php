<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

// Вспомогательная функция для отправки стандартизированных ответов.
function sendJsonResponse($status, $message, $data = []) {
    http_response_code($status);
    echo json_encode(array_merge(['message' => $message], $data));
    exit();
}

// Путь к файлу базы данных SQLite.
$db_file = __DIR__ . '/gamers.db';

try {
    // Подключение к базе данных SQLite.
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Если подключение не удалось, например, файл не существует или поврежден.
    sendJsonResponse(500, 'Ошибка подключения к базе данных', ['details' => $e->getMessage()]);
}

// Проверка метода запроса и получение данных.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(405, 'Метод не разрешен');
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$nickname = $data['nickname'] ?? null;

// Серверная валидация.
if (empty($email) || empty($password) || empty($nickname)) {
    sendJsonResponse(400, 'Все поля обязательны для заполнения');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(400, 'Некорректный формат email');
}
if (strlen($password) < 8) {
    sendJsonResponse(400, 'Пароль должен содержать не менее 8 символов');
}

// Проверка уникальности email и никнейма в базе данных.
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    sendJsonResponse(409, 'Этот email уже зарегистрирован');
}
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nickname = ?");
$stmt->execute([$nickname]);
if ($stmt->fetchColumn() > 0) {
    sendJsonResponse(409, 'Этот никнейм уже занят');
}

// Хеширование пароля и запись нового пользователя.
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
try {
    $stmt = $pdo->prepare("INSERT INTO users (email, nickname, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$email, $nickname, $passwordHash]);
} catch (PDOException $e) {
    sendJsonResponse(500, 'Ошибка при создании пользователя', ['details' => $e->getMessage()]);
}

// Успешный ответ.
sendJsonResponse(201, 'Регистрация прошла успешно. Пожалуйста, подтвердите ваш email');

?>