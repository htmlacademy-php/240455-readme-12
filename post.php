<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Проверка существования параметра запроса с ID поста

$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

if ($post_id > 0) {
    $query = '
        SELECT      
            p.*,
            u.login,
            u.avatar,
            u.dt_add AS dt_user_registration,
            c.category
        FROM post AS p
            INNER JOIN user AS u
                ON p.user_id = u.id
            INNER JOIN category AS c
                ON p.category_id = c.id
        WHERE p.id = ' . $post_id;

    $post = get_result($db_link, $query, 3);
    
    // число лайков поста

    $query = get_number('likeit', 'post_id', $post_id);
    
    $arr_num['likes_count'] = get_result($db_link, $query, 1);
    
    // число комментариев к посту

    $query = get_number('comment', 'post_id', $post_id);
    
    $arr_num['comments_count'] = get_result($db_link, $query, 1);
    
    // число подписчиков автора поста
    $query = '
      SELECT COUNT(id) 
      FROM subscription 
      WHERE target_id IN 
          (SELECT user_id 
          FROM post 
          WHERE post.id = ' . $post_id .')';

    $arr_num['followers_count'] = get_result($db_link, $query, 1);

    $followers_word = get_noun_plural_form($arr_num['followers_count'], 'подписчик', 'подписчика', 'подписчиков');
    
    // число постов автора поста
    $query = '
        SELECT COUNT(user_id) 
        FROM post 
        WHERE user_id IN 
            (SELECT user_id 
            FROM post AS p
            WHERE p.id = ' . $post_id .')';

    $arr_num['posts_count'] = get_result($db_link, $query, 1);
    
    $posts_word = get_noun_plural_form($arr_num['posts_count'], 'публикация', 'публикации', 'публикаций');
    
    $view_word = get_noun_plural_form($post['view_count'], 'просмотр', 'просмотра', 'просмотров');
    
    // генерация дат
    $post = add_elements($post, '', 'dt_user_registration', 'date_user_interval', 'date_user_title', $not_ago = 1);
    
    // получение хештегов
    $query = '
        SELECT h_name 
        FROM hashtag 
        INNER JOIN post_hashtag_rel     
            ON post_hashtag_rel.hashtag_id = hashtag.id 
        WHERE post_hashtag_rel.post_id = ' . $post_id;
    
    $hashtags = get_result($db_link, $query, 4);
  
    // получение комментариев
    
    $comment_condition = ' LIMIT 1';  //условие для ограничения количества выводимых комментариев
    
    if (isset($_GET['show_comments'])) {
        $comment_condition = '';
    }
    
    $query = '
        SELECT c.id, c.dt_add, c_content, post_id, u.login, u.avatar
        FROM comment AS c
        INNER JOIN user AS u
           ON u.id = c.user_id
        WHERE c.post_id = ' . $post_id . '
        ORDER BY c.dt_add ASC' . $comment_condition;

    $comments = get_result($db_link, $query, 2);
    
    // генерация дат и номера комментария
    $i = 1;
    if ($comments) {
        foreach ($comments as $key => $comment) {
            $comments[$key]['count'] = $i++;
        }
        
        $comments = add_elements($comments, '$comment', 'dt_add', 'date_interval', 'date_title');
    }
    
    
    // выбор подшаблона поста
    
    $post_type = 'templates/post-' . $post['category'] . '.php';
    
} else {
    exit('Пост не существует');
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы

$main_content = include_template('post.php', [
    'post' => $post,
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