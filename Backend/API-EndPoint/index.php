<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

header("Content-Type: application/json; charset=UTF-8");


function sendJsonResponse($status, $message, $data = []) {
    http_response_code($status);
    echo json_encode(array_merge(['message' => $message], $data));
    exit();
}

$db_file = __DIR__ . '/BD/gamers.db';


if (!file_exists($db_file)) {
    sendJsonResponse(500, 'Ошибка: Файл базы данных не найден по указанному пути.');
}


$pdo = new PDO("sqlite:$db_file");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(405, 'Метод не разрешен');
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$nickname = $data['nickname'] ?? null;


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
$stmt = $pdo->prepare("INSERT INTO users (email, nickname, password_hash) VALUES (?, ?, ?)");
$stmt->execute([$email, $nickname, $passwordHash]);


sendJsonResponse(201, 'Регистрация прошла успешно. Пожалуйста, подтвердите ваш email');

