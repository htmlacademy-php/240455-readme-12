<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
//объявление пустых массивов
$post_data = []; // массив полученных данных
$errors = []; // массив ошибок

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    
    $arr_keys = array_keys($_POST); //Получаем ключи массива $_POST
    
    $arr_options = array_fill_keys($arr_keys, FILTER_SANITIZE_STRING); //Создаем фильтры для ключей
    
    $post_data = filter_input_array(INPUT_POST, $arr_options); //Отфильтрованные данные из POST
    
    $category_chosen = $post_data['category']; //Выбранная категория формы
    
    //id категории для таблицы post
    $query =  'SELECT id FROM category WHERE category = "' . $category_chosen . '"';
    $category_id = (int)get_result($db_link, $query, 1); 

    //Проверка на обязательность заполнения определённых полей формы
    $required = [ //Формируем список полей, обязательных для заполнения
        'heading' => "Заголовок",
        'p_text' => "Текст",
        'author' => "Автор",
        'url' => "Ссылка",
    ];
   
    foreach ($post_data as $key => $value) {
        if (array_key_exists($key, $required) && empty($value)) { //проверка обязательного поля на заполненность
            $errors[$key]['head'] = $required[$key];
            $errors[$key]['description'] = "Поле надо заполнить";
        }
    }

    //Валидация URL
    if (array_key_exists('url', $post_data) && !array_key_exists('url', $errors)) {
        url_check($post_data['url'], $category_chosen, $errors);
    };
  
    //Валидация Фото по выбранному фото или ссылке на него
    if ($category_chosen === 'photo') {
        $file_path = 'uploads/';
        // Если загружен файл и нет ошибок сохраняем его в папку UPLOAD_PATH_IMG
        if ($_FILES['userpic-file-photo']['error'] === UPLOAD_ERR_OK) { 
            
            $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];
            
            $mime_type = mime_content_type($tmp_name); //mime тип
            $mime_type_ext = str_replace("image/", "", $mime_type);

            
            if (in_array($mime_type_ext, ALLOW_EXT)) {

                $file_name =  strtolower(uniqid() . $_FILES['userpic-file-photo']['name']);
                move_uploaded_file($tmp_name, $file_path . $file_name);
                
            } else {
                $errors['file']['head'] = 'Файл фото недопустимого типа';
                $errors['file']['description'] = 'Загрузите картинку в формате png, jpeg, jpg или gif';
            }
        // Если есть интернет-ссылка и нет ошибок проверки ссылки, скачиваем файл и сохраняем в папку uploads
        } elseif ($post_data['url_photo'] and !isset($errors['file'])) { 
            if (validate_url($post_data['url_photo'])) {
                if (is_url_exist($post_data['url_photo'])) {
                    $file_info = new finfo(FILEINFO_MIME_TYPE);
                    $image = file_get_contents($post_data['url_photo']);
                    
                    $mime_type = $file_info->buffer($image);
                    $mime_type_ext = str_replace("image/", "", $mime_type);
 
                    if (in_array($mime_type_ext, ALLOW_EXT)) {
                        
                        $file_name =  uniqid() . '.' . $mime_type_ext;
                        file_put_contents($file_path . $file_name, $image);
                        
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
            $post_data['p_text'] ?: '',
            $post_data['author'] ?: '',
            $file_name ?? '',
            ($category_chosen === 'video') ? ($post_data['url'] ?: '') : '',
            ($category_chosen !== 'video') ? ($post_data['url'] ?: '') : '',
            $post_data['user_id'] ?? 3,
            $category_id ?? 0,
        ];
        
        $query = 'INSERT INTO post (p_title, p_content, author, p_img, p_video, p_link, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($db_link, $query, $data);

        mysqli_stmt_execute($stmt);
        
        //Получение id сохраненного поста
        $post_id = mysqli_insert_id($db_link);
        
        //Валидация и добавление тегов в базу
        if ($post_data['tags'] != '') {
            $tags = hash_tags2arr($post_data['tags']);
            $query =  'SELECT h_name FROM hashtag';
            $all_tags = get_result($db_link, $query, 4); //Теги из БД
            foreach ($tags as $tag) {
                if (!in_array($tag, $all_tags)) { //Проверяем наличие конкретного хештега в БД
                    $query = 'INSERT INTO hashtag (h_name) VALUES (?)'; //Если тега нет, то добавляем в таблицу хештегов
                    $stmt = db_get_prepare_stmt($db_link, $query, $data = [$tag]);
                    mysqli_stmt_execute($stmt);
                    
                    $tag_id = mysqli_insert_id($db_link); //Получаем id добаленного тега для внесения в таблицу связи хештегов
                } else {
                    $query =  'SELECT id FROM hashtag WHERE h_name = "' . $tag . '"';
                    $tag_id = get_result($db_link, $query, 1); //Получаем id существующего тега для внесения в таблицу связи хештегов
                }
                
                // Вносим данные в таблицу связи хештегов
                $query = 'INSERT INTO post_hashtag_rel (post_id, hashtag_id) VALUES (?, ?)';
                $stmt = db_get_prepare_stmt($db_link, $query, $data = [$post_id, $tag_id]);
                mysqli_stmt_execute($stmt);
            }
        }
        
        //Вывод поста
        header("Location: /post.php?post_id=" . $post_id);
        exit;
    }
    
} else { 
    //Открытие таба по выбранному типу контента
    $category_chosen = filter_input(INPUT_GET, 'category_chosen', FILTER_SANITIZE_STRING);
    if ($category_chosen == '') {
        $category_chosen = 'text'; // публикация с текстом по умолчанию
    }
}

$tags = '';

if (isset($post_data['tags'])) {
    $tags = $post_data['tags'];
} 

$form_tags = include_template('form-tags.php', [
    'tags' => $tags,
]);

$form_invalid_block = '';

if ($errors) {
    $form_invalid_block = include_template('form_invalid_block.php', [
        'errors' => $errors
    ]);
}

$form_buttons = include_template('form_buttons.php');

$categories = get_сategories($db_link);

$is_auth = rand(0, 1);

$user_name = 'Никитина Виктория';

//Подготовка и вывод страницы
$main_content = include_template('adding-post.php', [
    'categories' => $categories,
    'category_chosen' => $category_chosen,
    'errors' => $errors,
    'form_buttons' => $form_buttons,
    'form_invalid_block' => $form_invalid_block,
    'form_tags' => $form_tags,
    'post_data' => $post_data,
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'добавление публикации',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);