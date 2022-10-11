<?php

function cut_text ($text, $numLetters = 300) {

    $textNum = mb_strlen($text);

    if($textNum > $numLetters) {

        $words = explode(" ", $text);
        $text = [];

        foreach ($words as $word) {  

            $num = mb_strlen($word);
            $numSum += $num;

            if ($numSum <= $numLetters) {
                $text[] = $word; 
            } else {
                break;
            }
        }

        $text = implode(" ", $text);
        $text .= '... <a class="post-text__more-link" href="#">Читать далее</a>';

    } else {
        $text;
    }

    return $text;
}

?>