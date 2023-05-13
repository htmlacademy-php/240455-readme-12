<?php 

require_once 'db.php';

// Подключение к базе

$db_link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if ($db_link == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($db_link, "utf8");

?>
