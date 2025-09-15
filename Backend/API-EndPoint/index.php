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

/**
 * Генерирует один случайный уникальный никнейм на основе исходного
 * Проверяет доступность в базе данных и возвращает первый свободный вариант
 * 
 * @param string $baseNickname - Исходный никнейм пользователя
 * @param PDO $pdo - Подключение к базе данных для проверки уникальности
 * @return string - Свободный никнейм или вариант с timestamp в крайнем случае
 */
function generateRandomNickname($baseNickname, $pdo) {
    // Массивы слов для генерации случайного имени пользователя
    // Массив, присвоенный переменной $adj, содержит имена прилагательные
    // Массив, присвоенный переменной $nouns, содержит имена существительные

    $adj = [
        'Amazing',
        'Beautiful',
        'Brilliant',
        'Wonderful',
        'Excellent',
        'Fantastic',
        'Gorgeous',
        'Stunning',
        'Magnificent',
        'Spectacular',
        'Charming',
        'Elegant',
        'Graceful',
        'Lovely',
        'Delightful',
        'Enchanting',
        'Captivating',
        'Radiant',
        'Vibrant',
        'Dazzling',
        'Brave',
        'Courageous',
        'Bold',
        'Strong',
        'Powerful',
        'Mighty',
        'Fearless',
        'Confident',
        'Determined',
        'Resilient',
        'Kind',
        'Gentle',
        'Sweet',
        'Caring',
        'Loving',
        'Compassionate',
        'Generous',
        'Thoughtful',
        'Considerate',
        'Empathetic',
        'Smart',
        'Intelligent',
        'Wise',
        'Clever',
        'Talented',
        'Gifted',
        'Skilled',
        'Creative',
        'Innovative',
        'Honest',
        'Trustworthy',
        'Reliable',
        'Loyal',
        'Faithful',
        'Sincere',
        'Genuine',
        'Authentic',
        'Noble',
        'Honorable',
        'Cheerful',
        'Joyful',
        'Happy',
        'Optimistic',
        'Positive',
        'Enthusiastic',
        'Energetic',
        'Lively',
        'Spirited',
        'Dynamic',
        'Peaceful',
        'Calm',
        'Serene',
        'Tranquil',
        'Patient',
        'Understanding',
        'Tolerant',
        'Accepting',
        'Forgiving',
        'Merciful',
        'Hardworking',
        'Diligent',
        'Dedicated',
        'Committed',
        'Ambitious',
        'Motivated',
        'Persistent',
        'Tenacious',
        'Focused',
        'Disciplined',
        'Unique',
        'Special',
        'Extraordinary',
        'Outstanding',
        'Exceptional',
        'Remarkable',
        'Incredible',
        'Precious',
        'Valuable',
        'Perfect'
    ];

    $nouns = [
        'Shadow',
        'Phoenix',
        'Dragon',
        'Wolf',
        'Eagle',
        'Tiger',
        'Lion',
        'Falcon',
        'Hawk',
        'Raven',
        'Thunder',
        'Lightning',
        'Storm',
        'Blaze',
        'Fire',
        'Flame',
        'Spark',
        'Flash',
        'Bolt',
        'Strike',
        'Knight',
        'Warrior',
        'Hunter',
        'Ranger',
        'Scout',
        'Guardian',
        'Defender',
        'Champion',
        'Hero',
        'Legend',
        'Ghost',
        'Spirit',
        'Phantom',
        'Specter',
        'Wraith',
        'Ninja',
        'Samurai',
        'Assassin',
        'Sniper',
        'Archer',
        'Blade',
        'Sword',
        'Dagger',
        'Arrow',
        'Spear',
        'Shield',
        'Armor',
        'Crown',
        'Throne',
        'King',
        'Queen',
        'Prince',
        'Duke',
        'Lord',
        'Master',
        'Chief',
        'Captain',
        'Admiral',
        'General',
        'Commander',
        'Viper',
        'Cobra',
        'Panther',
        'Leopard',
        'Jaguar',
        'Lynx',
        'Bear',
        'Shark',
        'Whale',
        'Dolphin',
        'Star',
        'Nova',
        'Comet',
        'Meteor',
        'Galaxy',
        'Cosmos',
        'Universe',
        'Infinity',
        'Eternity',
        'Destiny',
        'Wizard',
        'Mage',
        'Sorcerer',
        'Oracle',
        'Prophet',
        'Sage',
        'Mystic',
        'Demon',
        'Angel',
        'God',
        'Titan',
        'Giant',
        'Beast',
        'Monster',
        'Creature',
        'Machine',
        'Robot',
        'Cyber',
        'Matrix',
        'Code'
    ];

    $maxAttempts = 15; // Максимальное количество попыток генерации уникального никнейма
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        // Генерируем случайные числа для создания вариантов никнеймов
        $randomNumber = rand(1000, 9999);  // Четырехзначное число
        $smallNumber = rand(10, 99);       // Двузначное число

        // Создаётся массив различных вариантов генерации никнейма
        // Каждый вариант использует разную стратегию комбинирования элементов
        $variants = [
            $baseNickname . $randomNumber,                                    // Пример: User1234
            $baseNickname . '_' . $randomNumber,                             // Пример: User_1234
            $baseNickname . $smallNumber,                                    // Пример: User42
            $adj[array_rand($adj)] . $baseNickname,                         // Пример: AmazingUser
            $baseNickname . $nouns[array_rand($nouns)],                     // Пример: UserDragon
            $baseNickname . '_' . $nouns[array_rand($nouns)],               // Пример: User_Phoenix
            $adj[array_rand($adj)] . $nouns[array_rand($nouns)] . $smallNumber, // Пример: BraveWolf42
            $baseNickname . '_' . $adj[array_rand($adj)],                   // Пример: User_Bold
            strtolower($adj[array_rand($adj)]) . '_' . $baseNickname        // Пример: amazing_User
        ];

        // Происходит случайный выбор одного из вариантов
        $suggestedNickname = $variants[array_rand($variants)];
        
        // Проверка, свободен ли сгенерированный никнейм в базе данных
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$suggestedNickname]);
        
        // Если никнейм не найден в базе (COUNT = 0), значит он свободен
        if ($stmt->fetchColumn() == 0) {
            return $suggestedNickname; // Возвращается первый найденный свободный никнейм
        }

        $attempts++; // Увеличивается счетчик попыток
    }

    //! КРАЙНИЙ СЛУЧАЙ: Если за 15 попыток не удалось найти свободный никнейм,
    //! создаётся гарантированно уникальный вариант с временной меткой Unix
    return $baseNickname . '_' . time(); // Пример: User_1726419234
}

