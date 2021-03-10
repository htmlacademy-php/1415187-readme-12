/* список типов контента для поста */
INSERT INTO content_types (type_name, type_class)
VALUES
('Фото', 'photo'),
('Видео', 'video'),
('Текст', 'text'),
('Цитата', 'quote'),
('Ссылка', 'link');

/* придумайте пару пользователей*/
INSERT INTO users (username, email, avatar, password)
VALUES
('Эльвира', 'elvira@mail.ru', 'img/userpic-elvira.jpg', '012345'),
('Петро', 'petro@rambler.ru', 'img/userpic-petro.jpg', '123456'),
('Лариса', 'larisa@gmail.ru', 'img/userpic-larisa.jpg', '234567'),
('Владик', 'vladik@ya.ru', 'img/userpic-big.jpg', '345678'),
('Виктор', 'viktor@bk.ru', 'img/userpic-mark.jpg', '456789');

/*существующий список постов */
INSERT INTO posts (heading, post_type, content, author_id, view_count)
VALUES
('Цитата', 4,  'Мы в жизни любим только раз, а после ищем лишь похожих', 3, 10),
('Игра престолов', 3, 'Не могу дождаться начала финального сезона своего любимого сериала!', 4, 3),
('Наконец, обработал фотки!', 1, 'img/rock.jpg', 3, 49),
('Моя мечта', 1, 'img/coast.jpg', 5, 25),
('Лучшие курсы', 5, 'www.htmlacademy.ru', 4, 13);

UPDATE posts SET quote_author = 'Неизвестный автор' WHERE id=1;

/* придумайте пару комментариев к разным постам */
INSERT INTO comments SET user_id = 2, post_id = 4, content = 'тестовый комментарий 1';
INSERT INTO comments SET user_id = 1, post_id = 5, content = 'тестовый комментарий 2';

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

UPDATE posts SET content = NULL WHERE id BETWEEN 3 AND 4;
UPDATE posts SET img_url = 'img/rock.jpg' WHERE id = 3;
UPDATE posts SET img_url = 'img/coast.jpg' WHERE id = 4;

UPDATE users SET password = 'd6a9a933c8aafc51e55ac0662b6e4d4a' WHERE id = 1;
UPDATE users SET password = 'e10adc3949ba59abbe56e057f20f883e' WHERE id = 2;
UPDATE users SET password = '508df4cb2f4d8f80519256258cfb975f' WHERE id = 3;
UPDATE users SET password = '5bd2026f128662763c532f2f4b6f2476' WHERE id = 4;
UPDATE users SET password = 'e35cf7b66449df565f93c607d5a81d09' WHERE id = 5;