<?php

define('SORTING', array(
    'popularity' => 'Популярность',
    'likes' => 'Лайки',
    'date' => 'Дата',
));

// разрешенные типы фото
define('ALLOW_EXT', array(
    'png',
    'jpeg',
    'jpg',
    'gif',
));

define('DATE_FORMAT', "d.m.Y H:i");
define('DATE_FORMAT_ORIGINAL', "Y.m.d H:i");

define('VISIBLE_COMMENTS', 2);

date_default_timezone_set('Europe/Moscow');

mb_internal_encoding("UTF-8");

/**
 * Обрезает текстовое содержимое если оно превышает заданное число символов. Также, если текст был обрезан, добавляет к нему ссылку «Читать далее»
 *
 * @param string $text Текстовая строка
 * @param string $link Ссылка
 * @param int $max_len Максимальная длина текста 
 * @return string
 */
function cut_text ($text, $link, $max_len = 300) {
    
    $text_trimmed = trim($text);
    $text_len = mb_strlen($text_trimmed);

    if ($text_len > $max_len) {
       
        $text = mb_substr($text, 0, $max_len,'UTF-8'); // Обрезаем и работаем со всеми кодировками и указываем исходную кодировку
        $position = mb_strrpos($text, ' ', 0, 'UTF-8'); // Определение позиции последнего пробела. Именно по нему и разделяем слова
        $text = mb_substr($text, 0, $position, 'UTF-8'); // Обрезаем переменную по позиции

        $text .= '... <a class="post-text__more-link" href="' . $link . '">Читать далее</a>';

    } 

    return $text;
}

/**
 * Функция-фильтр от XSS
 *
 * @param array $arr Фильтруемый массив
 * @return
 */
function filter_xss (&$arr) {
    
    $arr = htmlentities($arr);
}

/**
 * Получаем прошедший интервал времени в относительном формате.
 * n < 60 минут -> "n минут назад",
 * 1 часа <= n < 24 часов -> "n часов назад", 
 * 1 дня <= n < 7 дней -> "n дней назад",
 * 7 дня <= n < 35 дней -> "n недель назад", 
 * 35 дней <= n -> "n месяцев назад"
 *
 * @param string $date Дата прошедшего события
 * @param bool $is_ago Признак слова "назад"
 * @return string $interval Возвращает интервал между экземплярами дат
 */
function get_interval ($date, $is_ago = false) {
    
    $cur_date = date_create("now"); // создаёт экземпляр текущей даты
    $date = date_create($date); // создаёт экземпляр даты
    $date_string = $date->format(DATE_FORMAT_ORIGINAL); // возвращает дату в указанном формате string
    $cur_date_string = $cur_date->format(DATE_FORMAT_ORIGINAL); // возвращает текущую дату в указанном формате string
    $diff = date_diff($date, $cur_date); // возвращает разницу между датами
    $days = $diff->days; // возвращает разницу между датами в днях
    $days_in_week = 7; // дней в неделе
    $days_in_5weeks = 35; // дней в 5 неделях
    $days_in_year = 365; // дней в году
    
    if ($cur_date_string > $date_string) { // прошедшая дата
        if ($days < 1) {
            $hours = $diff->h; //max 23
            if (1 <= $hours) {
                $time_count = $hours . " час" . get_noun_plural_form($hours, '', 'а', 'ов');
            } else {
                $minuts = $diff->i; 
                $time_count = $minuts . " минут" . get_noun_plural_form($minuts, 'у', 'ы', '');
            }
        } elseif ($days >= 1) {  // если разница больше или равна одному дню
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
        
        if ($is_ago) {
            $time_count .= " назад";
        }
        
    } elseif ($cur_date_string === $date_string) { // если текущая дата и принятая одинаковы
        $time_count = "только что";
    } else { // дата в будущем
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
 * @return array | int
 */
function get_result ($db_link, $query, $mode = 2) {
    
    $result = mysqli_query($db_link, $query);
    
    $rows = mysqli_num_rows($result);
    
    if ($rows) {
        if ($mode === 1) { // одно значение
            $array = mysqli_fetch_array($result);
            $array = $array[0];
        } elseif ($mode === 2) { // несколько записей и несколько полей (двумерный)
            $array = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } elseif ($mode === 3) { // несколько полей одной записи (ряд)
            $array = mysqli_fetch_assoc($result);
        } elseif ($mode === 4) { // одно поле из нескольких записей (колонка) 
            $array = mysqli_fetch_all($result);
            $array = array_column($array, 0); 
        } else {
            exit('Неверный mode');
        }
    } else {
        $array = [];
    }
    
    return $array;
}

/**
 * Принимает таблицу и условие и выдает количество
 *
 * @param string $db_link Соединение
 * @param string $table Таблица
 * @param string $condition Условие
 * @return int Количество
 */

function get_number ($db_link, $table, $condition) {
    
    $query = '
        SELECT COUNT(id)
        FROM ' . $table . '
        WHERE '. $condition;
    
    $number = get_result($db_link, $query, 1);
    
    return $number;
}

/**
 * Проверка на корректную ссылку
 *
 * @param string $value Значение поля
 * @return bool true при корректной ссылке, иначе false 
 */

function validate_url($value) {
    
    return filter_var($value, FILTER_VALIDATE_URL);
}

/**
 * Проверка длины
 *
 * @param string $name Значение поля
 * @param string $explain Текст пояснения
 * @param int $min Минимальное значение
 * @param int $max Максимальное значение
 * @return string Текст
 */

function validate_length($name, &$explain, $min, $max = 300) {
    
    $len = mb_strlen($name);
    
    if ($res = $len < $min or $len > $max) {
        $explain = "Значение должно быть от $min до $max символов";
    }
    
    return $res;
}

/**
 * Получение массива категорий
 *
 * @param mysqli $db_link Соединение
 * @return array
 */

function get_сategories($db_link) {
    $query = 'SELECT * FROM category ORDER BY id';
    
    return get_result($db_link, $query);
}

/**
 *Преобразование строки хеш-тегов в массив
 *
 *@param string $tags
 *@return array
 */
function hash_tags2arr($tags) {
    
    $tags = trim($tags,' #'); // убираем концевые пробелы строки и первый #
    $arr = explode('#',$tags); // разбивка на массив
    $arr = array_map('trim',$arr); // удаляем концевые пробелы слова
    $arr = array_unique($arr); // удаляем возможные дубли
    
    return $arr;
}

/**
 * Проверка наличия/существования удаленного файла (страницы)
 *
 * @param string $url
 * @return bool результат проверки
 */
function is_url_exist($url) {
    
    $urlHeaders = get_headers($url);
    $res = strpos($urlHeaders[0], '200');
    
    return (bool) $res;
}

/**
 * Валидация ссылки
 *
 * @param string $value Значение поля
 * @param string $category_chosen Категория публикации
 * @param array $errors Массив ошибок по ссылке
 * @return array Массив ошибок
 */
function url_check($value, $category_chosen, &$errors) {
    if (!validate_url($value)) {
        $errors['url']['head'] = "Ссылка";
        $errors['url']['description'] = "Укажите корректную ссылку";
    } else {
        if ($category_chosen === 'video') {
            $youtube_check = check_youtube_url($value);
            if ($youtube_check !== true) {
                $errors['url']['head'] = "Youtube ссылка";
                $errors['url']['description'] = $youtube_check;
            }
        } else {
            if (!is_url_exist($value)) {
                $errors['url']['head'] = "Ссылка";
                $errors['url']['description'] = "Страница не найдена";
            }
        }
    }
    return $errors;
}
