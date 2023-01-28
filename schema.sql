CREATE DATABASE IF NOT EXISTS readme
	DEFAULT CHARACTER SET utf8
	COLLATE utf8_general_ci;
	
USE readme;

CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
 	dt_add DATETIME,
    	email VARCHAR(128) NOT NULL UNIQUE,
	login VARCHAR(128) NOT NULL UNIQUE,
	password VARCHAR(128) NOT NULL,
	avatar TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `users` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS posts (
	id_post INT AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME,
	title VARCHAR(128),
	content TEXT,
	author VARCHAR(128),
	img VARCHAR(128),
	video VARCHAR(128),
	link VARCHAR(128),
	views INT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `posts` ADD INDEX ( `id_post` );

CREATE TABLE IF NOT EXISTS post_user_rel (
	id_post_user INT AUTO_INCREMENT PRIMARY KEY,
	id_post INT NOT NULL,
	id_user INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `post_user_rel` ADD INDEX ( `id_post` );
ALTER TABLE `post_user_rel` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS post_category_rel (
	id_post_category INT AUTO_INCREMENT PRIMARY KEY,
	id_post INT NOT NULL,
	id_category INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `post_category_rel` ADD INDEX ( `id_post` );
ALTER TABLE `post_category_rel` ADD INDEX ( `id_category` );

CREATE TABLE IF NOT EXISTS post_hashtag_rel (
	id_post_hashtags INT AUTO_INCREMENT PRIMARY KEY,
	id_post INT NOT NULL,
	id_hashtag INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `post_hashtag_rel` ADD INDEX ( `id_post` );
ALTER TABLE `post_hashtag_rel` ADD INDEX ( `id_hashtag` );

CREATE TABLE IF NOT EXISTS comments (
	id_comment INT  AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME,
	content TEXT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `comments` ADD INDEX ( `id_comment` );

CREATE TABLE IF NOT EXISTS comment_user_rel (
	id_comment_user INT AUTO_INCREMENT PRIMARY KEY,
	id_comment INT,
	id_user INT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `comment_user_rel` ADD INDEX ( `id_comment` );
ALTER TABLE `comment_user_rel` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS comment_post_rel (
	id_comment_post INT AUTO_INCREMENT PRIMARY KEY,
	id_comment INT NOT NULL,
	id_post INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `comment_post_rel` ADD INDEX ( `id_comment` );
ALTER TABLE `comment_post_rel` ADD INDEX ( `id_post` );

CREATE TABLE IF NOT EXISTS likes (
	id_like INT AUTO_INCREMENT PRIMARY KEY,
	id_post INT NOT NULL,
	id_user INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `likes` ADD INDEX ( `id_user` );
ALTER TABLE `likes` ADD INDEX ( `id_post` );

CREATE TABLE IF NOT EXISTS subscriptions_author (
	id_subscription_author INT AUTO_INCREMENT PRIMARY KEY,
	id_user INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `subscriptions_author` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS subscriptions_follower (
	id_subscription_follower INT AUTO_INCREMENT PRIMARY KEY,
	id_user INT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `subscriptions_follower` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS messages (
	id_message INT AUTO_INCREMENT PRIMARY KEY,
	dt_add DATETIME,
	content TEXT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `messages` ADD INDEX ( `id_message` );

CREATE TABLE IF NOT EXISTS message_recipient_rel (
	id_message_recipient INT AUTO_INCREMENT PRIMARY KEY,
	id_message INT,
	id_user INT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `message_recipient_rel` ADD INDEX ( `id_message` );
ALTER TABLE `message_recipient_rel` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS  message_sender_rel (
	id_message_sender INT AUTO_INCREMENT PRIMARY KEY,
	id_message INT NOT NULL,
	id_user INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `message_sender_rel` ADD INDEX ( `id_message` );
ALTER TABLE `message_sender_rel` ADD INDEX ( `id_user` );

CREATE TABLE IF NOT EXISTS categories (
	id_category INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(128),
	class_name VARCHAR(128)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `categories` ADD INDEX ( `id_category` );

INSERT INTO `categories` (`id_category`, `name`, `class_name`)
VALUES (NULL, 'Текст', 'text'),  (NULL, 'Цитата', 'quote'), (NULL, 'Картинка', 'photo'), (NULL, 'Видео', 'video'), (NULL, 'Ссылка', 'link');

CREATE TABLE IF NOT EXISTS hashtags (
	id_hashtag INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(128) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

ALTER TABLE `hashtags` ADD INDEX ( `id_hashtag` );