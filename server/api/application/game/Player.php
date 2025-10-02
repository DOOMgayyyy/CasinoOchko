<?php
require_once('Card.php');
require_once('gameLogic.php');

class Player
{
    public $id;
    public $cards = [];
    public $balance;

    public function __construct($id, $balance = 1000) //заглушка 1000 баланс 
    {
        $this->id = $id;
        $this->balance = $balance;
        $this->cards = [];
    }

    //добавление карты в руку
    public function addCard(Card $card)
    {
        $this->cards[] = $card;
        return $this;
    }

    //расчет очков руки
    public function getScore()
    {
        return gameLogic::calculateHandScore($this->cards);
    }

    //делаем ставку если хватает денег. amount - сумма ставки 
    public function makeBet($amount)
    {
        //минимальная ставка 50
        if ($amount < 50) {
            return 0;
        }

        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            return $amount;
        }

        return 0;
    }
}

//проверка на перебор руки
function isBust($hand)
{
    if ($hand instanceof Player) {
        return $hand->getScore() > 21;
    } elseif (is_array($hand)) {
        return gameLogic::calculateHandScore($hand) > 21;
    }
    return false;
}