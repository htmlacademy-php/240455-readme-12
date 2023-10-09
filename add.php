<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
$query = 'SELECT * FROM category';

$post_types = get_result($db_link, $query); //создание вспомогательных массивов, например $post_types

//объявление пустых массивов
$post_data = []; // массив полученных данных
$errors = []; // массив ошибок

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = ['heading', 'post-text', 'cite-text', 'quote-author', 'post-link', 'video-url']; // формируем список полей, обязательных для заполнения

    //текстовая публикация
    if ($_POST['post-type'] == 1) {
        $arr_options = array(
            'heading' => FILTER_SANITIZE_STRING,
            'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
            'post-type' =>  FILTER_SANITIZE_STRING,
            'post-text' => FILTER_SANITIZE_STRING,
        );
    }
    //цитата
    if ($_POST['post-type'] == 2) {
        $arr_options = array(
            'heading' => FILTER_SANITIZE_STRING,
            'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
            'post-type' =>  FILTER_SANITIZE_STRING,
            'cite-text' => FILTER_SANITIZE_STRING,
            'quote-author' => FILTER_SANITIZE_STRING,
        );
    }
    //картинка
    if ($_POST['post-type'] == 3) {
        $arr_options = array(
            'heading' => FILTER_SANITIZE_STRING,
            'photo-url' => FILTER_SANITIZE_STRING,
            'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        );
    }
    //видео
    if ($_POST['post-type'] == 4) {
        $arr_options = array(
            'heading' => FILTER_SANITIZE_STRING,
            'video-url' => FILTER_SANITIZE_STRING,
            'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        );
    }
    //ссылка
    if ($_POST['post-type'] == 5) {
        $arr_options = array(
            'heading' => FILTER_SANITIZE_STRING,
            'post-link' => FILTER_SANITIZE_STRING,
            'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        );
    }
    
    //Отфильтрованные данные из POST
    $post_data = filter_input_array(INPUT_POST); 

    //Выбранная категория формы
    $post_type_chosen = $post_data['post-type'];
    
    //Добавление тегов в базу
    $tags = array_unique(array_filter(explode(" ", $post_data['tags'])));
    if ($tags) {
        $tagsAmount = count($tags); //количество тегов

        foreach ($tags as $tag) {
            $query = 'INSERT INTO hashtag (h_name) VALUES (?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = [$tag]);
            mysqli_stmt_execute($stmt);
        }
    }

    //Валидация полей формы публикации
    $rules = [
        'post-text' => function() {
            return validateLength('post-text', 70);
        },
        'cite-text' => function() {
            return validateLength('cite-text', 1, 70);
        },
        'post-link' => function() {
            return validateUrl('post-link');
        },
        'video-url' => function() {
            return validateUrl('video-url');
        },
        'video-url' => function() {
            return check_youtube_url(($_POST['video-url']));
        },
    ];
    
    foreach ($post_data as $key => $value) {
        if (in_array($key, $required) && empty($value)) {
            if ($key == 'heading') {
                $errors[$key]['head'] = "Заголовок";
            } elseif ($key == 'post-text') {
                $errors[$key]['head'] = "Текст поста";
            } elseif ($key == 'quote-author') {
                $errors[$key]['head'] = "Автор";
            } elseif ($key == 'cite-text') {
                $errors[$key]['head'] = "Текст цитаты";
            } elseif ($key == 'post-link') {
                $errors[$key]['head'] = "Ссылка";
            } elseif ($key == 'video-url') {
                $errors[$key]['head'] = "Ссылка YouTube";
            } 
            $errors[$key]['description'] = "Поле надо заполнить";
        }

        if (isset($rules[$key]) && !isset($errors[$key])) {
            $rule = $rules[$key];
            $error = $rule();
            
            if (!empty($error)) {
                if ($key != 'video-url') {
                    $errors[$key]['description'] = $rule();
                } elseif ($key == 'video-url' && $error != 1) {
                    $errors[$key]['description'] = $rule();
                }
                if ($key == 'post-text') {
                    $errors[$key]['head'] = "Текст поста";
                } elseif ($key == 'cite-text') {
                    $errors[$key]['head'] = "Текст цитаты";
                } elseif ($key == 'post-link') {
                    $errors[$key]['head'] = "Ссылка";
                } elseif ($key == 'video-url' && $error != 1) {
                    $errors[$key]['head'] = "Ссылка YouTube";
                }
            }
        }
    }
    //Валидация картинки
    if ($_POST['post-type'] == 3) {
        //Валидация выбранной картинки
        if ($_FILES['userpic-file-photo']['name']) {
            $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];
            $file_path = __DIR__ . '/img/uploads/';

            $file_name = $_FILES['userpic-file-photo']['name'];
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            if ($file_type !== "image/jpeg" && $file_type !== "image/jpg" && $file_type !== "image/gif" && $file_type !== "image/png") {
                $errors['file']['head'] = 'Файл фото недопустимого типа';
                $errors['file']['description'] = 'Загрузите картинку в формате png, jpeg, jpg или gif';
            } else {
                move_uploaded_file($tmp_name, $file_path . $file_name);
                $post_data['file'] =  'uploads/' . $file_name;
            }
        //Валидация ссылки с картинкой
        } elseif (!empty($post_data['photo-url'])) {
            if (empty(validateUrl('photo-url'))) {
                $img_url = $post_data['photo-url'];
                $headers = get_headers($img_url, 1);

                if (preg_match("|200|", $headers[0]) && preg_match("/(jpeg|jpg|gif|png)/", $headers['Content-Type'])) {
                    $image = file_get_contents($img_url);
                    
                    if (!$image) {
                        $errors['file']['head'] = 'Нет фото';
                        $errors['file']['description'] = 'Не удалось скачать файл';
                    }

                    $image_name = basename($img_url);
                    
                    file_put_contents(__DIR__ . '/uploads/' . $image_name, $image);
                    $post_data['file'] = 'uploads/' . $image_name;
                } else {
                    $errors['file']['head'] = 'Нет фото';
                    $errors['file']['description'] = 'Ссылка битая или не содержит фото';
                }
            } else {
                $errors['file']['head'] = 'Нет фото';
                $errors['file']['description'] = 'Некорректная ссылка';
            }
        } else {
            $errors['file']['head'] = 'Нет фото';
            $errors['file']['description'] = 'Загрузите файл или укажите ссылку на файл';
        }
    }

    $errors = array_filter($errors);

    //Запись данных
    if (!$errors) {
        $post_data['user_id'] = 3; //пока укажите в качестве ID пользователя любое число
        //текстовая публикация
        if ($post_type_chosen == 1) {
            $query = 'INSERT INTO post (p_title, p_content, user_id, category_id) VALUES (?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['post-text'], $post_data['user_id'], $post_data['post-type']));
        } 
        //цитата
        elseif ($post_type_chosen == 2) {
            $query = 'INSERT INTO post (p_title, p_content, author, user_id, category_id) VALUES (?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['cite-text'], $post_data['quote-author'], $post_data['user_id'], $post_data['post-type']));
        }
        //картинка
        elseif ($post_type_chosen == 3) {
            $query = 'INSERT INTO post (p_title, p_img, user_id, category_id) VALUES (?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['file'], $post_data['user_id'], $post_data['post-type']));
        }
        //видео
        elseif ($post_type_chosen == 4) {
            $query = 'INSERT INTO post (p_title, p_video, user_id, category_id) VALUES (?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['video-url'], $post_data['user_id'], $post_data['post-type']));
        }
        //ссылка
        elseif ($post_type_chosen == 5) {
            $query = 'INSERT INTO post (p_title, p_content, p_link, user_id, category_id) VALUES (?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['post-link'], $post_data['post-link'], $post_data['user_id'], $post_data['post-type']));
        }
        mysqli_stmt_execute($stmt);
        
        // вывод поста
        $post_id = mysqli_insert_id($db_link);
        header("Location: /post.php?post_id=" . $post_id);

        //запрос id тегов
        $query = "SELECT id FROM hashtag ORDER BY id DESC LIMIT " . $tagsAmount;
        $tags_id = get_result($db_link, $query, 4);
        
        //добавление связей тегов и поста в post_hashtag_rel
        foreach ($tags_id as $tag_id) {
            $query = 'INSERT INTO post_hashtag_rel (post_id, hashtag_id) VALUES (?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = [$post_id, $tag_id]);
            mysqli_stmt_execute($stmt);
        }
    }
    
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
    // Открытие таба по выбранному типу контента
    $post_type_chosen = filter_input(INPUT_GET, 'post_type_chosen', FILTER_SANITIZE_NUMBER_INT);
    $post_type_chosen = (int) $post_type_chosen; 
}

if ($post_type_chosen == 0) {
    $post_type_chosen = 1; // публикация с текстом по умолчанию
} 

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы
$main_content = include_template('adding-post.php', [
    'post_types' => $post_types,
    'post_type_chosen' => $post_type_chosen,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'добавление публикации',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);