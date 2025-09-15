API Документация 

Общие сведения
Базовый URL: http://<твой_сервер>/index.php
Формат данных: application/json
Методы: POST (основной), OPTIONS (для CORS preflight)

РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЕЙ

URL: POST /index.php

Тело запроса (JSON): 

{
  "email": "user@example.com",
  "password": "MySecret123",
  "nickname": "PlayerOne"
}

Валидация:
email — обязателен, формат проверяется.
password — обязателен, хешируется.
nickname — обязателен, уникальный.

Успешный ответ:
{
  "message": "Пользователь успешно зарегистрирован",
  "id": 1,
  "email": "user@example.com",
  "nickname": "PlayerOne",
  "wallet": 0
}

ОШИБКИ:

| Код | Сообщение                                      |
| --- | ---------------------------------------------- |
| 400 | Все поля обязательны для заполнения            |
| 400 | Некорректный формат email                      |
| 400 | Пароль слишком короткий                        |
| 409 | Такой email или nickname уже существует        |
| 500 | Ошибка базы данных / внутренняя ошибка сервера |


СТРУКТУРА БД

id — INTEGER PRIMARY KEY AUTOINCREMENT
email — TEXT UNIQUE
username — TEXT UNIQUE
password_hash — TEXT
wallet — REAL DEFAULT 0
