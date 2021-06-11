<?php

/**
 * Возвращает укороченную версию текста с многоточием, если длина больше указанной.
 *
 * @param string $text Полный текст
 * @param int $length Длина укороченного текста
 * @return string Укороченный текст
 */
function cut_text(string $text, int $length = 300)
{
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length + 1);
        $end = mb_strlen(strrchr($text, ' '));
        $text = mb_substr($text, 0, -$end) . '...';
    }
    return $text;
}

/**
 * Возвращает время относительно текущей даты.
 *
 * @param string $time Дата/время отсчета
 * @param DateTime $current_time Текущая дата/время
 * @return string Относительное время в общем формате (прим.: "4 дня *назад*", "3 недели *назад*")
 * @throws Exception
 */
function time_difference(string $time, DateTime $current_time)
{
    date_default_timezone_set('Europe/Moscow');

    $diff = date_diff($current_time, new DateTime($time));

    if ($diff->y > 0) {
        $relative_time = $diff->y . ' ' .
            get_noun_plural_form($diff->y, 'год', 'года', 'лет');
    } elseif ($diff->m > 0) {
        $relative_time = $diff->m . ' ' .
            get_noun_plural_form($diff->m, 'месяц', 'месяца', 'месяцев');
    } elseif ($diff->d > 6) {
        $relative_time = floor(($diff->d) / 7) . ' ' .
            get_noun_plural_form(floor(($diff->d) / 7), ' неделю', ' недели', ' недель');
    } elseif ($diff->d > 0) {
        $relative_time = $diff->d . ' ' .
            get_noun_plural_form($diff->d, 'день', 'дня', 'дней');
    } elseif ($diff->h > 0) {
        $relative_time = $diff->h . ' ' .
            get_noun_plural_form($diff->h, 'час', 'часа', 'часов');
    } elseif ($diff->i > 0) {
        $relative_time = $diff->i . ' ' .
            get_noun_plural_form($diff->i, 'минуту', 'минуты', 'минут');
    } elseif ($diff->s >= 0) {
        $relative_time = 'Менее минуты';
    } else {
        $relative_time = '';
    }
    return $relative_time;
}

/**
 * Создает страницу для ошибки 404
 */
function display_404_page($user)
{
    $page_content = include_template('404.php');
    $layout_content = include_template(
        'layout.php',
        [
            'content' => $page_content,
            'user' => $user,
            'title' => 'Ресурс не найден: 404',
            'active_section' => ''
        ]
    );
    print($layout_content);
    http_response_code(404);
}

/**
 * Выбирает размер иконки "Тип контента" (размеры взяты из разметки)
 *
 * @param string $type Тип контента
 * @return array Массив из двух значений: ширина и высота иконки.
 */
function filter_size_ico(string $type)
{
    if ($type === 'photo') {
        $result = ['w' => 22, 'h' => 18];
    } elseif ($type === 'video') {
        $result = ['w' => 24, 'h' => 16];
    } elseif ($type === 'text') {
        $result = ['w' => 20, 'h' => 21];
    } elseif ($type === 'quote') {
        $result = ['w' => 21, 'h' => 20];
    } elseif ($type === 'link') {
        $result = ['w' => 21, 'h' => 18];
    } else {
        $result = ['w' => 22, 'h' => 20];
    }
    return ($result);
}

/**
 * Создает список типов поста
 *
 * @param mysqli $connection Соединение с базой
 * @return array Список типов
 */
