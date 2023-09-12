<?php

require_once 'helpers.php';
require_once 'functions.php';
require_once 'dbconn.php';

// Выполнение запросов
$query = 'SELECT * FROM category';

$post_types = get_result($db_link, $query); //создание вспомогательных массивов, например $post_types

// Фильтрация по выбранному типу контента
$post_type_chosen = filter_input(INPUT_GET, 'post_type_chosen', FILTER_SANITIZE_NUMBER_INT);
$post_type_chosen = (int) $post_type_chosen; 

if (!$post_type_chosen) {
    $post_type_chosen = 1; // публикация с текстом по умолчанию
} 

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
    //$curr_type = get_current_type(INPUT_POST); // текущий типа поста / получен по POST
} else { 
    //$curr_type = get_current_type(INPUT_GET); // текущий типа поста получен по GET 
}

// массив отображаемых наименований
$field_heads = [];

// отфильтрованные данные из POST. Отсутствующие поля заполняем пустыми значениями. url, при наличии в $_POST, будет обработано ниже
//$post_data = filter_input_array(INPUT_POST, $arr_options); 


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
]);

$layout_content = include_template('layout.php', [
   'page_title' => 'добавление публикации',
   'is_auth' => $is_auth, 
   'user_name' => $user_name,
   'main_content' => $main_content,
]);

print($layout_content);