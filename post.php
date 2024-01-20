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

// Проверка существования параметра show_all_comments и скрытых комментариев
$show_all_comments = isset($_GET['show_all_comments']);

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

if (!$post) {
    exit('Пост не существует');
}

// число лайков поста
$arr_num['likes_count'] = get_number($db_link, 'likeit', 'post_id =' . $post_id);

// число комментариев к посту
$arr_num['comments_count'] = get_number($db_link, 'comment', 'post_id =' . $post_id);

// число подписчиков автора поста
$arr_num['followers_count'] = get_number($db_link, 'subscription', 'target_id =' . $post['user_id']);

$followers_word = 'подписчик' . get_noun_plural_form($arr_num['followers_count'], '', 'а', 'ов');

// число постов автора поста
$arr_num['posts_count'] = get_number($db_link, 'post', 'user_id =' . $post['user_id']);

$posts_word = 'публикаци' . get_noun_plural_form($arr_num['posts_count'], 'я', 'и', 'й');

$view_word = 'просмотр' . get_noun_plural_form($post['view_count'], '', 'а', 'ов');

// генерация дат
$post['date_user_interval'] = get_interval ($post['dt_user_registration'], false);
$post['date_user_title'] = date(DATE_FORMAT, strtotime($post['dt_user_registration']));

// получение хештегов
$query = '
    SELECT h_name 
    FROM hashtag 
    INNER JOIN post_hashtag_rel     
        ON post_hashtag_rel.hashtag_id = hashtag.id 
    WHERE post_hashtag_rel.post_id = ' . $post_id;

$hashtags = get_result($db_link, $query, 4);
if ($hashtags) {
    $hashtags = explode(' ', $hashtags[0]);
}
// получение комментариев
$query = '
    SELECT 
        c.id, 
        c.dt_add, 
        c_content, 
        post_id, 
        u.login, 
        u.avatar
    FROM comment AS c
    INNER JOIN user AS u
       ON u.id = c.user_id
    WHERE c.post_id = ' . $post_id . '
    ORDER BY c.dt_add ASC';

$comments = get_result($db_link, $query);

// генерация дат комментариев, запись id и ссылки последнего комментария
if ($comments) {
    foreach ($comments as $key => $comment) {
        $comments[$key]['comment_interval'] = get_interval(date(DATE_FORMAT, strtotime($comment['dt_add'])), true);
        $comments[$key]['comment_date_title'] = $comment['dt_add'];
    }
}

$last_comment_id = $comments ? $comments[array_key_last($comments)]['id'] : 0;
$last_comment_href = $comments ? 'post.php?post_id=' . $post['id'] . '&show_all_comments#last_comment_id_' . $last_comment_id : '#';

if (!$show_all_comments) {
    $comments = array_slice($comments, 0, 2);
}

// выбор подшаблона поста
$post_type = 'templates/post-' . $post['category'] . '.php';

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы
$main_content = include_template('posting.php', [
    'post' => $post,
    'post_type' => $post_type,
    'arr_num' => $arr_num,
    'last_comment_href' => $last_comment_href,
    'view_word' => $view_word,
    'hashtags' => $hashtags,
    'comments' => $comments,
    'last_comment_id' => $last_comment_id,
    'show_all_comments' => $show_all_comments,
    'followers_word' => $followers_word,
    'posts_word' => $posts_word,
]);

$layout_content = include_template('layout.php', [
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
   'page_title' => $post['p_title'],
]);

print($layout_content);

