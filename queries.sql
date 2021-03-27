/* список типов контента для поста */
INSERT INTO content_types (type_name, type_class)
VALUES
('Фото', 'photo'),
('Видео', 'video'),
('Текст', 'text'),
('Цитата', 'quote'),
('Ссылка', 'link');

/* придумайте пару пользователей*/
INSERT INTO users (username, email, avatar, password, dt_add)
VALUES
('Эльвира', 'elvira@mail.ru', 'userpic-elvira.jpg', '012345', '2005-11-03 06:06:57'),
('Петро', 'petro@rambler.ru', 'userpic-petro.jpg', '123456', '2006-01-02 18:06:37'),
('Лариса', 'larisa@gmail.ru', 'userpic-larisa.jpg', '234567', '2008-03-12 06:06:02'),
('Владик', 'vladik@ya.ru', 'userpic-big.jpg', '345678', '2012-11-03 06:06:57'),
('Виктор', 'viktor@bk.ru', 'userpic-mark.jpg', '456789', '2020-08-11 23:11:15');

/*существующий список постов */
INSERT INTO posts (heading, post_type, content, author_id, view_count, dt_add)
VALUES
('Цитата', 4,  'Мы в жизни любим только раз, а после ищем лишь похожих', 3, 10, '2020-11-03 06:06:57'),
('Игра престолов', 3, 'Не могу дождаться начала финального сезона своего любимого сериала!', 4, 3, '2019-11-03 06:06:57'),
('Наконец, обработал фотки!', 1, 'img/rock.jpg', 5, 49, '2020-03-03 06:06:57'),
('Моя мечта', 1, 'img/coast.jpg', 3, 25, '2021-02-03 18:06:57'),
('Лучшие курсы', 5, 'www.htmlacademy.ru', 4, 13, '2020-04-08 23:18:37');

UPDATE posts SET quote_author = 'Неизвестный автор' WHERE id=1;

/* придумайте пару комментариев к разным постам */
INSERT INTO comments SET user_id = 2, post_id = 4, content = 'тестовый комментарий 1', dt_add = '2019-11-03 06:06:57';
INSERT INTO comments SET user_id = 1, post_id = 5, content = 'тестовый комментарий 2', dt_add = '2019-11-03 06:06:57';

/* получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента */
SELECT posts.content, posts.view_count, users.username, content_types.type_name FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
ORDER  BY view_count;

/* получить список постов для конкретного пользователя; */
SELECT * FROM posts WHERE author_id=5;

/*получить список комментариев для одного поста, в комментариях должен быть логин пользователя */
SELECT comments.content, users.username FROM comments
INNER JOIN users ON comments.user_id=users.id
WHERE comments.post_id=5;

/* добавить лайк к посту */
INSERT INTO likes SET user_id=3, post_id=3;

/* подписаться на пользователя */
INSERT INTO subscribe SET follower_id=4, author_id=5;
INSERT INTO subscribe SET follower_id=1, author_id=2;
INSERT INTO subscribe SET follower_id=1, author_id=3;   
INSERT INTO subscribe SET follower_id=1, author_id=4;
INSERT INTO subscribe SET follower_id=1, author_id=5;

UPDATE posts SET content = NULL WHERE id BETWEEN 3 AND 4;
UPDATE posts SET img_url = 'rock.jpg' WHERE id = 3;
UPDATE posts SET img_url = 'coast.jpg' WHERE id = 4;

/* хэши паролей пользователей */
UPDATE users SET password = '$2y$10$jrqi2JwC04tkGjGbYsyihO8veQEBLtAxjHbFPyIcGCY23NqdOrJIS' WHERE id = 1;
UPDATE users SET password = '$2y$10$cvYgfAODC3E6v7r8DP9cE.NRsZ2wFuUzqVyuEfh/ccYO7azs6qi76' WHERE id = 2;
UPDATE users SET password = '$2y$10$/VIYHnnIsg807GHG1as/1e00oF3L7A1UIrnoqzcyAdsOiOhwvsSv6' WHERE id = 3;
UPDATE users SET password = '$2y$10$fWUzRnLAa0Pb8XGdkFNqCuTWeNQnWRlMqVXz0ZmnW0Dq9EjH0Ewyq' WHERE id = 4;
UPDATE users SET password = '$2y$10$guNBWeUMFFQi3pa/xR2yKOZQ3oFMsPQLPPPw5APDLHdZC7InfxQzm' WHERE id = 5;


INSERT INTO messages (dt_add, content, sender_id, receiver_id, was_read)
VALUES
('2018-02-03 13:27:05', 'Тестовое сообщение #1', 3, 1, false),
('2020-02-03 13:27:05','Тестовое сообщение #2', 1, 2, false);