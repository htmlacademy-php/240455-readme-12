<?php

define('SORTING', array(
    ['popularity', 'Популярность'],
    ['likes', 'Лайки'],
    ['date', 'Дата'],
));

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
function get_interval ($date, $not_ago = 0) {
    
    $cur_date = date_create("now"); // создаёт экземпляр даты
    $date = date_create($date); // создаёт экземпляр даты
    $date_string = $date->format('Y.m.d H:i'); // возвращает дату в указанном формате string
    $cur_date_string = $cur_date->format('Y.m.d H:i'); // возвращает дату в указанном формате string
    $diff = date_diff($date, $cur_date); // возвращает разницу между датами
    $days = $diff->days; // возвращает разницу между датами в днях
    $hours_in_day = 24; // часов в сутках
    $days_in_week = 7; // дней в неделе
    $days_in_5weeks = 35; // дней в 5 неделях
    $days_in_year = 365.2468; // дней в году
    if ($cur_date_string > $date_string) {
        if ($days < 1) {
            $hours = $diff->h; 
            if (1 <= $hours and $hours < $hours_in_day) {
                $time_count = $hours . " час" . get_noun_plural_form($hours, '', 'а', 'ов');
            }
            elseif ($hours < 1) {
                $minuts = $diff->i; 
                $time_count = $minuts . " минут" . get_noun_plural_form($minuts, 'у', 'ы', '');
            }
        } elseif (1 <= $days) {
            if ($days < $days_in_week) {
                $time_count = $days . " " . get_noun_plural_form($days, 'день', 'дня', 'дней');
            } elseif ($days_in_week <= $days and $days < $days_in_5weeks) {
                $weeks = floor($days / $days_in_week);
                $time_count = $weeks . " недел" . get_noun_plural_form($weeks, 'ю', 'и', 'ь');
            } elseif ($days_in_5weeks <= $days and $days < $days_in_year) {
                $months = $diff->m;
                $time_count = $months . " месяц" . get_noun_plural_form($months, '', 'а', 'ев');
            } elseif ($days_in_year <= $days) {
                $years = $diff->y;
                $time_count = $years . " " . get_noun_plural_form($years, 'год', 'года', 'лет');
            }
        }
        
        if (!$not_ago) {
            $time_count .= " назад";
        }
        
    } elseif ($cur_date_string == $date_string) {
        $time_count = "только что";
    } else { 
        $time_count = $date_string . " - дата в будущем";
    }
    
    return $time_count;
}

/**
 * Принимает соединение и запрос и выдает результат/массив
 *
 * @param mysqli $db_link Соединение
 * @param string $query Запрос
 * @param int $mode Тип ответа 
 * @return array
 */

function get_result ($db_link, $query, $mode) {
    
    $result = mysqli_query($db_link, $query);
    
    $rows = mysqli_num_rows($result);
    
    if ($rows) {
        if ($mode == 1) { // одно значение
            $array = mysqli_fetch_array($result);
            $array = $array[0];
        } elseif ($mode == 2) { // несколько записей и несколько полей (двумерный)
            $array = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } elseif ($mode == 3) { // несколько полей одной записи (ряд)
            $array = mysqli_fetch_assoc($result);
        } elseif ($mode == 4) { // одно поле из нескольких записей (колонка) 
            $array = mysqli_fetch_all($result);
            $array = array_column($array,0); 
        } else {
            exit('Неверный mode');
        }
    } else {
        $array = [];
    }
    
    return $array;
}
