<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'db.php';

// Подключение к базе

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if ($link == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($link, "utf8");

// Проверка существования параметра запроса с ID поста

$post_id = filter_input(INPUT_GET, 'id');

if (isset($post_id)) {
    $query = '
        SELECT      
            p.*,
            u.login,
            u.avatar,
            u.dt_add AS user_registration,
            c.*, 
            COUNT(s.target_id) AS subscription_count,
            (SELECT COUNT(*) FROM post WHERE user_id = u.id) AS post_count
        FROM post AS p
            LEFT JOIN user AS u
                ON p.user_id = u.id
            LEFT JOIN subscription AS s
                ON u.id = s.target_id
            INNER JOIN category AS c
                ON p.category_id = c.id
        WHERE p.id = ' . $post_id . ' GROUP BY p.id';
    
    $post = get_result($link, $query);
    
    foreach ($post as $item):
        $page_title =  $item['p_title'];
        $youtube_url = $item['p_video'];
        $url = $item['p_link'];
        $img_url = $item['p_img'];
        $text = $item['p_content'];
        $author = $item['author'];
        $category = $item['category'];
        $title = $item['p_title'];
        $login = $item['login'];
        $avatar = $item['avatar'];
        $followers = $item['subscription_count'];
        $posts = $item['post_count'];
        $user_registration_interval = get_interval ($item['user_registration'], 1);
        $user_registration_title = date("d.m.Y H:i", strtotime($item['user_registration']));
        $followers_word = get_noun_plural_form($followers, 'подписчик', 'подписчика', 'подписчиков');
        $posts_word = get_noun_plural_form($posts, 'публикация', 'публикации', 'публикаций');
    endforeach;
    
    if (!$post) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
} else {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы

$main_content = include_template('post.php', [
    'post' => $post,
    'page_title' => $page_title,
    'title' => $title,
    'youtube_url' => $youtube_url,
    'url' => $url,
    'img_url' => $img_url,
    'text' => $text,
    'author' => $author,
    'category' => $category,
    'login' => $login,
    'avatar' => $avatar,
    'followers' => $followers,
    'posts' => $posts,
    'user_registration_interval' => $user_registration_interval,
    'user_registration_title' => $user_registration_title,
    'followers_word' => $followers_word,
    'posts_word' => $posts_word,
]);

$layout_content = include_template('layout.php', [
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);