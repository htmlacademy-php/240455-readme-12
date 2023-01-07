<?php

date_default_timezone_set('Europe/Moscow');

mb_internal_encoding("UTF-8");

/**
 * Обрезает текстовое содержимое если оно превышает заданное число символов. Также, если текст был обрезан, добавляет к нему ссылку «Читать далее»
 *
 * @param string $text Текстовая строка
 * @param int $max_len Максимальная длина текста 
 * @return string
 */
function cut_text ($text, $max_len = 300) {
    
    $text_trimmed = trim($text);

    $text_num = mb_strlen($text_trimmed);

    if ($text_num > $max_len) {
       
        $text = mb_substr($text, 0, $max_len,'UTF-8'); // Обрезаем и работаем со всеми кодировками и указываем исходную кодировку
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
 * Получаем прошедший интервал времени в относительном формате.
 * n < 60 минут -> "n минут назад",
 * 1 часа <= n < 24 часов -> "n часов назад", 
 * 1 дня <= n < 7 дней -> "n дней назад",
 * 7 дня <= n < 35 дней -> "n недель назад", 
 * 35 дней <= n -> "n месяцев назад"
 *
 * @param string $date Дата
 * @return string $interval Возвращает интервал между экземплярами дат
 */

function get_interval ($date) {
    
    $cur_date = date_create("now"); // создаёт экземпляр даты на основе формата
    
    $date = date_create($date);
    
    $date_string = $date->format('Y.m.d H:i');
    
    $diff = date_diff($date, $cur_date);
    
    $days = $diff->days;
    
    $one = 1; // один
    
    $hours_in_day = 24; // часов в сутках
    
    $days_in_week = 7; // дней в неделе
    
    $days_in_5weeks = 35; // дней в 5 неделях
    
    if ($cur_date == $date || $cur_date > $date) {
        if ($days < $one) {
            $hours = $diff->h; 
            if ($one <= $hours && $hours < $hours_in_day) {
                $time_count = $hours . " час" . get_noun_plural_form($hours, '', 'а', 'ов');
            }
            elseif ($hours < $one) {
                $minuts = $diff->i; 
                $time_count = $minuts . " минут" . get_noun_plural_form($minuts, 'у', 'ы', '');
            }
        } elseif ($one <= $days) {
            if ($days < $days_in_week) {
                $time_count = $days . " " . get_noun_plural_form($days, 'день', 'дня', 'дней');
            } elseif ($days_in_week <= $days && $days < $days_in_5weeks) {
                $weeks = floor($days / $days_in_week);
                $time_count = $weeks . " недел" . get_noun_plural_form($weeks, 'ю', 'и', 'ь');
            } elseif ($days_in_5weeks <= $days) {
                $months = $diff->m;
                $time_count = $months . " месяц" . get_noun_plural_form($months, '', 'а', 'ев');
            }
        }
        
        return $time_count . " назад";
        
    } else {
        return $date_string;
    }
    

}