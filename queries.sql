USE readme;

-- Добавление списка типов контента для поста

INSERT INTO `category`
    (`category`, `category_name`)
VALUES 
    ('Текст', 'text'),
    ('Цитата', 'quote'), 
    ('Картинка', 'photo'),
    ('Видео', 'video'),
    ('Ссылка', 'link');
    
 -- Добавление пользователей

INSERT INTO `user` 
    (`dt_add`, `email`, `login`, `password`, `avatar`)
VALUES 
    ('2017.03.01', 'larisa@mail.ru', 'Лариса', '123', 'userpic-larisa-small.jpg'),
    ('2018.08.06', 'vladik@mail.ru', 'Владик', '1234', 'userpic.jpg'),
    ('2018.02.15', 'victor@mail.ru', 'Виктор', '12345', 'userpic-mark.jpg'),
    ('2022.05.23', 'alena@mail.ru', 'Алена', '123456', ''),
    ('2023.01.01', 'alex@mail.ru', 'Александр', '1234567', '');
    
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
    ('2022.08.07', 'Какая красота', '5', '4');
    
-- Добавить лайк к посту

INSERT INTO `likeit`
    (`user_id`, `post_id`)
VALUES 
    ('1', '3');
    
-- Подписаться на пользователя

INSERT INTO `subscription`
    (`user_id`, `target_id`)
VALUES 
    ('1', '4');
    
-- Получить список постов с сортировкой по популярности (просмотры view_count) и вместе с именами авторов и типом контента

SELECT p.id, p.dt_add, p.p_title, p.p_content, p.author, p.p_img, p.p_video, p.p_link, p.user_id, p.category_id, p.view_count, u.login, c.category 
FROM post p
LEFT JOIN user u ON p.user_id = u.id	
LEFT JOIN category c ON p.category_id = c.id	
ORDER BY view_count DESC;

-- Получить список постов для конкретного пользователя

SELECT * FROM post p WHERE user_id = 3;

-- Получить список комментариев для одного поста, в комментариях должен быть логин пользователя

SELECT c.id, c.dt_add, c.c_content, c.user_id, c.post_id, p.user_id, p.id, u.login
FROM  comment c
JOIN post p ON c.post_id = p.id
JOIN user u ON u.id = p.user_id 
WHERE p.id = 2;
