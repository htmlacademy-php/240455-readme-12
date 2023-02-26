<?php
require_once 'init.php';

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
} else {
    $sql = 'SELECT * FROM category';
    $result = mysqli_query($link, $sql);
    
    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
    
    $sql_post = 'SELECT 
                    p.*, 
                    u.login, 
                    u.avatar,
                    c.category 
                FROM post AS p
                    INNER JOIN user AS u 
                        ON p.user_id = u.id	
                    INNER JOIN category AS c 
                        ON p.category_id = c.id	
                ORDER BY view_count DESC';
    $result_post = mysqli_query($link, $sql_post);

    if ($result_post) {
        $posts = mysqli_fetch_all($result_post, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($link);
        $content = include_template('error.php', ['error' => $error]);
    }
}

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
    'posts' => $posts,      
    'categories' => $categories, 
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'популярное',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);