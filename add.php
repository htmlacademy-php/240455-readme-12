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
    
    /* данные, полученные методом POST
     $_POST = array(
     'product_id'    => 'libgd<script>',
     'component'     => '10',
     'versions'      => '2.0.33',
     'testscalar'    => array('2', '23', '10', '12'),
     'testarray'     => '2',
     
     [text-heading] => Заголовок,
     [post-text] => Текст публикации,
     [text-tags] => теги,
     
     [quote-heading] => Заголовок цитаты,
     [quote-text] => Текст цитаты,
     [quote-author] => Автор цитаты,
     [quote-tags] => Теги цитаты,
     
     [photo-heading] => Ссылка из интернета с картинкой,
     [photo-url] => ссылка для картинки из инета,
     [photo-tags] => Теги для картинки,
     [файл]
     
     [video-heading] => ссылка на видео,
     [video-url] => ссылка YOUTUBE,
     [video-tags] => теги видео,
     
     [link-heading] => Заголовок ссылки,
     [post-link] => адрес ссылки,
     [link-tags] => теги для ссылки
     
     );
     */
    
    $arr_options = array(
        'text-heading' => FILTER_SANITIZE_STRING,
        'post-text'   => FILTER_SANITIZE_STRING,
        'text-tags'   =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        
        'quote-heading' =>  FILTER_SANITIZE_STRING,
        'quote-text' =>  FILTER_SANITIZE_STRING,
        'quote-author' =>  FILTER_SANITIZE_STRING,
        'quote-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        
        'photo-heading' =>  FILTER_SANITIZE_STRING,
        'photo-url' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже Обязательность - Нет
        'photo-tags' =>  FILTER_SANITIZE_STRING,// Обязательность - Нет
        
        'video-heading' =>  FILTER_SANITIZE_STRING,
        'video-url' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже
        'video-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
        
        'link-heading' =>  FILTER_SANITIZE_STRING,
        'post-link' =>  FILTER_SANITIZE_STRING, //url, при наличии в $_POST, будет обработано ниже
        'link-tags' =>  FILTER_SANITIZE_STRING, // Обязательность - Нет
    );
    
    $post_data = filter_input_array(INPUT_POST, $arr_options); // отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями.
    
    // формируем список полей, обязательных для заполнения
    $required_fields = ['text-heading', 'post-text', 'quote-heading', 'quote-text', 'quote-author', 
                        'photo-heading', 'video-heading', 'video-url', 'link-heading', 'post-link'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
    
    if (count($errors)) {
        // показать ошибку валидации
    }
    
    if (isset($_FILES['userpic-file-photo'])) {
        $file_name = $_FILES['userpic-file-photo']['name'];
        $file_path = __DIR__ . '/uploads/';
        // Формат загруженного файла должен быть изображением одного из следующих типов: png, jpeg, gif. 
        move_uploaded_file($_FILES['userpic-file-photo']['tmp_name'], $file_path . $file_name);
    }
} else { 
    //$curr_type = get_current_type(INPUT_GET); // текущий тип поста получен по GET 
    
    // Открытие таба по выбранному типу контента
    $post_type_chosen = filter_input(INPUT_GET, 'post_type_chosen', FILTER_SANITIZE_NUMBER_INT);
    $post_type_chosen = (int) $post_type_chosen;
}

if (!isset($post_type_chosen)) {
    $post_type_chosen = 1; // публикация с текстом по умолчанию
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