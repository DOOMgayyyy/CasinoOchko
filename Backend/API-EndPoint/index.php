<?php
// Устанавливаем заголовки для CORS (Cross-Origin Resource Sharing).
// Access-Control-Allow-Origin: * - Разрешает запросы с любого источника (домена).
header("Access-Control-Allow-Origin: *");
// Access-Control-Allow-Methods: POST, OPTIONS - Указывает разрешенные HTTP-методы.
header("Access-Control-Allow-Methods: POST, OPTIONS");
// Access-Control-Allow-Headers: Content-Type - Указывает разрешенные заголовки в запросе.
header("Access-Control-Allow-Headers: Content-Type");

// Обработка предварительного запроса (preflight request) методом OPTIONS.
// Браузеры отправляют такой запрос перед основным (например, POST), чтобы проверить, разрешены ли CORS-заголовки.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Если это OPTIONS-запрос, просто завершаем выполнение скрипта с кодом 200 OK.
    exit();
}
// Устанавливаем заголовок Content-Type, чтобы клиент знал, что ответ будет в формате JSON.
header("Content-Type: application/json; charset=UTF-8");

/**
 * Функция для отправки ответа в формате JSON.
 * @param int $status - HTTP-статус код ответа (например, 200, 404, 500).
 * @param string $message - Сообщение для пользователя.
 * @param array $data - Дополнительные данные для включения в ответ (необязательно).
 */
function sendJsonResponse($status, $message, $data = []) {
    // Устанавливаем HTTP-статус ответа.
    http_response_code($status);
    // Формируем массив ответа, объединяя сообщение и дополнительные данные.
    echo json_encode(array_merge(['message' => $message], $data));
    exit();
}

// Определяем путь к файлу базы данных SQLite. __DIR__ - это магическая константа, которая содержит путь к директории текущего файла.
$db_file = __DIR__ . '/BD/users.db';

// Проверяем, существует ли файл базы данных по указанному пути.
if (!file_exists($db_file)) {
    // Если файл не найден, отправляем ошибку 500 (Internal Server Error).
    sendJsonResponse(500, 'Ошибка: Файл базы данных не найден по указанному пути.');
}

// Создаем новое подключение к базе данных SQLite с помощью PDO (PHP Data Objects).
$pdo = new PDO("sqlite:$db_file");
// Устанавливаем режим обработки ошибок. PDO::ERRMODE_EXCEPTION будет выбрасывать исключения при ошибках SQL.
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Проверяем, что запрос был отправлен методом POST.!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Если используется любой другой метод, отправляем ошибку 405 (Method Not Allowed).
    sendJsonResponse(405, 'Метод не разрешен');
}

// Получаем тело запроса (в формате JSON) и декодируем его в ассоциативный массив PHP.
// 'php://input' - это специальный поток, позволяющий читать сырые данные из тела запроса.
$data = json_decode(file_get_contents('php://input'), true);
// Извлекаем данные из массива. Используем оператор объединения с null (??) для присвоения null, если ключ отсутствует.
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$nickname = $data['nickname'] ?? null;








// --- ВАЛИДАЦИЯ ДАННЫХ ---

// Проверяем, что все необходимые поля были переданы и не являются пустыми.
if (empty($email) || empty($password) || empty($nickname)) {
    // Если какое-то поле пустое, отправляем ошибку 400 (Bad Request).
    sendJsonResponse(400, 'Все поля обязательны для заполнения');
}
// Проверяем корректность формата email с помощью встроенного фильтра PHP.
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Если email некорректен, отправляем ошибку 400.
    sendJsonResponse(400, 'Некорректный формат email');
}
// Проверяем минимальную длину пароля.
if (strlen($password) < 8) {
    // Если пароль слишком короткий, отправляем ошибку 400.
    sendJsonResponse(400, 'Пароль должен содержать не менее 8 символов');
}



// --- ПРОВЕРКА УНИКАЛЬНОСТИ В БАЗЕ ДАННЫХ ---




// Готовим SQL-запрос для проверки, существует ли пользователь с таким email.
// Использование подготовленных выражений (?) защищает от SQL-инъекций.
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
// Выполняем запрос, подставляя значение переменной $email вместо знака вопроса.
$stmt->execute([$email]);
// fetchColumn() возвращает значение единственного столбца из следующей строки результата.
if ($stmt->fetchColumn () > 0) {
    // Если найдена хотя бы одна запись, значит email уже используется. Отправляем ошибку 409 (Conflict).
    sendJsonResponse(409, 'Этот email уже зарегистрирован');
}
// Аналогично проверяем уникальность никнейма.
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
$stmt->execute([$nickname]);
if ($stmt->fetchColumn() > 0) {
    // Если никнейм занят, отправляем ошибку 409.
    sendJsonResponse(409, 'Этот никнейм уже занят');
}


// --- СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ ---

// Хешируем пароль. password_hash() создает безопасный хеш. PASSWORD_DEFAULT использует текущий наилучший алгоритм.
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
// Готовим SQL-запрос для вставки нового пользователя в таблицу `users`.
$stmt = $pdo->prepare("INSERT INTO users (email, username, password_hash) VALUES (?, ?, ?)");
// Выполняем запрос, передавая email, никнейм и хешированный пароль.
$stmt->execute([$email, $nickname, $passwordHash]);

// Если все прошло успешно, отправляем ответ со статусом 201 (Created).
sendJsonResponse(201, 'Регистрация прошла успешно. Пожалуйста, подтвердите ваш email');


