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
            COUNT(l.post_id) AS likes_count,
            COUNT(s.target_id) AS subscription_count,
            COUNT(com.post_id) AS comments_count,
            (SELECT COUNT(*) FROM post WHERE user_id = u.id) AS post_count
        FROM post AS p
            LEFT JOIN user AS u
                ON p.user_id = u.id
            LEFT JOIN subscription AS s
                ON u.id = s.target_id
            LEFT JOIN likeit AS l
                ON p.id = l.post_id
            LEFT JOIN comment AS com
                ON p.id = com.post_id
            INNER JOIN category AS c
                ON p.category_id = c.id
        WHERE p.id = ' . $post_id . ' GROUP BY p.id';
    
    $post = get_result($link, $query);
    
    foreach ($post as $item):
        $post_title =  $item['p_title'];
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
        $post_count = $item['post_count'];
        $likes = $item['likes_count'];
        $comments_count = $item['comments_count'];
        $view_count = $item['view_count'];
        $user_registration_interval = get_interval ($item['user_registration'], 1);
        $user_registration_title = date("d.m.Y H:i", strtotime($item['user_registration']));
        $followers_word = get_noun_plural_form($followers, 'подписчик', 'подписчика', 'подписчиков');
        $posts_word = get_noun_plural_form($post_count, 'публикация', 'публикации', 'публикаций');
        $view_word = get_noun_plural_form($view_count, 'просмотр', 'просмотра', 'просмотров');
    endforeach;
    
    $query = 'SELECT * 
              FROM hashtag 
              INNER JOIN post_hashtag_rel ON post_hashtag_rel.hashtag_id = hashtag.id 
              WHERE post_hashtag_rel.post_id = ' . $post_id . '';
    
    $hashtags = get_result($link, $query);
    
    $query = 'SELECT *
              FROM comment AS c
              INNER	JOIN	user ON user.id = c.user_id
              WHERE c.post_id = ' . $post_id . '';
    
    $comments = get_result($link, $query);
     
    foreach ($comments as $key => $comment) {
        $comments[$key]['comment_interval'] = date("d.m.Y H:i", strtotime($comment['dt_add']));
        $comments[$key]['comment_date_title'] = get_interval($comment['dt_add']);
    }
    
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
    'post_title' => $post_title,
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
    'post_count' => $post_count,
    'user_registration_interval' => $user_registration_interval,
    'user_registration_title' => $user_registration_title,
    'followers_word' => $followers_word,
    'posts_word' => $posts_word,
    'likes' => $likes,
    'comments_count' => $comments_count,
    'view_count' => $view_count,
    'view_word' => $view_word,
    'hashtags' => $hashtags,
    'comments' => $comments,
]);

$layout_content = include_template('layout.php', [
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
   'page_title' => $post_title,
]);

print($layout_content);