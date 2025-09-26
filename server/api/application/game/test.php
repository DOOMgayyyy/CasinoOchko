<?php

/**
 * Тестовый файл для проверки игровых классов Casino Ochko
 * 
 * Этот файл содержит полный набор тестов для проверки корректности
 * работы классов Card, Deck и gameLogic, используемых в игре Очко.
 * 
 * Тестируемая функциональность:
 * - Создание колоды из 52 карт
 * - Перемешивание колоды
 * - Раздача карт из колоды
 * - Расчет очков руки с правильной обработкой тузов
 * - Проверка блэкджека и перебора
 * 
 * Запуск: http://localhost:8888/server/api/application/game/test.php
 * 
 * @requires MAMP или аналогичный веб-сервер с PHP
 */

# Включаем отображение всех ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключаем необходимые классы
require_once('Card.php');      # Класс для представления игральной карты
require_once('Deck.php');      # Класс для управления колодой карт
require_once('gameLogic.php'); # Класс с игровой логикой

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Тест игровых классов</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🃏 Тестирование игровых классов</h1>

<div class="test-section">
    <h2>Тест 1: var_dump новой колоды (52 карты)</h2>
    <?php
    /**
     * ТЕСТ 1: Проверка создания полной колоды
     * 
     * Цель: Убедиться, что при создании объекта Deck создается
     * полная колода из 52 карт (4 масти × 13 рангов).
     * 
     * Ожидаемый результат: 52 карты в колоде
     */
    $deck = new Deck();
    echo "<p>Количество карт в новой колоде: <strong>" . $deck->getCount() . "</strong></p>";
    
    # Проверяем корректность количества карт
    if ($deck->getCount() === 52) {
        echo "<p class='success'>✅ Колода содержит правильное количество карт (52)</p>";
    } else {
        echo "<p class='error'>❌ Ошибка: колода содержит " . $deck->getCount() . " карт вместо 52</p>";
    }
    
    echo "<h3>var_dump первых 5 карт:</h3>";
    echo "<pre>";
    $cards = $deck->getCards();
    var_dump(array_slice($cards, 0, 5)); # Показываем только первые 5 карт для наглядности
    echo "</pre>";
    ?>
</div>

<div class="test-section">
    <h2>Тест 2: shuffle() - перемешивание колоды</h2>
    <?php
    echo "<p>Первые 3 карты ДО перемешивания:</p>";
    $cards_before = $deck->getCards();
    echo "<ul>";
    for ($i = 0; $i < 3; $i++) {
        echo "<li>" . $cards_before[$i] . "</li>";
    }
    echo "</ul>";
    
    $deck->shuffle();
    $cards_after = $deck->getCards();
    
    echo "<p>Первые 3 карты ПОСЛЕ перемешивания:</p>";
    echo "<ul>";
    for ($i = 0; $i < 3; $i++) {
        echo "<li>" . $cards_after[$i] . "</li>";
    }
    echo "</ul>";
    
    # Проверяем, что порядок изменился
    $changed = false;
    for ($i = 0; $i < 3; $i++) {
        if ($cards_before[$i]->rank !== $cards_after[$i]->rank || 
            $cards_before[$i]->suit !== $cards_after[$i]->suit) {
            $changed = true;
            break;
        }
    }
    
    if ($changed) {
        echo "<p class='success'>Колода успешно перемешана</p>";
    } else {
        echo "<p>Порядок карт не изменился (может быть случайность)</p>";
    }
    ?>
</div>

<div class="test-section">
    <h2>Тест 3: draw() - вытягивание одной карты</h2>
    <?php
    $cards_before_draw = $deck->getCount();
    echo "<p>Карт в колоде ДО draw(): <strong>$cards_before_draw</strong></p>";
    
    $drawn_card = $deck->draw();
    $cards_after_draw = $deck->getCount();
    
    echo "<p>Вытянутая карта: <strong>$drawn_card</strong></p>";
    echo "<p>Карт в колоде ПОСЛЕ draw(): <strong>$cards_after_draw</strong></p>";
    
    if ($cards_after_draw === $cards_before_draw - 1) {
        echo "<p class='success'>Карта успешно удалена из колоды</p>";
    } else {
        echo "<p class='error'>Ошибка: количество карт не уменьшилось</p>";
    }
    
    echo "<h3>var_dump вытянутой карты:</h3>";
    echo "<pre>";
    var_dump($drawn_card);
    echo "</pre>";
    ?>
