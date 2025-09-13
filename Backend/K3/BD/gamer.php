<?php

$db_file = 'gamers.db';

if (!file_exists($db_file)) {
    $pdo = new PDO("sqlite:$db_file");
    
    $sql = "CREATE TABLE gamers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL
    )";
    
    $pdo->exec($sql);
    
    echo "Файл базы данных и таблица 'gamers' успешно созданы.";
} else {
    echo "Файл 'gamers.db' уже существует. Создание пропущено.";
}
