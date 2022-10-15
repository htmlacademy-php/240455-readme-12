<?php
/**
 * Обрезает текстовое содержимое если оно превышает заданное число символов. Также, если текст был обрезан, добавляет к нему ссылку «Читать далее»
 *
 * @param string $text Данные для обрезания
 * @param int $numLetters Количество символов, до которого нужно обрезать текст 
 *
 * @return string
 */
function cut_text ($text, $numLetters = 300) {

    $textNum = mb_strlen($text);

    if ($textNum > $numLetters) {
        
        $text = mb_substr($text, 0, $numLetters,'UTF-8'); // Обрезаем и работаем со всеми кодировками и указываем исходную кодировку
        $position = mb_strrpos($text, ' ', 'UTF-8'); // Определение позиции последнего пробела. Именно по нему и разделяем слова
        $text = mb_substr($text, 0, $position, 'UTF-8'); // Обрезаем переменную по позиции

        $text .= '... <a class="post-text__more-link" href="#">Читать далее</a>';

    } 

    return $text;
}
