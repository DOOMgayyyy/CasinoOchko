<?php
require_once('Card.php');
require_once('gameLogic.php');

class Player
{
    public $id;
    public $cards = [];
    public $balance;
    public $currentBet = 0;
    public $splitHands = []; //для сплита рука
    public $activeHandIndex = 0; // Индекс активной руки

    public function __construct($id, $balance = 1000) //заглушка 1000 баланс 
    {
        $this->id = $id;
        $this->balance = $balance;
        $this->cards = [];
        $this->splitHands = [];
        $this->activeHandIndex = 0;
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
            $this->currentBet = $amount;
            return $amount;
        }

        return 0;
    }

    //сплит
    public function split()
    {
        //проверка возможен ли сплит
        if (count($this->cards) != 2 || $this->cards[0]->rank !== $this->cards[1]->rank) {
            return false;
        }

        //проверка хватит ли на ставку 
        if ($this->balance < $this->currentBet) {
            return false;
        }

        $this->balance -= $this->currentBet; //снятие ставки за 2 руку

        //создание двух рук из 2 карт
        $hand1 = [$this->cards[0]];
        $hand2 = [$this->cards[1]];

        //очищаем основную руку
        $this->cards = [];
        $this->splitHands = [$hand1, $hand2];
        $this->activeHandIndex = 0;

        return true;
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