/**
 * Генерирует массив предложений альтернативных никнеймов
 * Используется когда основной никнейм уже занят
 * 
 * @param string $baseNickname - Исходный никнейм пользователя
 * @param PDO $pdo - Подключение к базе данных
 * @param int $count - Количество предложений для генерации (по умолчанию 3)
 * @return array - Массив уникальных свободных никнеймов
 */
function generateMultNick($baseNickname, $pdo, $count = 3) {
    $suggestions = [];                    // Массив для хранения предложений
    $maxAttempts = 30;                   // Максимальное количество попыток генерации
    $attempts = 0;                       // Счетчик текущих попыток

    // Основной цикл генерации предложений
    while (count($suggestions) < $count && $attempts < $maxAttempts) {
        // Генерируем один случайный никнейм
        $suggestion = generateRandomNickname($baseNickname, $pdo);

        // Проверка, что никнейм сгенерирован и не дублируется в наших предложениях
        if ($suggestion && !in_array($suggestion, $suggestions)) {
            $suggestions[] = $suggestion; // Добавляем уникальное предложение
        }

        $attempts++; // Увеличивается счетчик попыток
    }

    //! FALLBACK: Если основная генерация не дала достаточно предложений
    //! Создается простые варианты с временными метками для гарантии уникальности
    while (count($suggestions) < $count) {
        // Используется текущее время + смещение для создания уникального суффикса
        $timestampSuggestion = $baseNickname . '_' . (time() + count($suggestions));
        
        if (!in_array($timestampSuggestion, $suggestions)) {
            $suggestions[] = $timestampSuggestion;
        } else {
            break; // Защита от бесконечного цикла (крайне маловероятный случай)
        }
    }

    return $suggestions; // Возвращается массив предложений
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

//! Проверяем, что запрос был отправлен методом POST.!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
    // КОНФЛИКТ НИКНЕЙМА: Пользователь с таким никнеймом уже существует
    
    // Генерируем альтернативные предложения никнеймов
    $suggestions = generateMultNick($nickname, $pdo, 3);
    
    // ДОПОЛНИТЕЛЬНАЯ ЗАЩИТА: Проверяем, что предложения были успешно сгенерированы
    // Этот блок сработает только в крайне редких случаях системных сбоев
    if (empty($suggestions)) {
        // Создаем простые базовые варианты как последний резерв
        $suggestions = [
            $nickname . rand(1000, 9999),      // Пример: User1234
            $nickname . '_' . rand(100, 999),  // Пример: User_456
            $nickname . rand(10, 99)           // Пример: User78
        ];
    }
    
    // Отправляем ответ с кодом 409 (Conflict) и предложениями альтернативных никнеймов
    sendJsonResponse(409, 'Этот никнейм уже занят', [
        'suggested_nicknames' => $suggestions
    ]);
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


