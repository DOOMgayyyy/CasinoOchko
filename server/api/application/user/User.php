<?php
class User
{
    private $db;
    function __construct($db)
    {
        $this->db = $db;
    }

    public function getUser($token)
    {
        return $this->db->getUserByToken($token);
    }
public function login($email, $hash, $rnd)
    {
        // 1. Ищем пользователя по E-mail
        $user = $this->db->getUserByEmail($email);
        if ($user) {
            if (md5($user->password . $rnd) === $hash) {
                $token = md5(rand());
                $this->db->updateToken($user->id, $token);
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'token' => $token
                ];
            }
            return ['error' => 1002]; // Неверный пароль
        }
        return ['error' => 1005]; // Пользователь не существует
    }

    public function logout($token)
    {
        $user = $this->db->getUserByToken($token);
        if ($user) {
            $this->db->updateToken($user->id, null);
            return true;
        }
        return ['error' => 1003];
    }

    public function registration($email, $password, $name)
{
    //проверка логина (уникальность имени пользователя)
    $user = $this->db->getUserByLogin($name);
    if ($user) {
        return ['error' => 1001];
    }

    //проверка email (уникальность)
    $user = $this->db->getUserByEmail($email);
    if ($user) {
        return ['error' => 1007];
    }

    //все гуд регестрируем
    $this->db->registration($email, $password, $name);
    $user = $this->db->getUserByEmail($email);

    if ($user) {
        $token = md5(rand()); 
        $this->db->updateToken($user->id, $token);
        return [
            'id' => $user->id,
            'name' => $user->name,
            'token' => $token
        ];
    }
    return ['error' => 1004];
}

    //обновление имени с проверкой уникальности 
    public function updateUserName($userId, $newName)
    {
        if ($this->isNameUnique($newName, $userId)) {
            $success = $this->db->updateUserName($userId, $newName);
            if ($success) {
                return $this->db->getUserById($userId);
            }
            return ['error' => 1009];
        }
        return ['error' => 1010];
    }

    //проверка уникального имени
    private function isNameUnique($name, $excludingUserId = null)
    {
        return $this->db->isNameUnique($name, $excludingUserId);
    }
}