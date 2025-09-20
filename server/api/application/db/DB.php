<?php
class DB
{
    private $pdo;
    // разкомент тут MYsql и смена названия базы данных
    function __construct()
    {
        $host = '127.0.0.1';
        $port = '3306';
        $user = 'root';
        $pass = ''; 
        $db = 'CasinoOchko';
        $connect = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
        $this->pdo = new PDO($connect, $user, $pass);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    // выполнить запрос без возвращения данных
    private function execute($sql, $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        return $sth->execute($params);
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