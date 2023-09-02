<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
$query = 'SELECT * FROM category';

$categories = get_result($db_link, $query);

// Фильтрация по выбранному типу контента
$categ_chosen = filter_input(INPUT_GET, 'categ_chosen', FILTER_SANITIZE_NUMBER_INT);
$categ_chosen = (int) $categ_chosen; 

if (!$categ_chosen) {
    $categ_chosen = 1; // публикация с текстом по умолчанию
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    print_r($_POST);
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы
$main_content = include_template('adding-post.php', [
    'categories' => $categories,
    'categ_chosen' => $categ_chosen,
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'добавление публикации',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);