<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'db.php';

// Подключение к базе

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if ($link == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
else {
    mysqli_set_charset($link, "utf8");
    
    // выполнение запросов
    
    $query = 'SELECT * FROM category';
    
    $categories = create_result($link, $query);

    $query = 'SELECT 
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
    
    $posts = create_result($link, $query);
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
    'categories' => $categories,
    'posts' => $posts,      
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'популярное',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);