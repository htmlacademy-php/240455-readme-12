USE readme;

-- Добавление списка типов контента для поста

INSERT INTO `category`
    (`category`, `category_name`, `category_w`, `category_h`, `category_w_main`, `category_h_main`)
VALUES 
    ('photo', 'Картинка', '22', '18', '22', '18'),
	 ('video', 'Видео', '24', '16', '22', '18'),
    ('text', 'Текст', '20', '21', '22', '18'),
    ('quote', 'Цитата', '21', '20', '22', '18'),
    ('link', 'Ссылка', '21', '18', '22', '18');
    
 -- Добавление пользователей

INSERT INTO `user` 
    (`dt_add`, `email`, `login`, `password`, `avatar`)
VALUES 
    ('2017.03.01', 'larisa@mail.ru', 'Лариса', '123', 'userpic-larisa-small.jpg'),
    ('2018.08.06', 'vladik@mail.ru', 'Владик', '1234', 'userpic.jpg'),
    ('2018.02.15', 'victor@mail.ru', 'Виктор', '12345', 'userpic-mark.jpg'),
    ('2022.05.23', 'alena@mail.ru', 'Алена', '123456', 'user-elvira.jpg'),
    ('2023.01.01', 'alex@mail.ru', 'Александр', '1234567', 'userpic-petro.jpg');
    
-- Добавление постов

INSERT INTO `post`
    (`dt_add`, `p_title`, `p_content`, `author`, `p_img`, `p_video`, `p_link`, `user_id`, `category_id`, `view_count`)
VALUES 
    ('2022.06.01', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Лариса', '', '', '', '1', '2', '10'),
    ('2022.06.05', 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', 'Владик', '', '', '', '2', '1', '3'),
    ('2022.08.07', 'Наконец, обработал фотки!', 'rock-medium.jpg', 'Виктор', 'rock-medium.jpg', '', '', '3', '3', '1'),
    ('2022.08.21', 'Моя мечта', 'coast-medium.jpg', 'Лариса', 'coast-medium.jpg', '', '', '1', '3', '0'),
    ('2022.09.30', 'Лучшие курсы', 'www.htmlacademy.ru', 'Владик', '', '', 'https://htmlacademy.ru/', '2', '2', '0');
    
-- Добавление комментариев

INSERT INTO `comment`
    (`dt_add`, `c_content`, `user_id`, `post_id`)
VALUES 
    ('2022.06.05', 'Я тоже!', '1', '2'),
    ('2022.08.07', 'Какая красота', '5', '4'),
	 ('2022.09.10', 'Хочу туда!!!!', '5', '4')
	 ('2023.01.15', 'Таиланд?', '1', '4');
    
-- Добавить лайк к посту

INSERT INTO `likeit`
    (`user_id`, `post_id`)
VALUES 
    ('1', '3');
    
-- Подписаться на пользователя

INSERT INTO `subscription`
    (`user_id`, `target_id`)
VALUES 
	 ('4', '1'),
	 ('3', '1'),
	 ('2', '1'),
    ('1', '4');
    
-- Добавить хештег

INSERT INTO `hashtag`
    (`h_name`)
VALUES 
    ('Трогательно'),
	 ('Природа'),
	 ('Сериал');

-- Добавить хештег

INSERT INTO `hashtag`
    (`h_name`)
VALUES 
    ('#Трогательно'),
	 ('#Природа'),
	 ('#Сериал'),
	 ('#Красота');

-- Добавить связь хештега и поста

INSERT INTO `post_hashtag_rel`
    (`post_id`, `hashtag_id`)
VALUES 
    ('1', '1'),
	 ('3', '2'),
	 ('2', '3'),
	 ('3', '4');
	 
-- Добавить подписчика

INSERT INTO `subscription`
    (`user_id`, `target_id`)
VALUES 
    ('1', '4');
    
-- Получить список постов с сортировкой по популярности (просмотры view_count) и вместе с именами авторов и типом контента

SELECT 
    p.*, 
    u.login, 
    c.category 
FROM post AS p
    INNER JOIN user AS u 
        ON p.user_id = u.id	
    INNER JOIN category AS c 
        ON p.category_id = c.id	
ORDER BY view_count DESC;

-- Получить список постов для конкретного пользователя

SELECT * FROM post WHERE user_id = 3;

-- Получить список комментариев для одного поста, в комментариях должен быть логин пользователя

SELECT 
	 c.id, 
	 c.dt_add, 
	 c.c_content, 
	 c.user_id, 
	 c.post_id,  
	 u.login
FROM comment AS c
	 INNER JOIN post AS p 
	 	  ON c.post_id = p.id 
	 INNER JOIN user AS u 
	 	  ON u.id = p.user_id 
WHERE p.id = 2;
    