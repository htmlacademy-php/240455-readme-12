<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов

$query = 'SELECT * FROM category';

$categories = get_result($db_link, $query, 2);

// Фильтрация по выбранному типу контента и сортировки

$categ_chosen = filter_input(INPUT_GET, 'categ_chosen', FILTER_SANITIZE_NUMBER_INT);

if (!$categ_chosen) {
    $categ_chosen = 0; // 0 - все категории
}

$sort_chosen = filter_input(INPUT_GET, 'sort_by', FILTER_SANITIZE_STRING);

if (!$sort_chosen) {
    $sort_chosen = 'popularity'; // по популярности
}

if ($sort_chosen == 'likes') {
    $sort_by = 'likes_count DESC';
} elseif ($sort_chosen == 'date') {
    $sort_by = 'dt_add DESC';
} else {
    $sort_by = 'view_count DESC';
}

//Формирование запроса в зависимости от выбранного типа контента

// $query_select = '
//         SELECT
//             p.*,
//             u.login,
//             u.avatar,
//             c.category,
//             COUNT(l.post_id) AS likes_count,
//             COUNT(com.post_id) AS comments_count';

// $query_from = '
//         FROM post AS p
//             INNER JOIN user AS u
//                 ON p.user_id = u.id
//             INNER JOIN category AS c
//                 ON p.category_id = c.id
//             LEFT JOIN likeit AS l
//                 ON p.id = l.post_id
//             LEFT JOIN comment AS com
//                 ON p.id = com.post_id';

// $query_where = 'WHERE c.id = ' . $categ_chosen;

// $query_group_order = '
//         GROUP BY p.id
//         ORDER BY '. $sort_by;

// $query_limit = 'LIMIT 6';

// $query = 

if ($categ_chosen == 0) {
    $query = '
        SELECT
            p.*,
            u.login,
            u.avatar,
            c.category,
            COUNT(l.post_id) AS likes_count,
            COUNT(com.post_id) AS comments_count
        FROM post AS p
            INNER JOIN user AS u
                ON p.user_id = u.id
            INNER JOIN category AS c
                ON p.category_id = c.id
            LEFT JOIN likeit AS l
                ON p.id = l.post_id
            LEFT JOIN comment AS com
                ON p.id = com.post_id
        GROUP BY p.id
        ORDER BY '. $sort_by .'
        LIMIT 6';
} else {
    $query = '
        SELECT
            p.*,
            u.login,
            u.avatar,
            c.category,
            COUNT(l.post_id) AS likes_count,
            COUNT(com.post_id) AS comments_count
        FROM post AS p
            INNER JOIN user AS u
                ON p.user_id = u.id
            INNER JOIN category AS c
                ON p.category_id = c.id
            LEFT JOIN likeit AS l
                ON p.id = l.post_id
            LEFT JOIN comment AS com
                ON p.id = com.post_id
        WHERE c.id = ' . $categ_chosen . '
        GROUP BY p.id
        ORDER BY ' . $sort_by;
}

$posts = get_result($db_link, $query, 2);

// Генерация дат

foreach ($posts as $key => $post) {
    $posts[$key]['date_title'] = date("d.m.Y H:i", strtotime($post['dt_add']));

    $posts[$key]['date_interval'] = get_interval($post['dt_add']);
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