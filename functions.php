<?php

mb_internal_encoding("UTF-8");

/**
 * Обрезает текстовое содержимое если оно превышает заданное число символов. Также, если текст был обрезан, добавляет к нему ссылку «Читать далее»
 *
 * @param string $text Текстовая строка
 * @param int $maxLen Максимальная длина текста 
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
 * @return string
 */

function filter_xss (&$value) {
    $value = htmlentities($value);
}

/**
 * Получаем прошедший интервал времени в относительном формате
 *
 * @param string $date Дата
 * @return string $interval Возвращает интервал между экземплярами дат
 */

function get_interval ($date) {
    
    $cur_date = date_create("now"); // создаёт экземпляр даты на основе формата
    
    $date = date_create($date);
    
    $diff = date_diff($date, $cur_date);
    
    $time_count = $diff->days;
    
    if ($time_count < 1) {
        $time_count = $diff->h; 
        if ($time_count > 1) {
            $time_count = $time_count . " час" . get_noun_plural_form($time_count, '', 'а', 'ов');
        }
        elseif ($time_count < 1) {
            $time_count = $diff->i; 
            $time_count = $time_count . " минут" . get_noun_plural_form($time_count, 'у', 'ы', '');
        }
    } else {
        if ($time_count < 7) {
            $time_count = $time_count . " " . get_noun_plural_form($time_count, 'день', 'дня', 'дней');
        } elseif (6 < $time_count && $time_count < 35) {
            $time_count = $time_count / 7;
            $time_count = floor($time_count);
            $time_count = $time_count . " недел" . get_noun_plural_form($time_count, 'ю', 'и', 'ь');
        } elseif (35 < $time_count) {
            $time_count = $diff->m;
            $time_count = $time_count . " месяц" . get_noun_plural_form($time_count, '', 'а', 'ев');
        }
    }
    
    return $time_count . " назад";
}