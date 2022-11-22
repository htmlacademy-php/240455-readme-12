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
 * @param string $value значение массива
 *
 * @return string
 */

function filter_xss (&$value) {
    $value = htmlspecialchars($value, ENT_QUOTES);
}