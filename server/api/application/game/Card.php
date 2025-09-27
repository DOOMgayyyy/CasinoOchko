<?php
/**
 * Класс Card - представляет игральную карту
 * 
 * Каждая карта имеет масть, ранг и базовое значение для подсчета очков.
 * Используется в игре Очко (Блэкджек) для создания колоды и расчета очков.
 */
class Card 
{
     # Переменная $suit - Масть карты (hearts, diamonds, clubs, spades)
    public $suit;
    
     # Переменная $rank -  Ранг карты (A, 2, 3, ..., 10, J, Q, K)
    public $rank;
    
    # Переменная $value -  Базовое значение карты для подсчета очков
    public $value;

    /**
     * Конструктор класса Card
     * 
     * Создает новую карту с указанной мастью и рангом.
     * Автоматически рассчитывает базовое значение карты.
     * 
     * @param string $suit Масть карты
     * @param string $rank Ранг карты
     */
    public function __construct($suit, $rank) 
    {
        $this->suit = $suit;
        $this->rank = $rank;
        $this->value = $this->calculateValue($rank);
    }

    /**
     * Рассчитывает базовое значение карты по ее рангу
     * 
     * Правила расчета:
     * - Туз (A) = 11 (может быть изменен на 1 в игровой логике)
     * - Карты (K, Q, J) = 10
     * - Числовые карты = их номинал
     * 
     * @param string $rank Ранг карты
     * @return int Базовое значение карты
     */
    private function calculateValue($rank) 
    {
        switch ($rank) {
            case 'A':
                return 11; # Туз по умолчанию 11, может быть изменен на 1
            case 'K':
            case 'Q':
            case 'J':
                return 10; # Все карты стоят 10 очков
            default:
                return (int)$rank; # Числовые карты равны своему номиналу
        }
    }

    /**
     * Возвращает строковое представление карты
     * 
     * Формат: "ранг of масть" (например, "A of hearts")
     * 
     * @return string Строковое представление карты
     */
    public function __toString() 
    {
        return $this->rank . ' of ' . $this->suit;
    }
}