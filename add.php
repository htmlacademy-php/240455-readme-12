<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
$categories = getCategories($db_link);

//объявление пустых массивов
$post_data = []; // массив полученных данных
$errors = []; // массив ошибок

// разрешенные типы фото
define('ALLOW_EXT', array(
    'png',
    'jpeg',
    'jpg',
    'gif',
));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    //Записываем ключи массива $_POST
    $arr_keys = array_keys($_POST);
    //Создаем фильтры для ключей
    $arr_options = array_fill_keys($arr_keys, FILTER_SANITIZE_STRING);
    //Отфильтрованные данные из POST
    $post_data = filter_input_array(INPUT_POST, $arr_options); 
    //Выбранная категория формы
    $category_chosen = $post_data['category'];
    //id категории для таблицы post
    $category_id = $post_data['category_id'];

    //Валидация полей формы публикации
    //Формируем правила для валидации
    $rules = [
        'post-text' => function($value) {
            return validateLength($value, 70);
        },
        'post-link' => function($value) {
            if (!validateUrl($value)) {
                return "Укажите корректную ссылку";
            } elseif (!is_url_exist($value)) {
                return "Страница не найдена";
            }
        },
        'video-url' => function($value) {
            if (!validateUrl($value)) {
                return "Укажите корректную ссылку";
            } else {
                if (check_youtube_url($value) != 1) {
                    return check_youtube_url($value);
                }
            }
        },
    ];
    
    //Формируем список полей, обязательных для заполнения
    $required = [
        'heading' => "Заголовок",
        'post-text' => "Текст поста",
        'cite-text' => "Текст цитаты",
        'quote-author' => "Автор",
        'post-link' => "Ссылка",
        'video-url' => "Ссылка YouTube",
    ];
    
    foreach ($post_data as $key => $value) {
        if (array_key_exists($key, $required)) {
            if (!validateFilled($value)) {
                $errors[$key]['head'] = $required[$key];
                $errors[$key]['description'] = "Поле надо заполнить";
            } else {
                if (isset($rules[$key])) {
                    $rule = $rules[$key];
                    $error = $rule($value);
                    if ($error) {
                        $errors[$key]['head'] = $required[$key];
                        $errors[$key]['description'] = $rule($value);
                    }
                }
            }
        }
    }
  
    //Валидация картинки
    if ($category_chosen === 'photo') {
        $file_path = __DIR__ . '/uploads/';
        // Если загружен файл и нет ошибок сохраняем его в папку UPLOAD_PATH_IMG
        if ($_FILES['userpic-file-photo']['error'] === UPLOAD_ERR_OK) { 
            
            $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];
            
            $mime_type = mime_content_type($tmp_name); //mime тип
            $mime_type_ext = str_replace("image/", "", $mime_type);

            
            if (in_array($mime_type_ext, ALLOW_EXT)) {

                $file_name =  strtolower(uniqid() . $_FILES['userpic-file-photo']['name']);
                move_uploaded_file($tmp_name, $file_path . $file_name);
                
                $post_data['file'] =  'uploads/' . $file_name;
            } else {
                $errors['file']['head'] = 'Файл фото недопустимого типа';
                $errors['file']['description'] = 'Загрузите картинку в формате png, jpeg, jpg или gif';
            }
        // Если есть интернет-ссылка и нет ошибок проверки ссылки, скачиваем файл и сохраняем в папку UPLOAD_PATH_IMG
        } elseif ($post_data['photo-url'] and !isset($errors['file'])) { 
            if (validateUrl($post_data['photo-url'])) {
                if (is_url_exist($post_data['photo-url'])) {
                    $file_info = new finfo(FILEINFO_MIME_TYPE);
                    $image = file_get_contents($post_data['photo-url']);
                    
                    $mime_type = $file_info->buffer($image);
                    $mime_type_ext = str_replace("image/", "", $mime_type);
 
                    if (in_array($mime_type_ext, ALLOW_EXT)) {
                        
                        $file_name =  uniqid() . '.' . $mime_type_ext;
                        file_put_contents($file_path . $file_name, $image);
                        
                        $post_data['file'] =  'uploads/' . $file_name;
                    } else {
                        $errors['file']['head'] = 'Файл фото недопустимого типа';
                        $errors['file']['description'] = 'Загрузите картинку в формате png, jpeg, jpg или gif';
                    }
                } else {
                    $errors['file']['head'] = 'Нет фото';
                    $errors['file']['description'] = 'Ссылка не существует или не содержит фото';
                }
            } else {
                $errors['file']['head'] = 'Нет фото';
                $errors['file']['description'] = 'Укажите корректную ссылку';
            }
        } else { // нет фото
            $errors['file']['head'] = 'Нет фото';
            $errors['file']['description'] = 'Загрузите файл или укажите ссылку на файл';
        }
    }

    $errors = array_filter($errors);

    // если нет ошибок, то запись данных и переход на страницу просмотра поста (post.php)
    if (!$errors) {
        //пока укажите в качестве ID пользователя любое число
        $post_data['user_id'] = 3; 
        
        $data = [
            $post_data['heading'] ?: '',
            $post_data['post-text'] ?: '',
            $post_data['quote-author'] ?: '',
            $post_data['file'] ?: '',
            $post_data['video-url'] ?: '',
            $post_data['p_link'] ?: '',
            $post_data['user_id'] ?? 3,
            $category_id ?? 0,
        ];
        
        $query = 'INSERT INTO post (p_title, p_content, author, p_img, p_video, p_link, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($db_link, $query, $data);

        mysqli_stmt_execute($stmt);
        
        // Извлечение id поста
        $post_id = mysqli_insert_id($db_link);
        
        //Добавление тегов в базу
        $tags = hash_tags2arr($post_data['tags']);
        
        if ($tags) {
            $query =  'SELECT h_name FROM hashtag';
            $all_tags = get_result($db_link, $query, 4);
            foreach ($tags as $tag) {
                if (!in_array($tag, $all_tags)) {
                    $query = 'INSERT INTO hashtag (h_name) VALUES (?)';
                    $stmt = db_get_prepare_stmt($db_link, $query, $data = [$tag]);
                    mysqli_stmt_execute($stmt);
                    $tag_id = mysqli_insert_id($db_link);            
                } else {
                    $query =  'SELECT id FROM hashtag WHERE h_name = "' . $tag . '"';
                    $tag_id = get_result($db_link, $query, 1);
                }
                $query = 'INSERT INTO post_hashtag_rel (post_id, hashtag_id) VALUES (?, ?)';
                $stmt = db_get_prepare_stmt($db_link, $query, $data = [$post_id, $tag_id]);
                mysqli_stmt_execute($stmt);
            }
        }
        
        // вывод поста
        header("Location: /post.php?post_id=" . $post_id);
        exit();
    }
    
} else { 
    // Открытие таба по выбранному типу контента
    $category_chosen = filter_input(INPUT_GET, 'category_chosen', FILTER_SANITIZE_STRING);
    
    if ($category_chosen == '') {
        $category_chosen = 'text'; // публикация с текстом по умолчанию
    }
}

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

// Подготовка и вывод страницы
$main_content = include_template('adding-post.php', [
    'categories' => $categories,
    'category_chosen' => $category_chosen,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'добавление публикации',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);