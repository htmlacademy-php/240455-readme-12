<?php

require_once 'helpers.php';
require_once 'functions.php';

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

$posts = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'author' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'author' => 'Владик',
        'avatar' => 'userpic.jpg',
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'author' => 'Виктор',
        'avatar' => 'userpic-mark.jpg',
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'author' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'author' => 'Владик',
        'avatar' => 'userpic.jpg',
    ],
];

// Генерация псевдодат

foreach ($posts as $key => $post) {
    $posts[$key]['date'] = generate_random_date($key);
    
    $posts[$key]['date_title'] = date("d.m.Y H:i", strtotime(generate_random_date($key)));
    
    $posts[$key]['date_interval'] = get_interval(generate_random_date($key));
}

// Очистка от XSS

array_walk_recursive($posts, 'filter_xss');

// Подготовка и вывод страницы

$main_content = include_template('main.php', [
    'posts' => $posts,                                       
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'популярное',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);