<?php
class DB
{
    private $pdo;
    // разкомент тут MYsql и смена названия базы данных
    function __construct()
    {
        $host = '127.0.0.1';// ip для подключения к бд
        $port = '3306';// порт 
        $user = 'root';// логин для входа в бд
        $pass = ''; // пароль для бд
        $db = 'CasinoOchko';// название базы данных 
        $connect = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";// формирование команды для подключения к базе данных
        // cоздаем объект PDO для работы с БД
        $this->pdo = new PDO($connect, $user, $pass);
        //Объект PDO в контексте веб-разработки — это расширение PHP, которое предоставляет единый, универсальный интерфейс для работы с различными базами данных, абстрагируя различия между ними и позволяя разработчикам писать более переносимый и безопасный код для взаимодействия с БД
    }
    // деструктор, отвечает за закрытие соединеняи с бд
    public function __destruct()
    {
        // закрываем через pdo
        $this->pdo = null;
    }

    // выполнить запрос без возвращения данных, базовый метод для работы с Бд
    private function execute($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql); // Подготавливаем запрос
        return $sth->execute($params); // Выполняем с параметрами
    }

    // получение ОДНОЙ записи
    private function query($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    // получение НЕСКОЛЬКИХ записей
    private function queryAll($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserByLogin($login)
    {
        return $this->query("SELECT * FROM users WHERE login=?", [$login]);
    }

    public function getUserByToken($token)
    {
        return $this->query("SELECT * FROM users WHERE token=?", [$token]);
    }
    // получение id пользователя
    public function getUserById($userId)
    {
        return $this->query("SELECT id, name, login, token FROM users WHERE id = ?", [$userId]);
    }

    public function updateToken($userId, $token)
    {
        $this->execute("UPDATE users SET token=? WHERE id=?", [$token, $userId]);
    }

    public function registration($login, $password, $name)
    {
        $this->execute("INSERT INTO users (login,password,name) VALUES (?, ?, ?)", [$login, $password, $name]); //изменение ошибки переменной
    }

    public function getChatHash()
    {
        return $this->query("SELECT * FROM hashes WHERE id=1");
    }

    public function updateChatHash($hash)
    {
        $this->execute("UPDATE hashes SET chat_hash=? WHERE id=1", [$hash]);
    }

    //обновление имени
    public function updateUserName($userId, $newName)
    {
        return $this->execute("UPDATE users SET name = ? WHERE id = ?", [$newName, $userId]);
    }

    //проверка уникальность имени
    public function isNameUnique($name, $excludingUserId = null)
    {
        if ($excludingUserId) {
            $user = $this->query("SELECT id FROM users WHERE name = ? AND id != ?", [$name, $excludingUserId]);
        } else {
            $user = $this->query("SELECT id FROM users WHERE name = ?", [$name]);
        }
        return $user === false;
    }

    public function addMessage($userId, $message)
    {
        $this->execute('INSERT INTO messages (user_id, message, created) VALUES (?,?, now())', [$userId, $message]);
    }

    public function getMessages()
    {
        return $this->queryAll("SELECT u.name AS author, m.message AS message,
                                to_char(m.created, 'yyyy-mm-dd hh24:mi:ss') AS created FROM messages as m 
                                LEFT JOIN users as u on u.id = m.user_id 
                                ORDER BY m.created DESC"
        );
    }
}