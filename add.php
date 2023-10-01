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

// будут накапливаться все ошибки по типу поста $errors['url'], $errors['photo']
//По такому принципу $errors['photo'] = [ 'head' => 'Фото', 'description' => 'Файл фото недопустимого типа.', ];

//учесть $allow_ext = ['png', 'jpeg', 'jpg', 'gif']; // разрешенные типы фото

// если нет ошибок, то запись данных и переход на страницу просмотра поста (post.php) if (!$errors) {
// данные записываем через подготовленное выражение
//}

//Если были ошибки массив полученных данных отправляем опять на adding-post.php И там должно отразиться неверные данные и 
//причины - почему данные не приняты. Эта процедура может крутиться много раз

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = ['heading', 'post-text', 'cite-text', 'quote-author']; // формируем список полей, обязательных для заполнения

    //текстовая публикация
//     if ($_POST['post-type'] == 1) {
//         $arr_options = array(
//             'heading' => FILTER_SANITIZE_STRING,
//             'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
//             'post-type' =>  FILTER_SANITIZE_STRING,
//             'post-text' => FILTER_SANITIZE_STRING,
//         );
//     }
    //цитата
//     if ($_POST['post-type'] == 2) {
//         $arr_options = array(
//             'heading' => FILTER_SANITIZE_STRING,
//             'tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
//             'post-type' =>  FILTER_SANITIZE_STRING,
//             'cite-text' => FILTER_SANITIZE_STRING,
//             'quote-author' => FILTER_SANITIZE_STRING,
//         );
//     }

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
    ];
    
    foreach ($post_data as $key => $value) {
        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле надо заполнить";
        }

        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    $errors = array_filter($errors);
//    print_r($errors);
//     } elseif ($_POST['post-type'] == '3') {
//         $arr_options = array(
//             'photo-heading' =>  FILTER_SANITIZE_STRING,
//             'photo-url' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже Обязательность - Нет
//             'photo-tags' =>  FILTER_SANITIZE_STRING,// Обязательность - Нет
//         );
        
//         $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями.
        
//         if (isset($_FILES['userpic-file-photo'])) {
//             $file_name = $_FILES['userpic-file-photo']['name'];
//             $file_path = __DIR__ . '/uploads/';
//             // Формат загруженного файла должен быть изображением одного из следующих типов: png, jpeg, gif.
//             move_uploaded_file($_FILES['userpic-file-photo']['tmp_name'], $file_path . $file_name);
//         }
        
//     } elseif ($_POST['post-type'] == '4') {
//         $arr_options = array(
//             'video-heading' =>  FILTER_SANITIZE_STRING,
//             'video-url' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже
//             'video-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
//         );
        
//     } elseif ($_POST['post-type'] == '5') {
//         $arr_options = array(
//             'post-link' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже
//         );

    
    //Запись данных
    if (!$errors) {
        $post_data['user_id'] = 3; //пока укажите в качестве ID пользователя любое число
        if ($post_type_chosen == 1) {
            $query = 'INSERT INTO post (p_title, p_content, user_id, category_id) VALUES (?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['post-text'], $post_data['user_id'], $post_data['post-type']));
        } elseif ($post_type_chosen == 2) {
            $query = 'INSERT INTO post (p_title, p_content, author, user_id, category_id) VALUES (?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($db_link, $query, $data = array($post_data['heading'], $post_data['cite-text'], $post_data['quote-author'], $post_data['user_id'], $post_data['post-type']));
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

// массив отображаемых наименований
$field_heads = [];


// добавляем обязательное для всех постов поле Заголовок
// проверка на обязательность заполнения определённых полей формы
// валидация URL 
// валидация Тегов 
// валидация Фото по выбранному фото или ссылке на него 

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