function get_content_types(mysqli $connection)
{
    $result = mysqli_query($connection, "SELECT * FROM content_types");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Подготавливает и выполняет "безопасный" запрос (и производит сравнение $check)
 *
 * @param mysqli $connection Данные для подключения к БД
 * @param string $sql Исходный запрос сплейсхолдерами
 * @param bool $check Сравнение с базой (true|false)
 * @param mixed $params Передаваемые параметры (integer|string)
 * @return mixed Результат выполнения подготовленного запроса
 */
function secure_query_bind_result(mysqli $connection, string $sql, bool $check, ...$params)
{
    $param_types = '';
    foreach ($params as $param) {
        $param_types .= (gettype($param) === 'integer') ? 'i' : 's';
    }
    $prepared_sql = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($prepared_sql, $param_types, ...$params);
    mysqli_stmt_execute($prepared_sql);
    if ($check) {
        mysqli_stmt_bind_result($prepared_sql, $bind);
        mysqli_stmt_fetch($prepared_sql);
        mysqli_stmt_close($prepared_sql);
        return $bind;
    } else {
        return mysqli_stmt_get_result($prepared_sql);
    }
}

/**
 * Разделяет строку с правилами валидации на отдельные првила
 *
 * @param array $rules Массив со всеми правилами валидации и их параметрами
 * @return array Массив, в котором каждая связка правило-параметры - отдельный элемент
 */
function get_validation_rules(array $rules): array
{
    $result = [];
    foreach ($rules as $field_name => $rule) {
        $result[$field_name] = explode('|', $rule);
    }
    return $result;
}

/**
 * Формирует имя функции валидации для дальнейшего вызова
 *
 * @param string $name Название метода валидации
 * @return string Имя функции валидации
 */
function get_validation_method_name(string $name): string
{
    $studly_words = str_replace(['-', ' '], '_', $name);
    return "validate_{$studly_words}";
}

/**
 * Разделяет название метода валидации и его параметры
 *
 * @param string $rule Связка правило-параметры
 * @return array Массив из названия и массива параметров
 */
function get_validation_name_and_parameters(string $rule): array
{
    $name_params = explode(':', $rule);
    $parameters = [];
    $name = $name_params[0];
    if (isset($name_params[1])) {
        $parameters = explode(',', $name_params[1]);
    }
    return [$name, $parameters];
}

/**
 * Валидация заполненого поля
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Параметр, по которому будет проводиться валидация
 * @return string Ошибка или NULL
 */
function validate_filled(array $input_array, string $parameter_name): ?string
{
    if (empty($input_array[$parameter_name])) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/**
 * Валидация длины поля
 *
 * @param array $input_array Массив, полученный методом POST
 * @param string $parameter_name Имя поля
 * @param array $length Массив с длиной поля ОТ и ДО
 * @return string Ошибка или NULL
 */
function validate_length(array $input_array, string $parameter_name, array $length)
{
    $len = strlen($input_array[$parameter_name]);
    if ($len < $length[0] or $len > $length[1]) {
        return 'Длина поля должна быть от ' . $length[0] . ' до ' . $length[1] . ' символов';
    }
    return null;
}

/**
 * Проверяет корректность URL-адреса
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Параметр, по которому будет проводиться валидация
 * @return string Ошибка либо NULL
 */
function validate_correct_url(array $input_array, string $parameter_name): ?string
{
    if (!filter_var($input_array[$parameter_name], FILTER_VALIDATE_URL)) {
        return 'Некорретный URL-адрес';
    }
    $headers = @get_headers($input_array[$parameter_name]);
    if (!is_array($headers)) {
        return "Такой ссылки не существует";
    }

    if (strpos($headers[0], '303')) {
        $err_flag = strpos($headers[18], '200') ? 200 : '';
    } else {
        $err_flag = strpos($headers[0], '200') ? 200 : '';
    }

    if ($err_flag !== 200) {
        return "Такой страницы не существует или ресурс недоступен";
    }
    return null;
}

/**
 * Проверяет загружен ли файл и является ли он изображением
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Имя поля, через которое загружен файл
 * @return string Ошибка либо NULL
 */
function validate_img_loaded(array $input_array, string $parameter_name): ?string
{
    if ($input_array[$parameter_name]['error'] !== 0) {
        return 'Файл не получен';
    } else {
        if (!in_array(exif_imagetype($input_array[$parameter_name]['tmp_name']), [1, 2, 3])) {
            return 'Недопустимый тип изображения';
        }
    }
    return null;
}

/**
 * Сохраняет файл в папку "@host/$img_folder/"
 *
 * @param string $img Название поля с изображением
 * @param string $img_folder Путь сохранения изображений
 * @return string Путь к сохраненному файлу
 */
function save_image(string $img, string $img_folder): ?string
{
    if ($_FILES[$img]['error'] !== 0) {
        return null;
    }
    $file_name = $_FILES[$img]['name'];
    move_uploaded_file($_FILES[$img]['tmp_name'], $img_folder . $file_name);
    return $file_name;
}

/**
 * Проверяет наличие по ссылке изображения
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Имя поля, содержащего ссылку на изображение
 * @return string Ошибка либо NULL
 */
function validate_image_url_content(array $input_array, string $parameter_name): ?string
{
    if (!@file_get_contents($input_array[$parameter_name])) {
        return 'По ссылке отсутствует изображение';
    } else {
        if (!in_array(@exif_imagetype($input_array[$parameter_name]), [1, 2, 3])) {
            return 'Недопустимый тип изображения';
        }
    }
    return null;
}

/**
 * Проверяет, что переданная ссылка ведет на доступное видео с youtube
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Ссылка на youtube видео
 * @return string Доступна или недоступна ссылка
 */
function validate_youtube_url(array $input_array, string $parameter_name): ?string
{
    $id = extract_youtube_id($input_array[$parameter_name]);

    if ($id) {
        $api_data = ['id' => $id, 'part' => 'id,status', 'key' => 'AIzaSyD24lsJ4BL-azG188tHxXtbset3ehKXeJg'];
        $url = "https://www.googleapis.com/youtube/v3/videos?" . http_build_query($api_data);
        $resp = @file_get_contents($url);
        $json = json_decode($resp, true);
        if ((($json['items']) === []) || ($json['items']) === null) {
            return 'Видео по ссылке не найдено';
        }
    }
    return null;
}

/**
 * Валидация массива значений из форм
 *
 * @param array $fields Проверяемый массив связками поле - значение
 * @param array $validation_array Массив правил валидации вида поле - список правил валидации
 * @param mysqli $connection Соединение с БД
 * @return array|string Массив со списком ошибок | Строка с ошибкой
 */
function validate(array $fields, array $validation_array, mysqli $connection)
{
    $db_functions = ['validate_exists', 'validate_correct_password'];
    $validations = get_validation_rules($validation_array);
    $errors = [];
    foreach ($validations as $field => $rules) {
        foreach ($rules as $rule) {
            [$name, $parameters] = get_validation_name_and_parameters($rule);
            $method_name = get_validation_method_name($name);
            $method_parameters = [];
            array_push($method_parameters, $fields, $field, $parameters);
            if (!function_exists($method_name)) {
                return 'Функции валидации ' . $method_name . ' не существует';
            }
            if (in_array($method_name, $db_functions)) {
                array_push($method_parameters, $connection);
            }
            if ($errors[$field] = call_user_func_array($method_name, $method_parameters)) {
                break;
            }
        }
    }
    return $errors;
}

/**
 * Проверяет совпадение вводов пароля
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @return string Ошибка или NULL
 */
function validate_repeat_password(array $input_array): ?string
{
    if ($input_array['password'] !== $input_array['password-repeat']) {
        return 'Пароли не совпадают';
    }
    return null;
}

/**
 * Проверяет корректность введенного email-адреса
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Проверяемый параметр, email
 * @return string Ошибка или NULL
 */
function validate_correct_email(array $input_array, string $parameter_name): ?string
{
    if (!filter_var($input_array[$parameter_name], FILTER_VALIDATE_EMAIL)) {
        return 'Некорретный email';
    }
    return null;
}

/**
 * Проверяет отсутствие|наличие значения в БД
 *
 * @param array $validation_array Проверяемый массив
 * @param string $parameter_name Имя искомого параметра
 * @param array $parameter_settings Установки параметров (где искать, и ищем отсутствие или наличие)
 * @param mysqli $connection Параметры подключения к БД
 * @return string Сообщение об ошибке, если нет ошибки - NULL
 */
function validate_exists(
    array $validation_array,
    string $parameter_name,
    array $parameter_settings,
    mysqli $connection
): ?string {
    $table_name = $parameter_settings[0];
    $column_name = $parameter_settings[1];
    $sql = "SELECT COUNT(*) AS amount FROM $table_name WHERE $column_name = ?";
    $amount = secure_query_bind_result($connection, $sql, true, $validation_array[$parameter_name]);
    if (($amount > 0) && (!in_array('not', $parameter_settings))) {
        return "Запись с таким $parameter_name уже присутствует в базе данных";
    } elseif (($amount === 0) && (in_array('not', $parameter_settings))) {
        return "Записи с таким $parameter_name нет в базе данных";
    }
    return null;
}

/**
 * Проверяет правильность введенного пароля
 *
 * @param array $validation_array Валидируемый массив
 * @param string $parameter_name Имя искомого параметра
 * @param        $parameter_settings
 * @param mysqli $connection Данные для подключения к БД
 * @return string Сообщение об ошибке или NULL
 */
function validate_correct_password(
    array $validation_array,
    string $parameter_name,
    $parameter_settings,
    mysqli $connection
): ?string {
    $table_name = $parameter_settings[0];
    $users_column_name = $parameter_settings[1];
    $password_column_name = $parameter_settings[2];
    $email = $validation_array['login'];
    $sql = "SELECT $password_column_name FROM $table_name WHERE $users_column_name = ?";
    $db_password = secure_query_bind_result($connection, $sql, false, $email);
    $password = mysqli_fetch_row($db_password)['password'] ?? null;
    return ($password !== null) ? (!password_verify(
        $validation_array[$parameter_name],
        $password
    ) ? "Вы ввели неверный пароль" : null) : null;
}

/**
 * Производит подключение к БД. Если доступ не получен - возвращает ошибку 500
 *
 * @param string $host Местоположение БД
 * @param string $user Логин
 * @param string $pass Пароль
 * @param string $db Имя БД
 * @return mysqli Результат подключения или NULL
 */
function db_connect(string $host, string $user, string $pass, string $db)
{
    $connection = mysqli_connect($host, $user, $pass, $db);

    if ($connection === false) {
        error_log(mysqli_connect_error(), 0);
        http_response_code(500);
        exit();
    }

    mysqli_set_charset($connection, "utf8mb4");
    return $connection;
}

/**
 * Ищет данные пользователя по email
 *
 * @param mysqli $connection подключение к БД
 * @param string $email Почта/логин пользователя
 * @return array ассоциативный массив с данными пользователя
 */
function get_user_data(mysqli $connection, string $email)
{
    $sql = "SELECT id, username, avatar FROM users WHERE email = ?";
    $result = secure_query_bind_result($connection, $sql, false, $email);
    return mysqli_fetch_assoc($result);
}

/**
 * Ищет данные пользователя в активном диалоге по id
 *
 * @param mysqli $connection подключение к БД
 * @param int|null $id ID пользователя
 * @return array ассоциативный массив с данными пользователя
 */
function get_user_data_dialog(mysqli $connection, $id)
{
    if ($id !== null) {
        $sql = "SELECT username, avatar FROM users WHERE id = ?";
        $result = secure_query_bind_result($connection, $sql, false, $id);
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

/**
 * Записывает данные пользователя из сессии, если аутентификация проведена
 *
 * @return array ассоциативный массив с данными пользователя
 */
function get_user(): ?array
{
    global $connection;
    if ($_SESSION['is_auth'] !== 1) {
        return null;
    }

    $user = [];
    $user['id'] = $_SESSION['id'];
    $user['name'] = $_SESSION['username'];
    $user['avatar'] = $_SESSION['avatar'];
    $user['messages'] = count_new_messages($connection, $user['id']);

    return $user;
}

/**
 * Производит подписку/отписку|возвращает статус подписки (true|false)
 *
 * @param mysqli $connection Подключение к БД
 * @param bool $check Произвести подписку.отписку| Проверку (true|false)
 * @param int $follower_id ID подписчика
 * @param int $author_id ID подписки
 * @return NULL|bool В случае проверки подписки возвращает статус
 */
function user_subscribe(mysqli $connection, bool $check, int $follower_id, int $author_id)
{
    $subscribe_query = "SELECT * FROM subscribe WHERE follower_id = ? AND author_id = ?";
    $subscribe_mysqli = secure_query_bind_result($connection, $subscribe_query, false, $follower_id, $author_id);
    if ($check) {
        if ($subscribe_mysqli->num_rows === 0) {
            $subscribe_query = "INSERT INTO subscribe SET follower_id = ?, author_id = ?";
            secure_query_bind_result($connection, $subscribe_query, false, $follower_id, $author_id);
            return true;
        } else {
            $subscribe_query = "DELETE FROM subscribe WHERE follower_id = ? AND author_id = ?";
            secure_query_bind_result($connection, $subscribe_query, false, $follower_id, $author_id);
            return false;
        }
    } else {
        return $subscribe_mysqli->num_rows > 0;
    }
    return null;
}

/**
 * Собирает данные для ленты постов на странице feed
 *
 * @param mysqli $connection Подключение к БД
 * @param string|NULL $filter Фильтр по типу контента
 * @param int $follower_id id пользователя
 * @return array Список постов
 */
function get_feed_posts(mysqli $connection, $filter, int $follower_id)
{
    $select_posts_query =
        "SELECT
            posts.*,
            content_types.type_class,
            users.id AS user_id,
            users.username,
            users.avatar,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comments
        FROM posts
        INNER JOIN users ON posts.author_id=users.id
        INNER JOIN content_types ON posts.post_type=content_types.id
        INNER JOIN subscribe ON posts.author_id = subscribe.author_id
        WHERE subscribe.follower_id = $follower_id ";

    if ($filter !== null) {
        $select_posts_query .= "AND content_types.type_class = '$filter' ";
    }

    $select_posts_query .= "ORDER BY dt_add DESC";
    $posts_mysqli = mysqli_query($connection, $select_posts_query);
    $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    if (!empty($posts ?? null)) {
        foreach ($posts as &$post) {
            $post = array_merge($post, get_hashtags($connection, $post['id']));
            $post = array_merge($post, count_reposts($connection, $post));
        }
    }
    return $posts;
}

/**
 * Возвращает значение, если оно содержится в массиве, иначе возвращает NULL
 *
 * @param mixed $value Искомое значение
 * @param array $options Массив, в котором ищем
 * @return mixed Исходное значение, если найдено в массиве. Иначе - NULL
 */
function get_filter($value, array $options)
{
    if (($value !== null) && (in_array($value, $options))) {
        return $value;
    }
    return null;
}

/**
 * Совершает репост поста, при условии, что пользователь не автор
 * оригинального поста и пользователь не совершал ранее репоста текущего поста (из  любого источника).
 * Скопированы будут все данные, кроме время (станет текущее), автор (пользователь),
 * количество просмотров (аннулируется), откуда взят репост (нужно, если репост делается уже скопированного поста).
 * В случае, если автор оригинального поста попытается сделать репост своего поста (из любого источника), то функция вернет ID поста без внесения каких-либо измений.
 * В случае, если пользователь уже делал репост текущего поста (из любого источника), в нем обновится только дата.
 *
 * @param mysqli $connection Соединение с БД
 * @param int $user_id ID пользователя
 * @param int $post_id ID поста
 * @return $repost_id ID of repost on current user
 */
function repost_post(mysqli $connection, int $user_id, int $post_id)
{
    $sql = "SELECT COUNT(*) AS amount FROM posts WHERE author_id = ?
        AND id = (SELECT original_post FROM posts WHERE id = ?)";
    $amount = secure_query_bind_result($connection, $sql, true, $user_id, $post_id);
    if ($amount !== 0) {
        return $post_id;
    }

    $current_time = date('Y-m-d H:i:s');
    $sql = "SELECT COUNT(*) AS amount FROM posts WHERE author_id = ?
        AND original_post = (SELECT original_post FROM posts WHERE id = ?)";
    $amount = secure_query_bind_result($connection, $sql, true, $user_id, $post_id);
    if ($amount === 0) {
        $sql =
            "INSERT INTO posts
                (dt_add,
                author_id,
                view_count,
                post_type,
                heading,
                content,
                quote_author,
                img_url,
                youtube_url,
                url,
                original_post,
                repost_from)
            SELECT
                ?,
                ?,
                0,
                post_type,
                heading,
                content,
                quote_author,
                img_url,
                youtube_url,
                url,
                original_post,
                id
            FROM posts WHERE id = ?";
    } else {
        $sql = "UPDATE posts SET dt_add = ? WHERE author_id = ? AND original_post =
            (SELECT original_post FROM (SELECT original_post FROM posts WHERE id = ?) AS posts_1)";
    }
    secure_query_bind_result($connection, $sql, false, $current_time, $user_id, $post_id);
    $sql = "SELECT id FROM posts WHERE author_id = ? AND original_post =
        (SELECT original_post FROM posts WHERE id = ?)";

    return secure_query_bind_result($connection, $sql, true, $user_id, $post_id);
}

/**
 * Производит подсчет репостов. Если пост не оригинальный (ID поста не совпадает с ID оригинала),
 * тогда подсчет ведется только тех репостов, которые сделаны с текущего поста.
 * Если пост оригинальный - ведется подсчет всех репостов (то есть количество таких постов в базе - 1)
 *
 * @param mysqli $connection
 * @param array $post Все данные поста, полученные ранее
 * @return mixed Количество репостов (опционально имя автора)
 */
function count_reposts(mysqli $connection, array $post)
{
    if ($post['id'] === $post['original_post']) {
        $sql_count_reposts = "SELECT COUNT(*) FROM posts WHERE original_post = ?";
        return ['reposts' => secure_query_bind_result($connection, $sql_count_reposts, true, $post['id']) - 1];
    } else {
        $sql_count_reposts = "SELECT COUNT(*) FROM posts WHERE repost_from = ?";
        $sql_get_author_name = "SELECT users.username FROM users JOIN posts ON users.id = posts.author_id WHERE posts.id = ?";
        return [
            'reposts' => secure_query_bind_result($connection, $sql_count_reposts, true, $post['id']),
            'author_original' => secure_query_bind_result(
                $connection,
                $sql_get_author_name,
                true,
                $post['original_post']
            )
        ];
    }
}

/**
 * Получает пост по ID
 *
 * @param mysqli $connection Соединение с БД
 * @param int|NULL $post_id ID поста
 * @return array|NULL Полученный из БД пост|NULL
 */
function get_post(mysqli $connection, $post_id)
{
    $select_post_by_id =
        "SELECT
            posts.*,
            users.username,
            users.avatar,
            content_types.type_class,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comments
        FROM posts
        INNER JOIN users ON posts.author_id=users.id
        INNER JOIN content_types ON posts.post_type=content_types.id
        WHERE posts.id = ?;";
    $post_mysqli = secure_query_bind_result($connection, $select_post_by_id, false, $post_id);
    $post = mysqli_fetch_assoc($post_mysqli);
    if (!empty($post ?? null)) {
        $reposts = count_reposts($connection, $post);
        $post = array_merge($post, get_hashtags($connection, $post_id));
        return array_merge($post, $reposts);
    }
    return null;
}

function get_hashtags(mysqli $connection, $post_id)
{
    $select_hashtags_query =
        "SELECT
            GROUP_CONCAT(hashtags.tag_name) as tags
        FROM posts
        inner JOIN post_tags ON post_tags.post_id = posts.id
        inner JOIN hashtags ON hashtags.id = post_tags.hashtag_id
        WHERE posts.id = ?";
    $hastags_mysqli = secure_query_bind_result($connection, $select_hashtags_query, false, $post_id);
    return mysqli_fetch_row($hastags_mysqli);
}

/**
 * Получить данные автора поста по ID
 *
 * @param mysqli $connection Соединение с БД
 * @param int $author_id ID автора
 * @return array|NULL Полученный из БД массив с данными автора|NULL
 */
function get_post_author(mysqli $connection, int $author_id)
{
    $select_post_author =
        "SELECT
            users.id,
            users.username,
            users.avatar,
            users.dt_add,
        (SELECT COUNT(*) FROM subscribe WHERE subscribe.author_id = users.id) AS followers,
        (SELECT COUNT(*) FROM posts WHERE posts.author_id = users.id) AS posts
        FROM users
        WHERE users.id = ?";
    $author_mysqli = secure_query_bind_result($connection, $select_post_author, false, $author_id);
    return mysqli_fetch_assoc($author_mysqli);
}

/**
 * Получить комментарии поста по ID
 *
 * @param mysqli $connection Соединение с БД
 * @param int $post_id ID поста
 * @return array|NULL Полученный из БД массив с комментариями|NULL
 */
function get_post_comments(mysqli $connection, int $post_id)
{
    $select_post_comments =
        "SELECT
            comments.*,
            users.id AS author_id,
            users.username AS author_name,
            users.avatar
        FROM comments
        INNER JOIN users ON comments.user_id=users.id
        WHERE post_id = ? ORDER BY dt_add DESC;";
    $comments_mysqli = secure_query_bind_result($connection, $select_post_comments, false, $post_id);
    return mysqli_fetch_all($comments_mysqli, MYSQLI_ASSOC);
}

/**
 * Увеличивает счетчик просмотров поста,
 * при этом не учитывает просмотры самого автора
 *
 * @param mysqli $connection Соединение с БД
 * @param int $post_id ID поста
 * @return NULL
 */
function increase_post_views($connection, $user_id, $post_id)
{
    $check_author_not_user_query = "SELECT IF(author_id = ?, true, false) FROM posts WHERE id = ?";
    $is_author = secure_query_bind_result($connection, $check_author_not_user_query, true, $user_id, $post_id);
    if (!$is_author) {
        $update_post_view_count_query = "UPDATE posts SET view_count = view_count + 1 WHERE id = ?";
        secure_query_bind_result($connection, $update_post_view_count_query, false, $post_id);
    }

    return null;
}

/**
 * Добавляет/удаляет лайк посту
 *
 * @param mysqli $connection Соединение с БД
 * @param int $user_id ID пользователя
 * @param int $post_id ID поста
 * @return NULL
 */
function like_post(mysqli $connection, int $user_id, int $post_id)
{
    $sql = "SELECT COUNT(*) AS amount FROM likes WHERE user_id = ? AND post_id = ?";
    $amount = secure_query_bind_result($connection, $sql, true, $user_id, $post_id);

    if ($amount === 0) {
        $sql = "INSERT INTO likes SET user_id = ?, post_id = ?";
    } else {
        $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
    }
    secure_query_bind_result($connection, $sql, false, $user_id, $post_id);
    return null;
}

/**
 * Удаляет значения для выбранного поля (игнорирует его)
 *
 * @param array $form Массив полей-значений из формы
 * @param string $field_name Название поля
 * @return array Полученный массив
 */
function ignore_field(array $form, string $field_name)
{
    unset($form['errors'][$field_name]);
    unset($form['values'][$field_name]);
    return $form;
}

/**
 * Проводит сохранение нового поста
 *
 * @param mysqli $connection Соединение с БД
 * @param array $post Данные поста
 * @param array $post_types Возможные типы постов
 * @param array $user Данные автора поста
 * @param mixed $file_url Путь к файлу
 * @return int ID поста в БД
 */
function save_post(mysqli $connection, array $post, array $post_types, array $user, $file_url = null)
{
    $post_id = null;
    $post_type = $post['form-type'];
    $current_time = date('Y-m-d H:i:s');
    $fields = [
        'heading',
        'author_id',
        'post_type',
        'content',
        'view_count',
        'dt_add',
    ];

    $parameters = [
        $post['heading'],
        $user['id'],
        $post_types[$post_type],
        $post['content'] ?? null,
        0,
        $current_time,
    ];

    if ($post_type === 'link') {
        $parameters[3] = $post['link-url'];
    }

    if ($post_type === 'quote') {
        array_push($fields, 'quote_author');
        array_push($parameters, $post['quote-author']);
    }

    if ($post_type === 'video') {
        array_push($fields, 'youtube_url');
        array_push($parameters, $post['video-url']);
    }

    if ($post_type === 'photo') {
        array_push($fields, 'img_url');
        array_push($parameters, $file_url);
    }

    $finalFields = [];
    foreach ($fields as $field) {
        $finalFields[] = "{$field} = ?";
    }
    $fields = implode(', ', $finalFields);
    $query = "INSERT INTO posts SET {$fields}";
    secure_query_bind_result($connection, $query, false, ...$parameters);
    $post_id = mysqli_insert_id($connection);
    secure_query_bind_result($connection, "UPDATE posts SET original_post = ? WHERE id = " . $post_id, false, $post_id);
    return $post_id;
}

/**
 * Сохраняет теги для поста + добавляет новые, если таких тегов еще нет.
 *
 * @param mixed $new_tags Строка с новыми тегами
 * @param mixed $post_id ID поста
 * @param mixed $connection Соединение с БД
 * @return NULL
 */
function add_tags(string $new_tags, $post_id, $connection)
{
    $new_tags = array_unique(explode(' ', htmlspecialchars($new_tags)));
    $select_tags_query = "SELECT * FROM hashtags WHERE tag_name in ('" . implode("','", $new_tags) . "')";
    $tags_mysqli = mysqli_query($connection, $select_tags_query);
    $tags = mysqli_fetch_all($tags_mysqli, MYSQLI_ASSOC);

    foreach ($new_tags as $new_tag) {
        $index = array_search($new_tag, array_column($tags, 'tag_name'));
        if ($index !== false) {
            unset($new_tags[$new_tag]);
            $tag_id = $tags[$index]['id'];
        } else {
            secure_query_bind_result($connection, "INSERT INTO hashtags SET tag_name = ?", false, $new_tag);
            $tag_id = mysqli_insert_id($connection);
        }
        secure_query_bind_result(
            $connection,
            "INSERT INTO post_tags SET post_id = ?, hashtag_id = ?",
            false,
            $post_id,
            $tag_id
        );
    }
    return null;
}

/**
 * Сохраняет изображение из формы или скачивает изображение по ссылке
 *
 * @param array $form Массив с данными формы
 * @param string $img_folder Путь сохранения изображения
 * @return string Имя файла
 */
function upload_file(array $form, string $img_folder)
{
    if (isset($form['values']['photo-file'])) {
        return save_image('photo-file', $img_folder);
    }

    $downloadedFileContents = file_get_contents($_POST['photo-url']);
    $file_name = basename($_POST['photo-url']);
    $file_path = $img_folder . $file_name;
    file_put_contents($file_path, $downloadedFileContents);

    return $file_name;
}

/**
 * Сохраняет комментарий в БД
 *
 * @param mysqli $connection Соединение с БД
 * @param int $user_id ID пользователя
 * @param int $post_id ID поста
 * @param string $comment Комментарий
 * @return NULL
 */
function post_comment(mysqli $connection, int $user_id, int $post_id, string $comment)
{
    $add_comment_query = "INSERT INTO comments SET user_id = ?, post_id = ?, dt_add = ?, content = ?";
    $current_time = date('Y-m-d H:i:s');

    return secure_query_bind_result(
        $connection,
        $add_comment_query,
        false,
        $user_id,
        $post_id,
        $current_time,
        $comment
    );
}

/**
 * Получает из БД количество постов,
 *
 * @param mysqli $connection Соединение с БД
 * @param mixed $filter Фильтр по типу контента
 * @return int Количество постов
 */
function get_total_posts(mysqli $connection, $filter)
{
    $count_posts_query = "SELECT COUNT(*) FROM posts
        INNER JOIN content_types ON posts.post_type=content_types.id ";

    if ($filter !== null) {
        $count_posts_query .= "WHERE content_types.type_class = '$filter' ";
    }

    $total_posts_mysqli = mysqli_query($connection, $count_posts_query);

    return mysqli_fetch_row($total_posts_mysqli)[0];
}

/**
 * Получает из БД список популярных постов
 *
 * @param mysqli $connection Соединение с БД
 * @param mixed $filter Фильтр по типу контента
 * @param string $sort Порядок сортировки по лайкам/дате/просмотрам
 * @param bool $reverse Направление сортировки по возврастанию/убыванию
 * @param int $page_limit Количетство постов на страницу
 * @param int $page_offset Сколько постов пропускаем
 * @return array Список постов
 */
function get_popular_posts(mysqli $connection, $filter, string $sort, bool $reverse, int $page_limit, int $page_offset)
{
    $order = $reverse ? 'ASC' : 'DESC';
    $select_posts_query =
        "SELECT
            posts.*,
            users.id AS user_id,
            users.username,
            users.avatar,
            content_types.type_class,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comments
        FROM posts
        INNER JOIN users ON posts.author_id=users.id
        INNER JOIN content_types ON posts.post_type=content_types.id ";

    if ($filter !== null) {
        $select_posts_query .= "WHERE content_types.type_class = '$filter' ";
    }

    $select_posts_query .= "ORDER BY $sort $order LIMIT ? OFFSET ?;";
    $posts_mysqli = secure_query_bind_result($connection, $select_posts_query, false, $page_limit, $page_offset);

    return mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
}

/**
 * Получает из БД данные профиля
 *
 * @param mysqli $connection Соединение с БД
 * @param int $profile_id ID Профиля
 * @return array|null Данные профиля
 */
function get_profile(mysqli $connection, $profile_id)
{
    $select_profile_query =
        "SELECT
            users.id,
            users.username,
            users.avatar,
            users.dt_add,
            (SELECT COUNT(*) FROM subscribe WHERE subscribe.author_id = users.id) AS followers
        FROM users
        WHERE users.id = ?
        GROUP BY users.id";
    $profile_mysqli = secure_query_bind_result($connection, $select_profile_query, false, $profile_id);

    return mysqli_fetch_assoc($profile_mysqli);
}

/**
 * Получает список постов пользователя - владельца профиля
 *
 * @param mysqli $connection Соединение с БД
 * @param int $profile_id ID профиля
 * @return array Список постов
 */
function get_profile_posts(mysqli $connection, int $profile_id)
{
    $select_user_posts_query =
        "SELECT
            posts.*,
            users.id AS user_id,
            users.username,
            users.avatar,
            content_types.type_class,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes
        FROM posts
        INNER JOIN users ON posts.author_id=users.id
        INNER JOIN content_types ON posts.post_type=content_types.id
        WHERE posts.author_id = ?
        GROUP BY posts.id
        ORDER BY dt_add DESC;";
    $posts_mysqli = secure_query_bind_result($connection, $select_user_posts_query, false, $profile_id);
    $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    if (!empty($posts ?? null)) {
        foreach ($posts as &$post) {
            $post = array_merge($post, get_hashtags($connection, $post['id']));
            $post = array_merge($post, count_reposts($connection, $post));
        }
    }
    return $posts;
}

/**
 * Получает список лайков, поставленных пользователю
 *
 * @param mysqli $connection Соединение с БД
 * @param int $profile_id ID профиля
 * @return array Список лайков(постов)
 */
function get_profile_likes(mysqli $connection, int $profile_id)
{
    $select_profile_likes =
        "SELECT
            likes.user_id,
            likes.post_id,
            posts.heading,
            posts.content,
            posts.youtube_url,
            posts.img_url,
            users.id AS user_id,
            users.username,
            users.avatar,
            content_types.type_class
        FROM likes
        INNER JOIN posts ON posts.id = likes.post_id AND posts.author_id = ?
        INNER JOIN content_types ON posts.post_type=content_types.id
        INNER JOIN users ON users.id = likes.user_id";
    $likes_mysqli = secure_query_bind_result($connection, $select_profile_likes, false, $profile_id);

    return mysqli_fetch_all($likes_mysqli, MYSQLI_ASSOC);
}

/**
 * Получает список подписчиков пользователя
 *
 * @param mysqli $connection Соединение с БД
 * @param int $user_id пользователя
 * @param int $profile_id провиля
 * @return array Список подписчиков
 */
function get_profile_subscribes(mysqli $connection, int $user_id, int $profile_id)
{
    $select_profile_subscribes =
        "SELECT
            users.id AS user_id,
            users.avatar,
            users.username,
            users.dt_add,
            (SELECT COUNT(*) FROM posts WHERE posts.author_id = users.id) AS post_count,
            COALESCE(user_subscribe, 0) AS user_subscribe
        FROM subscribe
        INNER JOIN users ON subscribe.author_id = users.id
        LEFT JOIN (SELECT author_id, follower_id AS user_subscribe FROM subscribe WHERE follower_id = ?) user_subscribed
        ON user_subscribed.author_id = users.id
        WHERE subscribe.follower_id = ?";
    $subscribes_mysqli = secure_query_bind_result(
        $connection,
        $select_profile_subscribes,
        false,
        $user_id,
        $profile_id
    );

    return mysqli_fetch_all($subscribes_mysqli, MYSQLI_ASSOC);
}

/**
 * Поиск постов по ключевым словам
 *
 * @param mysqli $connection Соединение с БД
 * @param string $keywords
 * @return array Список постов
 */
function search_posts(mysqli $connection, string $keywords)
{
    $search_query =
        "SELECT
            posts.*,
            users.username,
            users.avatar,
            content_types.type_class,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comments
        FROM posts
        INNER JOIN users ON posts.author_id=users.id
        INNER JOIN content_types ON posts.post_type=content_types.id ";
    $search_by_tag_query = $search_query . "INNER JOIN post_tags
    INNER JOIN hashtags ON hashtags.id = post_tags.hashtag_id
    WHERE posts.id = post_tags.post_id AND hashtags.tag_name = ?";
    $search_by_keywords_query = $search_query . "WHERE MATCH(heading,content) AGAINST(?)";
    $search_results_mysqli =
        (substr($keywords, 0, 1) === '#')
            ? secure_query_bind_result($connection, $search_by_tag_query, false, substr($keywords, 1))
            : secure_query_bind_result($connection, $search_by_keywords_query, false, $keywords);
    $posts = mysqli_fetch_all($search_results_mysqli, MYSQLI_ASSOC);
    if (!empty($posts ?? null)) {
        foreach ($posts as &$post) {
            $post = array_merge($post, get_hashtags($connection, $post['id']));
        }
    }
    return $posts;
}

/**
 * Получает список диалогов - пользователей от которых или которым есть сообщения
 *
 * @param mixed $connection
 * @param mixed $user_id Пользователь
 * @return array Список диалогов
 */
function get_dialogs($connection, $user_id)
{
    $select_dialogs_query =
        "SELECT
            dialog,
            username,
            avatar,
            content,
            sender_id,
            last_message
        FROM messages
        INNER JOIN (SELECT MAX(dt_add) AS last_message,
        IF(receiver_id = ?, sender_id, receiver_id) AS dialog
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY dialog) grps
        ON messages.dt_add = grps.last_message
        INNER JOIN users
        ON users.id = dialog
        ORDER BY last_message DESC ";
    $dialogs_assoc = [];
    $dialogs_mysqli = secure_query_bind_result($connection, $select_dialogs_query, false, $user_id, $user_id, $user_id);
    while ($dialogs = mysqli_fetch_array($dialogs_mysqli, MYSQLI_ASSOC)) {
        $dialogs_assoc[$dialogs['dialog']] = array_slice($dialogs, 1);
        $dialogs_assoc[$dialogs['dialog']]['messages'] = [];
    }
    return $dialogs_assoc;
}

/**
 * Получает список отправленных и полученных сообщений пользователя
 *
 * @param mixed $connection
 * @param mixed $user_id Пользователь
 * @return array Список сообщений
 */
function get_messages($connection, $user_id)
{
    $select_messages_query =
        "SELECT
            content,
            dt_add,
            sender_id,
            receiver_id,
        IF (receiver_id = ?, sender_id, receiver_id) AS dialog
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        ORDER BY dt_add ASC";
    $messages_mysqli = secure_query_bind_result(
        $connection,
        $select_messages_query,
        false,
        $user_id,
        $user_id,
        $user_id
    );

    return mysqli_fetch_all($messages_mysqli, MYSQLI_ASSOC);
}

/**
 * Добавляет сообщение в БД
 *
 * @param mysqli $connection
 * @param mixed $sender_id Отправитель
 * @param mixed $receiver_id Получатель
 * @param mixed $message Сообщение
 * @return mixed Результат выполнения запроса
 */
function add_message($connection, $sender_id, $receiver_id, string $message)
{
    $add_message_query = "INSERT INTO messages SET sender_id = ?, receiver_id = ?, dt_add = ?, content = ?, was_read = 0";
    $message = trim($message);
    $current_time = date('Y-m-d H:i:s');

    return secure_query_bind_result(
        $connection,
        $add_message_query,
        false,
        $sender_id,
        $receiver_id,
        $current_time,
        $message
    );
}

/**
 * Возвращает список подписчиков заданного пользователя
 *
 * @param mysqli $connection Соединение с БД
 * @param int $author_id ID автора
 * @return array Список подписчиков
 */
function get_user_followers(mysqli $connection, $author_id): array
{
    $select_followers_query = "SELECT users.username, users.email FROM users
        INNER JOIN subscribe ON users.id = subscribe.follower_id
        WHERE subscribe.author_id = ?";
    $followers_mysqli = secure_query_bind_result($connection, $select_followers_query, false, $author_id);

    return mysqli_fetch_all($followers_mysqli, MYSQLI_ASSOC);
}

/**
 * Возвращает количество не прочитанных пользователем сообщений
 *
 * @param mysqli $connection Соединение с БД
 * @param int $user_id ID пользователя
 * @return int Количество не прочитанных сообщений
 */
function count_new_messages(mysqli $connection, $user_id)
{
    $count_messages_query = "SELECT COUNT(*) AS amount FROM messages WHERE receiver_id = ? AND was_read = false";

    return secure_query_bind_result($connection, $count_messages_query, true, $user_id);
}

/**
 * Ставит отметку о прочтении, если пользователя открыл диалог с новыми сообщениями
 *
 * @param mysqli $connection Соединение с БД
 * @param int $active_dialog_id ID открытого диалога (= ID другого пользователя с которым диалог)
 * @param int $user_id ID пользователя
 * @return NULL
 */
function read_messages(mysqli $connection, $active_dialog_id, $user_id)
{
    $read_messages_query = "UPDATE messages SET was_read = true WHERE sender_id = ? AND receiver_id = ?";
    secure_query_bind_result($connection, $read_messages_query, false, $active_dialog_id, $user_id);

    return null;
}

/**
 * Меняет направление сортировки/при переключении между фильтрами не меняет значение
 *
 * @param bool|NULL $direction Текущее направление
 * @param array $params Прошлые параметры фильтра и сортировки
 * @param string|NULL Текущая сортировка по лайкам/дате/просмотрам
 * @param string|NULL Текущий фильтр
 * @return bool|NULL Возвращает противоположное значение полученному, либо NULL
 */
function get_reverse($direction, array $params, $sort, $filter)
{
    $params['sort'] = $params['sort'] ?? 'view_count';
    $params['filter'] = $params['filter'] ?? null;
    if (isset($direction)) {
        if (($params['sort'] === $sort) && ($params['filter'] === $filter)) {
            return $direction ? false : true;
        } elseif (($params['sort'] === $sort) && ($params['filter'] !== $filter)) {
            return $direction;
        }
    }
    return false;
}

/**
 * Производит подключение к почтовому серверу
 *
 * @param array $settings Настройки подключения к серверу
 * @param string $site_name Имя сайта
 * @return NULL
 */
function apply_mail_settings(array $settings, string $site_name)
{
    if (!$settings['encryption']) {
        $transport = new Swift_SmtpTransport($settings['server'], $settings['port']);
    } else {
        $transport = new Swift_SmtpTransport($settings['server'], $settings['port'], $settings['encryption']);
    }
    $transport->setUsername($settings['user']);
    $transport->setPassword($settings['password']);
    $mailer = new Swift_Mailer($transport);

    return $mailer;
}

/**
 * Отправляет уведомление о новом подписчике
 *
 * @param string $sender Отправитель писем с сервера
 * @param array $owner Массив с именем и email пользователя
 * @param array $follower Массив с данными подписчика
 * @param object $mailer Объект Swift_Mailer
 * @return NULL
 */
function new_follower_notification($sender, $owner, $follower, $mailer)
{
    global $site_name;
    $subject = 'У вас новый подписчик';
    $message = new Swift_Message($subject);
    $message->setFrom($sender, $site_name);
    $body = "Здравствуйте, " . $owner['username'] . ". На вас подписался новый пользователь " . $follower['name'] .
        " Вот ссылка на его профиль: " . ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' .
        $_SERVER['HTTP_HOST'] . "/profile.php?id=" . $follower['id'];
    $message->setTo($owner['email']);
    $message->setBody($body);
    $mailer->send($message);

    return null;
}

/**
 * Отправляет уведомление о новом посте
 *
 * @param string $sender Отправитель писем с сервера
 * @param array $post_author Массив с именем и email автора поста
 * @param array $mailing_list Массив со списком получателей
 * @param string $post_heading Заголовок поста
 * @param int $post_id ID поста
 * @param object $mailer Объект Swift_Mailer
 * @return NULL
 */
function new_post_notification($sender, $post_author, $mailing_list, $post_heading, $post_id, $mailer)
{
    $subject = "Новая публикация от пользователя " . $post_author['name'];
    $message = new Swift_Message($subject);
    $message->setFrom($sender, "ReadMe");
    foreach ($mailing_list as $reciever) {
        $body = "Здравствуйте, " . $reciever['username'] . ". Пользователь " . $post_author['name'] .
            " только что опубликовал новую запись " . "«" . $post_heading . "». " .
            "Посмотрите её на странице пользователя: " . ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' .
            $_SERVER['HTTP_HOST'] . "/profile.php?id=" . $post_author['id'];
        $message->setTo([$reciever['email']]);
        $message->setBody($body);
        $mailer->send($message);
    }
    return null;
}
