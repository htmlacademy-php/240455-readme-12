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

    $post = get_result($db_link, $query, 3);
    
    // число лайков поста
    $query = '
        SELECT COUNT(id)
        FROM likeit
        WHERE post_id = ' . $post_id;
    
    $arr_num['likes_count'] = get_result($db_link, $query, 1);
    
    // число комментариев к посту
    $query = '
        SELECT COUNT(id)
        FROM comment
        WHERE post_id = ' . $post_id;
    
    $arr_num['comments_count'] = get_result($db_link, $query, 1);
    
    // число подписчиков автора поста
    $query = '
      SELECT COUNT(id) 
      FROM post 
      WHERE user_id IN 
	   (SELECT user_id 
	   FROM post 
	   WHERE post.id = ' . $post_id .')';

    $arr_num['followers_count'] = get_result($db_link, $query, 1);

    $followers_word = get_noun_plural_form($arr_num['followers_count'], 'подписчик', 'подписчика', 'подписчиков');
    
    // число постов автора поста
    $query = '
        SELECT user_id AS user,
            (SELECT COUNT(*) 
             FROM post 
             WHERE user_id = user)
        FROM post AS p
        WHERE p.id = ' . $post_id;
    
    $arr_num['posts_count'] = get_result($db_link, $query, 1);
    
    $posts_word = get_noun_plural_form($arr_num['posts_count'], 'публикация', 'публикации', 'публикаций');
    
    $view_word = get_noun_plural_form($post['view_count'], 'просмотр', 'просмотра', 'просмотров');
    
    // генерация дат
    $user_registration_interval = get_interval ($post['user_registration'], 1);
    $user_registration_title = date("d.m.Y H:i", strtotime($post['user_registration']));
    
    // получение хештегов
    $query = '
        SELECT h_name 
        FROM hashtag 
        INNER JOIN post_hashtag_rel     
            ON post_hashtag_rel.hashtag_id = hashtag.id 
        WHERE post_hashtag_rel.post_id = ' . $post_id;
    
    $hashtags = get_result($db_link, $query, 2);
  
    // получение комментариев
    $query = '
        SELECT *
        FROM comment AS c
        INNER	JOIN user 
            ON user.id = c.user_id
        WHERE c.post_id = ' . $post_id;
    
    $comments = get_result($db_link, $query, 2);
    
    // генерация дат комментария
    if ($comments) {
        foreach ($comments as $key => $comment) {
            $comments[$key]['comment_interval'] = date("d.m.Y H:i", strtotime($comment['dt_add']));
            $comments[$key]['comment_date_title'] = get_interval($comment['dt_add']);
        }
    }
    if (!$post) {
        exit(mysqli_error());
    }
} else {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

// выбор подшаблона поста

switch ($post['category']) {
case 'photo':
    $post_type = 'templates/post-photo.php';
    break;
case 'video':
    $post_type = 'templates/post-video.php';
    break;
case 'text':
    $post_type = 'templates/post-text.php';
    break;
case 'quote':
    $post_type = 'templates/post-quote.php';
    break;
case 'link':
    $post_type = 'templates/post-link.php';
    break;
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы

$main_content = include_template('post.php', [
    'post' => $post,
    'user_registration_interval' => $user_registration_interval,
    'user_registration_title' => $user_registration_title,
    'view_word' => $view_word,
    'hashtags' => $hashtags,
    'comments' => $comments,
    'arr_num' => $arr_num,
    'followers_word' => $followers_word,
    'posts_word' => $posts_word,
    'post_type' => $post_type,
]);

$layout_content = include_template('layout.php', [
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
   'page_title' => $post['p_title'],
]);

print($layout_content);