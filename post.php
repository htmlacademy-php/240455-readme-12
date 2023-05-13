<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Проверка существования параметра запроса с ID поста

$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

if ($post_id !== 0) {
    $query = '
        SELECT      
            p.*,
            u.login,
            u.avatar,
            u.dt_add AS user_registration,
            c.*
        FROM post AS p
            INNER JOIN user AS u
                ON p.user_id = u.id
            INNER JOIN category AS c
                ON p.category_id = c.id
        WHERE p.id = ' . $post_id . ' 
        GROUP BY p.id';

    $post = get_result($db_link, $query, ['one_row' => 1]);
    
    // число лайков поста
    $query = '
        SELECT COUNT(id)
        FROM likeit
        WHERE post_id = ' . $post_id;
    
    $arr_num['likes_count'] = get_result($db_link, $query, ['one_value' => 1]);
    
    // число комментариев к посту
    $query = '
        SELECT COUNT(id)
        FROM comment
        WHERE post_id = ' . $post_id;
    
    $arr_num['comments_count'] = get_result($db_link, $query, ['one_value' => 1]);
    
    // число подписчиков автора поста
    $query = '
      SELECT COUNT(id) 
      FROM post 
      WHERE user_id IN 
	   (SELECT user_id 
	   FROM post 
	   WHERE post.id = ' . $post_id .')';

    $arr_num['followers_count'] = get_result($db_link, $query, ['one_value' => 1]);

    $followers_word = get_noun_plural_form($arr_num['followers_count'], 'подписчик', 'подписчика', 'подписчиков');
    
    // число постов автора поста
    $query = '
        SELECT user_id AS user,
            (SELECT COUNT(*) 
             FROM post 
             WHERE user_id = user)
        FROM post AS p
        WHERE p.id = ' . $post_id;
    
    $arr_num['posts_count'] = get_result($db_link, $query, ['one_value' => 1]);
    
    $posts_word = get_noun_plural_form($arr_num['posts_count'], 'публикация', 'публикации', 'публикаций');
    
    $view_word = get_noun_plural_form($post['view_count'], 'просмотр', 'просмотра', 'просмотров');
    
//     foreach ($post as $item):
//         $post_title =  $item['p_title'];
//         $youtube_url = $item['p_video'];
//         $url = $item['p_link'];
//         $img_url = $item['p_img'];
//         $text = $item['p_content'];
//         $author = $item['author'];
//         $category = $item['category'];
//         $title = $item['p_title'];
//         $login = $item['login'];
//         $avatar = $item['avatar'];
//         $view_count = $item['view_count'];
        $user_registration_interval = get_interval ($post['user_registration'], 1);
        $user_registration_title = date("d.m.Y H:i", strtotime($post['user_registration']));
//         $view_word = get_noun_plural_form($view_count, 'просмотр', 'просмотра', 'просмотров');
//     endforeach;
    
    $query = '
        SELECT h_name 
        FROM hashtag 
        INNER JOIN post_hashtag_rel     
            ON post_hashtag_rel.hashtag_id = hashtag.id 
        WHERE post_hashtag_rel.post_id = ' . $post_id;
    
    $hashtags = get_result($db_link, $query);
    
    $query = '
        SELECT *
        FROM comment AS c
        INNER	JOIN user 
            ON user.id = c.user_id
        WHERE c.post_id = ' . $post_id;
    
    $comments = get_result($db_link, $query);
     
    foreach ($comments as $key => $comment) {
        $comments[$key]['comment_interval'] = date("d.m.Y H:i", strtotime($comment['dt_add']));
        $comments[$key]['comment_date_title'] = get_interval($comment['dt_add']);
    }
    
    if (!$post) {
        exit(mysqli_error());
    }
} else {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы

$main_content = include_template('post.php', [
    'post' => $post,
//     'post_title' => $post_title,
//     'title' => $title,
//     'youtube_url' => $youtube_url,
//     'url' => $url,
//     'img_url' => $img_url,
//     'text' => $text,
//     'author' => $author,
//     'category' => $category,
//     'login' => $login,
//     'avatar' => $avatar,
    'user_registration_interval' => $user_registration_interval,
    'user_registration_title' => $user_registration_title,
    'view_word' => $view_word,
    'hashtags' => $hashtags,
    'comments' => $comments,
    'arr_num' => $arr_num,
    'followers_word' => $followers_word,
    'posts_word' => $posts_word,
    'post_block' => $post_block,
]);

$layout_content = include_template('layout.php', [
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
   'page_title' => $post['p_title'],
]);

print($layout_content);