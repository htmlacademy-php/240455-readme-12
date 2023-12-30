<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
$categories = get_сategories($db_link);

// Фильтрация по выбранному типу контента и сортировки
$categ_chosen = filter_input(INPUT_GET, 'categ_chosen', FILTER_SANITIZE_NUMBER_INT);
$categ_chosen = (int) $categ_chosen; // 0 - все категории
if ($categ_chosen) {
    $categ_condition = 'WHERE c.id = ' . $categ_chosen;
} else {
    $categ_condition = '';
}

$sort_chosen = filter_input(INPUT_GET, 'sort_by', FILTER_SANITIZE_STRING);

if (!$sort_chosen) {
    $sort_chosen = 'popularity'; // по популярности
}

if ($sort_chosen === 'likes') {
    $sort_by = 'likes_count DESC';
} elseif ($sort_chosen === 'date') {
    $sort_by = 'dt_add DESC';
} else {
    $sort_by = 'view_count DESC';
}

//Формирование запроса в зависимости от выбранного типа контента
$query = '
    SELECT
        p.*,
        u.login,
        u.avatar,
        c.category,
        (SELECT COUNT(id) FROM likeit WHERE post_id = p.id) AS likes_count,
        (SELECT COUNT(id) FROM comment WHERE post_id = p.id) AS comments_count
    FROM post AS p
        INNER JOIN user AS u
            ON p.user_id = u.id
        INNER JOIN category AS c
            ON p.category_id = c.id
    ' . $categ_condition . '
    ORDER BY ' . $sort_by;


$posts = get_result($db_link, $query);

// Генерация дат
foreach ($posts as $key => $post) {  
    $posts[$key]['date_interval'] = get_interval ($post['dt_add'], $not_ago = TRUE);
    $posts[$key]['date_title'] = date(DATE_FORMAT, strtotime($post['dt_add']));
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Очистка от XSS
array_walk_recursive($posts, 'filter_xss');

// Подготовка и вывод страницы
$main_content = include_template('main.php', [
    'categories' => $categories,
    'posts' => $posts,      
    'categ_chosen' => $categ_chosen,
    'sort_chosen' => $sort_chosen,
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'популярное',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);
