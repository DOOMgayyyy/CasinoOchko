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

    public function login($login, $hash, $rnd)
    {
        $user = $this->db->getUserByLogin($login);
        if ($user) {
            if (md5($user->password . $rnd) === $hash) {
                $token = md5(rand());
                $this->db->updateToken($user->id, $token);
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'token' => $token
                ];
            }
            return ['error' => 1002];
        }
        return ['error' => 1005];
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

    public function registration($login, $password, $name)
    {
        //проверка логина
        $user = $this->db->getUserByLogin($login);
        if ($user) {
            return ['error' => 1001];
        }

        //проверка имени
        if (!$this->isNameUnique($name)) {
            return ['error' => 1010];
        }

        //все гуд регестрируем
        $this->db->registration($login, $password, $name);
        $user = $this->db->getUserByLogin($login);

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