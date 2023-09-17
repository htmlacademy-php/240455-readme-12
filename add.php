<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
$query = 'SELECT * FROM category';

$post_types = get_result($db_link, $query); //создание вспомогательных массивов, например $post_types

//объявление пустых массивов
$post_data = []; // массив полученных данных 
$errors = []; // массив ошибок. будут накапливаться все ошибки по типу поста $errors['url'], $errors['photo']
//По такому принципу $errors['photo'] = [ 'head' => 'Фото', 'description' => 'Файл фото недопустимого типа.', ];
//учесть $allow_ext = ['png', 'jpeg', 'jpg', 'gif']; // разрешенные типы фото
// если нет ошибок, то запись данных и переход на страницу просмотра поста (post.php) if (!$errors) {
// данные записываем через подготовленное выражение
//}
//Если были ошибки массив полученных данных отправляем опять на adding-post.php И там должно отразиться неверные данные и 
//причины - почему данные не приняты. Эта процедура может крутиться много раз

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //$curr_type = get_current_type(INPUT_POST); // текущий тип поста получен по POST
    
    if ($_POST['post-type'] == 'text') {
        $arr_options = array(
            'text-heading' => FILTER_SANITIZE_STRING,
            'post-text' => FILTER_SANITIZE_STRING,
            'text-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
            'post-type' =>  FILTER_SANITIZE_STRING,
        );
        
        $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST

        //Валидация полей формы публикации
        $rules = [
            'text-heading' => function() {
            return validateFilled('text-heading');
            },
            'post-text' => function() {
            return validateFilled('post-text');
            },
            'post-text' => function() {
            return validateLength('post-text', 70);
            }
            ];
        
        foreach ($_POST as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule();
            }
        }
        
        $tags = array_filter(explode("#", $post_data['text-tags']));
        
        $post_type_chosen = $post_data['post-type'];
        
        $errors = array_filter($errors);
        
        //Запись данных 
        if (!$errors) {
            
            //header("Location: /post.php?post_id=");
        }
        
    } elseif ($_POST['post-type'] == 'photo') {
        $arr_options = array(
            'photo-heading' =>  FILTER_SANITIZE_STRING,
            'photo-url' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже Обязательность - Нет
            'photo-tags' =>  FILTER_SANITIZE_STRING,// Обязательность - Нет
        );
        
        $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями.
        
        if (isset($_FILES['userpic-file-photo'])) {
            $file_name = $_FILES['userpic-file-photo']['name'];
            $file_path = __DIR__ . '/uploads/';
            // Формат загруженного файла должен быть изображением одного из следующих типов: png, jpeg, gif.
            move_uploaded_file($_FILES['userpic-file-photo']['tmp_name'], $file_path . $file_name);
        }
        
    } elseif ($_POST['post-type'] == 'video') {
        $arr_options = array(
            'video-heading' =>  FILTER_SANITIZE_STRING,
            'video-url' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже
            'video-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        );
        
        $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями.
        
    } elseif ($_POST['post-type'] == 'quote') {
        $arr_options = array(
            'quote-heading' =>  FILTER_SANITIZE_STRING,
            'quote-text' =>  FILTER_SANITIZE_STRING,
            'quote-author' =>  FILTER_SANITIZE_STRING,
            'quote-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        );
        
        $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями.
        
    } elseif ($_POST['post-type'] == 'link') {
        $arr_options = array(
            'link-heading' =>  FILTER_SANITIZE_STRING,
            'post-link' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже
            'link-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        );
        
        $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями.
        
    }
} else { 
    //$curr_type = get_current_type(INPUT_GET); // текущий тип поста получен по GET 
    
    // Открытие таба по выбранному типу контента
    $post_type_chosen = filter_input(INPUT_GET, 'post_type_chosen', FILTER_SANITIZE_STRING);
//     $post_type_chosen = (int) $post_type_chosen;
}

if (!isset($post_type_chosen)) {
    $post_type_chosen = 'text'; // публикация с текстом по умолчанию
} 

// массив отображаемых наименований
$field_heads = [];

// формируем список полей, обязательных для заполнения
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