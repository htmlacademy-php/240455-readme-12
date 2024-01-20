CREATE DATABASE IF NOT EXISTS readme
	DEFAULT CHARACTER SET utf8
	COLLATE utf8_general_ci;
	
USE readme;

-- 5.0 Пользователь

CREATE TABLE IF NOT EXISTS user (
	id INT AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
	email VARCHAR(128) NOT NULL UNIQUE,
	login VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	avatar VARCHAR(255)
) COMMENT 'Зарегистрированные пользователи';

-- 5.7 Тип контента

CREATE TABLE IF NOT EXISTS category (
	id INT AUTO_INCREMENT PRIMARY KEY,
	category VARCHAR(20) comment 'Наименование типа контента',
	category_name VARCHAR(20) COMMENT 'Имя класса',
	category_w TINYINT COMMENT 'Ширина иконки',
	category_h TINYINT COMMENT 'Высота иконки'
) COMMENT 'Тип контента';

-- 5.1 Пост

CREATE TABLE IF NOT EXISTS post (
	id INT AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
	p_title VARCHAR(255) NOT NULL,
	p_content TEXT,
	author VARCHAR(128) COMMENT 'Автор цитаты: задаётся пользователем',
	p_img VARCHAR(255) COMMENT 'Изображение: ссылка на сохранённый файл изображения',
	p_video VARCHAR(255) COMMENT 'Видео: ссылка на видео с youtube',
	p_link VARCHAR(255) COMMENT 'Ссылка: ссылка на сайт, задаётся пользователем;',
	user_id INT NOT NULL COMMENT 'Автор поста. Поле связи с user.id',
	category_id INT NOT NULL COMMENT 'Контент/категория поста. Поле связи с category.id',
	view_count INT UNSIGNED DEFAULT 0,
	INDEX (user_id),
	INDEX (category_id),
	FOREIGN KEY (user_id) REFERENCES user (id),
	FOREIGN KEY (category_id) REFERENCES category (id)
) COMMENT 'Посты';

-- 5.2 Комментарий

CREATE TABLE IF NOT EXISTS comment (
	id INT  AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
	c_content TEXT NOT NULL,
	user_id INT NOT NULL COMMENT 'Автор комментария. Поле связи с user.id',
	post_id INT NOT NULL COMMENT 'id поста с этим комментарием. Поле связи с post.id',
	INDEX (user_id),
	INDEX (post_id),
	FOREIGN KEY (user_id) REFERENCES user (id),
	FOREIGN KEY (post_id) REFERENCES post (id)
) COMMENT 'Комментарии к постам';

-- 5.3 Лайки

CREATE TABLE IF NOT EXISTS likeit (
	id INT  AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL COMMENT 'Автор лайка. Поле связи с user.id',
	post_id INT NOT NULL COMMENT 'id поста с этим лайком. Поле связи с post.id',
	UNIQUE INDEX (user_id, post_id), -- индексы как связки с другими таблицами. guarantees that the index key contains no duplicate values
	FOREIGN KEY (user_id) REFERENCES user (id),
	FOREIGN KEY (post_id) REFERENCES post (id)
) comment 'Лайки к постам';

CREATE INDEX user_index ON likeit (user_id); -- поисковой индекс, как ускоритель поиска
CREATE INDEX post_index ON likeit (post_id); -- поисковой индекс, как ускоритель поиска

-- 5.4 Подписка

CREATE TABLE IF NOT EXISTS subscription (
	id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL COMMENT 'Автор подписки. Поле связи с user.id',
	target_id INT NOT NULL COMMENT 'Пользователь, на которого подписались. Поле связи с user.id',
	UNIQUE INDEX (user_id, target_id), -- индексы как связки с другими таблицами. guarantees that the index key contains no duplicate values
	FOREIGN KEY (user_id) REFERENCES user (id),
	FOREIGN KEY (target_id) REFERENCES user (id)
) comment 'Подписка';

CREATE INDEX user_index ON subscription (user_id); -- поисковой индекс, как ускоритель поиска
CREATE INDEX target_index ON subscription (target_id); -- поисковой индекс, как ускоритель поиска
	
-- 5.5 Сообщение

CREATE TABLE IF NOT EXISTS message (
	id INT AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
	m_content TEXT NOT NULL,
	recipient_id INT NOT NULL COMMENT 'Получатель сообщения. Поле связи с user.id',
	sender_id INT NOT NULL COMMENT 'Отправитель сообщения. Поле связи с user.id',
	INDEX (recipient_id),
	INDEX (sender_id),
	FOREIGN KEY (recipient_id) REFERENCES user (id),
	FOREIGN KEY (sender_id) REFERENCES user (id)
) COMMENT 'Сообщения из внутренней переписки пользователей';

-- 5.6 Хештег

CREATE TABLE IF NOT EXISTS hashtag (
	id INT AUTO_INCREMENT PRIMARY KEY,
	h_name VARCHAR(128) NOT NULL UNIQUE
) COMMENT 'Хештеги';

CREATE TABLE IF NOT EXISTS post_hashtag_rel (
	id INT AUTO_INCREMENT PRIMARY KEY,
	post_id INT NOT NULL COMMENT 'id поста с этим хештегом. Поле связи с post.id',
	hashtag_id INT NOT NULL COMMENT 'Хештег. Поле связи с hashtag.id',
	UNIQUE INDEX (post_id, hashtag_id), -- индексы как связки с другими таблицами. guarantees that the index key contains no duplicate values
	FOREIGN KEY (post_id) REFERENCES post (id),
	FOREIGN KEY (hashtag_id) REFERENCES hashtag (id)
) comment 'Таблица связей между постами и хештегами';

CREATE INDEX post_index ON post_hashtag_rel (post_id); -- поисковой индекс, как ускоритель поиска
CREATE INDEX hashtag_index ON post_hashtag_rel (hashtag_id); -- поисковой индекс, как ускоритель поиска
