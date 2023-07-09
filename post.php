<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Проверка существования параметра запроса с ID поста
$post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
$post_id = (int) $post_id;
if ($post_id === 0) {
    exit('Пост не существует');
}

// Проверка существования параметра show_comments и скрытых комментариев
$show_comments = filter_input(INPUT_GET, 'show_comments', FILTER_SANITIZE_NUMBER_INT);

$query = '
    SELECT      
        p.*,
        u.login,
        u.avatar,
        u.dt_add AS dt_user_registration,
        u.id AS user_id,
        c.category
    FROM post AS p
        INNER JOIN user AS u
            ON p.user_id = u.id
        INNER JOIN category AS c
            ON p.category_id = c.id
    WHERE p.id = ' . $post_id;

$post = get_result($db_link, $query, 3);

if(!$post) {
    exit('Пост не существует');
}

// число лайков поста
$arr_num['likes_count'] = get_number($db_link, 'likeit', 'post_id =' . $post_id);

// число комментариев к посту
$arr_num['comments_count'] = get_number($db_link, 'comment', 'post_id =' . $post_id);

// число подписчиков автора поста
$arr_num['followers_count'] = get_number($db_link, 'subscription', 'target_id =' . $post['user_id']);

$followers_word = get_noun_plural_form($arr_num['followers_count'], 'подписчик', 'подписчика', 'подписчиков');

// число постов автора поста
$arr_num['posts_count'] = get_number($db_link, 'post', 'user_id =' . $post['user_id']);

$posts_word = get_noun_plural_form($arr_num['posts_count'], 'публикация', 'публикации', 'публикаций');

$view_word = get_noun_plural_form($post['view_count'], 'просмотр', 'просмотра', 'просмотров');

// генерация дат
$post['date_user_interval'] = get_interval ($post['dt_user_registration'], 1);
$post['date_user_title'] = date(DATE_FORMAT, strtotime($post['dt_user_registration']));

// получение хештегов
$query = '
    SELECT h_name 
    FROM hashtag 
    INNER JOIN post_hashtag_rel     
        ON post_hashtag_rel.hashtag_id = hashtag.id 
    WHERE post_hashtag_rel.post_id = ' . $post_id;

$hashtags = get_result($db_link, $query, 4);

// получение комментариев
$comment_condition = ' LIMIT 2';  //условие для ограничения количества выводимых комментариев

if (isset($_GET['show_comments'])) {
    $comment_condition = '';
}

$query = '
    SELECT c.id, c.dt_add, c_content, post_id, u.login, u.avatar
    FROM comment AS c
    INNER JOIN user AS u
       ON u.id = c.user_id
    WHERE c.post_id = ' . $post_id . '
    ORDER BY c.dt_add ASC' . 
    $comment_condition;

$comments = get_result($db_link, $query);

// генерация дат и номера комментария
$i = 1;

if ($comments) {
    foreach ($comments as $key => $comment) {

        $comments[$key]['comment_interval'] = date(DATE_FORMAT, strtotime($comment['dt_add']));

        $comments[$key]['comment_date_title'] = get_interval($comment['dt_add']);

        $comments[$key]['comment_number'] = $i++;
    }
}

// выбор подшаблона поста
$post_type = 'templates/post-' . $post['category'] . '.php';

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы
$main_content = include_template('posting.php', [
    'post' => $post,
    'view_word' => $view_word,
    'hashtags' => $hashtags,
    'comments' => $comments,
    'arr_num' => $arr_num,
    'followers_word' => $followers_word,
    'posts_word' => $posts_word,
    'post_type' => $post_type,
    'show_comments' => $show_comments,
]);

$layout_content = include_template('layout.php', [
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
   'page_title' => $post['p_title'],
]);

print($layout_content);