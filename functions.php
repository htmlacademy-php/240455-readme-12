<?php

mb_internal_encoding("UTF-8");

/**
 * Обрезает текстовое содержимое если оно превышает заданное число символов. Также, если текст был обрезан, добавляет к нему ссылку «Читать далее»
 *
 * @param string $text Текстовая строка
 * @param int $maxLen Максимальная длина текста 
 *
 * @return string
 */
function cut_text ($text, $maxLen = 300) {
    
    $textTrimmed = trim($text);

    $textNum = mb_strlen($textTrimmed);

    if ($textNum > $maxLen) {
       
        $text = mb_substr($text, 0, $maxLen,'UTF-8'); // Обрезаем и работаем со всеми кодировками и указываем исходную кодировку
        $position = mb_strrpos($text, ' ', 'UTF-8'); // Определение позиции последнего пробела. Именно по нему и разделяем слова
        $text = mb_substr($text, 0, $position, 'UTF-8'); // Обрезаем переменную по позиции

        $text .= '... <a class="post-text__more-link" href="#">Читать далее</a>';

    } 

    return $text;
}

/**
 * Функция-фильтр от XSS
 *
 * @param string $value Значение массива
 *
 * @return string
 */

function filter_xss (&$value) {
    $value = htmlentities($value);
}

/**
 * Рассчитывает интервал между экземплярами дат в относительном формате
 *
 * @param string $date Дата
 *
 * @return integer $interval Возвращает интервал между экземплярами дат
 */

function find_interval ($date) {
    
    $cur_date = date_create("now"); // создаёт экземпляр даты на основе формата
    
    $date = date_create($date);
    
    $diff = date_diff($cur_date, $date);
    
    $minuts_count = date_interval_format($diff, "%i");
    $minuts_count = (int)$minuts_count;
    
    $hours_count = date_interval_format($diff, "%h");
    $hours_count = (int)$hours_count;
    
    $days_count = date_interval_format($diff, "%d");
    $days_count = (int)$days_count;

    $month_count = date_interval_format($diff, "%m");
    $month_count = (int)$month_count;
    
    switch (true) {
        case ($minuts_count !== 0):
            
        $interval = $minuts_count;
           
            return $interval . " " . 
            get_noun_plural_form(
                $interval,
                'минута',
                'минуты',
                'минут'
            ) . " назад";
            
        case ($hours_count !== 0):
            
        $interval = $hours_count;
            
            return $interval . " " . 
            get_noun_plural_form(
                $interval,
                'час',
                'часа',
                'часов'
            ) . " назад";
            
        case ($days_count < 7 && $days_count !== 0):
            
        $interval = $days_count;
            
            return $interval . " " . 
            get_noun_plural_form(
                $interval,
                'день',
                'дня',
                'дней'
            ) . " назад";
            
        case ($days_count > 6 && $days_count < 35):
            
        $interval = $days_count / 7;
        $interval = floor($interval);
            
            return $interval . " " . 
            get_noun_plural_form(
                $interval,
                'неделю',
                'недели',
                'недель'
            ) . " назад";
            
        case ($month_count !== 0):
            
        $interval = $month_count;
            
            return $interval . " " . 
            get_noun_plural_form(
                $interval,
                'месяц',
                'месяца',
                'месяцев'
            ) . " назад";
    }
}