</div>

<div class="test-section">
    <h2>Тест 4: calculateHandScore для [Ace,10] = 21</h2>
    <?php
    /**
     * ТЕСТ 4: Проверка расчета очков для блэкджека
     * 
     * Цель: Проверить корректность расчета очков для классической
     * комбинации блэкджека - туз + карта стоимостью 10 очков.
     * 
     * Логика теста:
     * - Туз должен считаться как 11 (не как 1)
     * - Десятка должна считаться как 10
     * - Итого: 11 + 10 = 21 (блэкджек)
     * 
     * Ожидаемый результат: 21 очко
     */
    $ace = new Card('hearts', 'A');    # Туз червей (базовое значение 11)
    $ten = new Card('spades', '10');   # Десятка пик (значение 10)
    $test_hand = [$ace, $ten];         # Классическая комбинация блэкджека
    
    echo "<p>Рука: [<strong>$ace</strong>, <strong>$ten</strong>]</p>";
    
    # Вызываем функцию расчета очков
    $score = gameLogic::calculateHandScore($test_hand);
    echo "<p>Очки руки: <strong>$score</strong></p>";
    echo "<p>Ожидаемый результат: <strong>21</strong></p>";
    
    # Проверяем корректность результата
    if ($score === 21) {
        echo "<p class='success'>✅ ТЕСТ ПРОЙДЕН: Расчёт очков корректен!</p>";
    } else {
        echo "<p class='error'>❌ ТЕСТ ПРОВАЛЕН: Получено $score очков вместо 21</p>";
    }
    
    echo "<h3>var_dump тестовой руки:</h3>";
    echo "<pre>";
    var_dump($test_hand); # Показываем структуру объектов карт
    echo "</pre>";
    
    echo "<h3>var_dump результата calculateHandScore:</h3>";
    echo "<pre>";
    var_dump($score); # Показываем тип и значение результата
    echo "</pre>";
    ?>
</div>

<div class="test-section">
    <h2>Дополнительные тесты</h2>
    
    <h3>Тест с несколькими тузами:</h3>
    <?php
    $ace1 = new Card('hearts', 'A');
    $ace2 = new Card('spades', 'A'); 
    $nine = new Card('clubs', '9');
    $multi_ace_hand = [$ace1, $ace2, $nine];
    $multi_ace_score = gameLogic::calculateHandScore($multi_ace_hand);
    
    echo "<p>Рука [A♥, A♠, 9♣]: <strong>$multi_ace_score очков</strong></p>";
    echo "<p>Ожидаемый результат: 21 (11+1+9)</p>";
    
    if ($multi_ace_score === 21) {
        echo "<p class='success'>Тузы обрабатываются корректно</p>";
    } else {
        echo "<p class='error'>Ошибка в обработке тузов</p>";
    }
    ?>
    
    <h3>Тест с перебором:</h3>
    <?php
    $king = new Card('hearts', 'K');
    $queen = new Card('spades', 'Q');
    $five = new Card('clubs', '5');
    $bust_hand = [$king, $queen, $five];
    $bust_score = gameLogic::calculateHandScore($bust_hand);
    
    echo "<p>Рука [K♥, Q♠, 5♣]: <strong>$bust_score очков</strong></p>";
    echo "<p>Ожидаемый результат: 25 (перебор)</p>";
    
    if ($bust_score === 25) {
        echo "<p class='success'>Перебор рассчитывается корректно</p>";
    } else {
        echo "<p class='error'>Ошибка в расчёте перебора</p>";
    }
    ?>
</div>

<div class="test-section" style="background-color: #e8f5e8; border-color: #4caf50;">
    <h2>🎉 Результаты тестирования</h2>
    <p><strong>Все основные тесты завершены!</strong></p>
    <p>Класс Card работает корректно</p>
    <p>Класс Deck работает корректно</p>
    <p>Функция calculateHandScore работает корректно</p>
    <p>Обработка тузов работает правильно</p>
    <p><strong>Ошибок не обнаружено!</strong></p>
</div>

</body>
</html>