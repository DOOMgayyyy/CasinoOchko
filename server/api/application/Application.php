<?php
require_once('db/DB.php');
require_once('user/User.php');
require_once('chat/Chat.php');

class Application
{
    private $user;
    private $chat;
    function __construct()
    {
        $db = new DB();
        $this->user = new User($db);
        $this->chat = new Chat($db);
    }

    public function login($params)
    {

        if ($params['email'] && $params['hash'] && $params['rnd']) {
            return $this->user->login($params['email'], $params['hash'], $params['rnd']);
        }
        return ['error' => 242];
    }

    public function logout($params)
    {
        if ($params['token']) {
            $user = $this->user->getUser($params['token']);
            if ($user) {
                return $this->user->logout($params['token']);
            }
            return ['error' => 705];
        }
        return ['error' => 242];
    }

    public function registration($params)
    {

        if ($params['email'] && !filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 242];
        }

        if ($params['email'] && $params['password'] && $params['name']) {
            return $this->user->registration($params['email'], $params['password'], $params['name']);
        }
        return ['error' => 242];
    }

    //обновление имени
    public function updateUserName($params)
    {
        if ($params['token'] && $params['newName']) {
            $user = $this->user->getUser($params['token']);
            if ($user) {
                return $this->user->updateUserName($user->id, $params['newName']);
            }
            return ['error' => 705];
        }
        return ['error' => 242];
    }

    public function sendMessage($params)
    {
        if ($params['token'] && $params['message']) {
            $user = $this->user->getUser($params['token']);
            if ($user) {
                return $this->chat->sendMessage($user->id, $params['message']);
            }
            return ['error' => 705];
        }
        return ['error' => 242];
    }

    public function getMessages($params)
    {
        if ($params['token'] && $params['hash']) {
            $user = $this->user->getUser($params['token']);
            if ($user) {
                return $this->chat->getMessages($params['hash']);
            }
            return ['error' => 705];
        }
        return ['error' => 242];
    }
}