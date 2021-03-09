<?php

/**
 * Возвращает укороченную версию текста с многоточием, если длина больше указанной.
 *
 * @param string $text Полный текст
 * @param int $length Длина укороченного текста
 *
 * @return string Укороченный текст
 */

function cut_text (string $text, int $length = 300) {
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
 * @param DateTime $time Дата/время отсчета
 * @param DateTime $current_time Текущая дата/время
 *
 * @return string Относительное время в общем формате (прим.: "4 дня назад", "3 недели назад")
 */

function time_difference (DateTime $time, DateTime $current_time) {
    date_default_timezone_set('Europe/Moscow');

    $diff = date_diff($current_time, $time);

    if ($diff->y > 0) {
        $relative_time = $diff->y . ' ' .
            get_noun_plural_form($diff->y, 'год', 'года', 'лет') . ' назад';
    }
    elseif ($diff->m > 0) {
        $relative_time = $diff->m . ' ' .
            get_noun_plural_form($diff->m, 'месяц', 'месяца', 'месяцев') . ' назад';
    }
    elseif ($diff->d > 6) {
        $relative_time = floor(($diff->d)/7) . ' ' .
            get_noun_plural_form(floor(($diff->d)/7),' неделю', ' недели', ' недель') . ' назад';
    }
    elseif ($diff->d > 0) {
        $relative_time = $diff->d . ' ' .
            get_noun_plural_form($diff->d, 'день', 'дня', 'дней') . ' назад';
    }
    elseif ($diff->h > 0) {
        $relative_time = $diff->h . ' ' .
            get_noun_plural_form($diff->h, 'час', 'часа', 'часов') . ' назад';
    }
    elseif ($diff->i > 0) {
        $relative_time = $diff->i . ' ' .
            get_noun_plural_form($diff->i, 'минуту', 'минуты', 'минут') . ' назад';
    }
    elseif ($diff->s >= 0) {
        $relative_time = 'Только что';
    }
    else {
        $relative_time = '';
    }
    return $relative_time;
}

/**
 * Подготавливает и выполняет "безопасный" запрос
 *
 * @param mysqli $con Данные для соединения с БД
 * @param string $sql Исходный запрос с плейсхолдерами
 * @param string $type Типы параметров в формате 'i' - integer,'s' - string
 * @param mixed $params Передаваемые параметры
 *
 * @return false|mysqli_result  Результат выполнения подготовленного запроса
 */

function secure_query(mysqli $con, string $sql, string $type, ...$params) {
    $prepared_sql = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($prepared_sql, $type, ...$params);
    mysqli_stmt_execute($prepared_sql);
    return mysqli_stmt_get_result($prepared_sql);
}

/**
 * Создает страницу для ошибки 404
 */

function display_404_page() {
    $page_content = include_template('404.php');
    $layout_content = include_template('layout.php',['content' => $page_content]);
    print($layout_content);
    http_response_code(404);
}

/**
 * Выбирает размер иконки "Тип контента" (размеры взяты из разметки)
 *
 * @param string $type Тип контента
 *
 * @return array Массив из двух значений: ширина и высота иконки.
 */

function filter_size_ico(string $type) {
    if ($type == 'photo') {
        $result = ['w' => 22, 'h' => 18];
    }
    elseif ($type == 'video') {
        $result = ['w' => 24, 'h' => 16];
    }
    elseif ($type == 'text') {
        $result = ['w' => 20, 'h' => 21];
    }
    elseif ($type == 'quote') {
        $result = ['w' => 21, 'h' => 20];
    }
    elseif ($type == 'link') {
        $result = ['w' => 21, 'h' => 18];
    }
    else {
        $result = ['w' => '', 'h' => ''];
    }
    return($result);
}

/**
 * Создает список типов поста
 *
 * @param mysqli $con Соединение с базой
 * @return array Список типов
 */

function get_content_types(mysqli $con) {
    $result = mysqli_query($con, "SELECT * FROM content_types");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Добавляет новый пост из списка постов. Версия для цитаты
 *
 * @param mysqli $con Параметры соединения с БД
 * @param string $heading Заголовок поста
 * @param int $form_type Тип поста
 * @param string $content Основное одержимое поста
 * @param string $author Автор цитаты
 */

function form_add_post_quote (mysqli $con, string $heading, int $form_type, string $content, string $author) {
    $add_post_query = "INSERT INTO posts SET heading = ?, post_type = ?, content = ?, author_id = 1, view_count = 0, quote_author = ?";
    secure_query($con, $add_post_query, 'siss', $heading, $form_type, $content, $author);
}

/**
 * Добавляет новый пост из списка постов. Версия для текста
 *
 * @param mysqli $con Параметры соединения с БД
 * @param string $heading Заголовок поста
 * @param int $form_type Тип поста
 * @param string $content Основное одержимое поста
 */

function form_add_post_text (mysqli $con, string $heading, int $form_type, string $content) {
    $add_post_query = "INSERT INTO posts SET heading = ?, post_type = ?, content = ?, author_id = 1, view_count = 0";
    secure_query($con, $add_post_query, 'sis', $heading, $form_type, $content);
}

/**
 * Добавляет новый пост из списка постов. Версия для ссылки
 *
 * @param mysqli $con Параметры соединения с БД
 * @param string $heading Заголовок поста
 * @param int $form_type Тип поста
 * @param string $link Ссылка на внешний ресурс
 */

function form_add_post_link (mysqli $con, string $heading, int $form_type, string $link) {
    $add_post_query = "INSERT INTO posts SET heading = ?, post_type = ?, content = ?, author_id = 1, view_count = 0";
    secure_query($con, $add_post_query, 'sis', $heading, $form_type, $link);
}

/**
 * Добавляет новый пост из списка постов. Версия для видео
 *
 * @param mysqli $con Параметры соединения с БД
 * @param string $heading Заголовок поста
 * @param int $form_type Тип поста
 * @param string $content Основное одержимое поста
 * @param string $youtube_link Ссылка на видео с youtube
 */

function form_add_post_video (mysqli $con, string $heading, int $form_type, string $content, string $youtube_link) {
    $add_post_query = "INSERT INTO posts SET heading = ?, post_type = ?, content = ?, author_id = 1, view_count = 0, youtube_url = ?";
    secure_query($con, $add_post_query, 'siss', $heading, $form_type, $content, $youtube_link);
}

/**
 * Добавляет новый пост из списка постов. Версия для фото
 *
 * @param mysqli $con Параметры соединения с БД
 * @param string $heading Заголовок поста
 * @param int $form_type Тип поста
 * @param string $content Основное одержимое поста
 * @param array $file Массив с содержанием файла
 */

function form_add_post_photo (mysqli $con, string $heading, int $form_type, string $content, array $file) {
    if (file_exists($file['tmp_name'])) {
        $file_url = save_image('photo-file');
    }
    else {
        $file_url = $_POST['photo-url'];
    }
    $add_post_query = "INSERT INTO posts SET heading = ?, post_type = ?, content = ?, author_id = 1, view_count = 0, img_url = ?";
    secure_query($con, $add_post_query, 'siss', $heading, $form_type, $content, $file_url);
}

/**
 * Добавляет новый пост из списка постов. Версия для цитаты
 *
 * @param mysqli $con Параметры соединения с БД
 * @param int $post_id ID поста, к которому добавляются теги
 * @param array $new_tags Массив из тегов, указанных при создании поста
 */

function form_add_post_tags (mysqli $con, int $post_id, array $new_tags) {
    $select_tags_query = "SELECT * FROM hashtags WHERE tag_name in ('".implode("','",$new_tags)."')";
    $tags_mysqli = mysqli_query($con, $select_tags_query);
    $tags = mysqli_fetch_all($tags_mysqli, MYSQLI_ASSOC);

    foreach ($new_tags as $new_tag) {
        $index = array_search($new_tag, array_column($tags, 'tag_name'));

        if ($index !== false) {
            unset($new_tags[$new_tag]);
            $tag_id = $tags[$index]['id'];
        }
        else {
            $add_tag_query = "INSERT into hashtags SET tag_name = ?";
            secure_query($con, $add_tag_query, 's', $new_tag);
            $tag_id = mysqli_insert_id($con);
        }
        $add_post_tag_query = "INSERT INTO post_tags SET post_id = ?, hashtag_id = ?";
        secure_query($con, $add_post_tag_query, 'ii', $post_id, $tag_id);
    }
}

/**
 * Разделяет строку с правилами валидации на отдельные првила
 *
 * @param  array $rules Массив со всеми правилами валидации и их параметрами
 * @return array Массив, в котором каждая связка правило-параметры - отдельный элемент
 */

function get_validation_rules(array $rules): array {
    $result = [];
    foreach ($rules as $field_name => $rule) {
        $result[$field_name] = explode('|', $rule);
    }
    return $result;
}

/**
 * Формирует имя функции валидации для дальнейшего вызова
 *
 * @param  string $name Название метода валидации
 * @return string Имя функции валидации
 */

function get_validation_method_name(string $name): string {
    $studly_words = str_replace(['-', ' '], '_', $name);
    return "validate_{$studly_words}";
}

/**
 * Разделяет название метода валидации и его параметры
 *
 * @param  string $rule Связка правило-параметры
 * @return array Массив из названия и массива параметров
 */

function get_validation_name_and_parameters(string $rule): array {
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
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param  string $parameter_name Параметр, по которому будет проводиться валидация
 * @return string Ошибка или null
 */

function validate_filled(array $input_array, string $parameter_name): ?string {
    if (empty($input_array[$parameter_name])) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/**
 * Валидация длины поля заголовка
 *
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param  string $parameter_name Имя поля
 * @return string Ошибка или null
 */

function validate_length_heading(array $input_array, string $parameter_name): ?string {
    $len = strlen($input_array[$parameter_name]);
    if ($len < 10 or $len > 50) {
           return 'Длина поля должна быть от 10 до 50 символов';
    }
    return null;
}

/**
 * Валидация длины текстового поля
 *
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param  string $parameter_name Имя текстового поля
 * @return string Ошибка или null
 */

function validate_length_content(array $input_array, string $parameter_name): ?string {
    $len = strlen($input_array[$parameter_name]);
    if ($len < 50 or $len > 500) {
           return 'Длина поля должна быть от 50 до 500 символов';
    }
    return null;
}

/**
 * Проверяет корректность URL-адреса
 *
 * @param array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Параметр, по которому будет проводиться валидация
 * @return string Ошибка либо Null
 */

function validate_correct_url(array $input_array, string $parameter_name): ?string {
    if (!filter_var($input_array[$parameter_name], FILTER_VALIDATE_URL)) {
        return 'Некорретный URL-адрес';
    }
    return null;
}

/**
 * Проверяет загружен ли файл и является ли он изображением
 *
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param  string $parameter_name Имя поля, через которое загружен файл
 * @return string Ошибка либо null
 */

function validate_img_loaded(array $input_array, string $parameter_name): ?string {
    if ($input_array[$parameter_name]['error'] != 0) {
        return 'Файл не получен';
    }
    else {
        if (!in_array(exif_imagetype($input_array[$parameter_name]['tmp_name']), [1, 2, 3])) {
            return 'Недопустимый тип изображения';
        }
    }
    return null;
}

/**
 * Сохраняет файл в папку "@host/uploads/"
 *
 * @param string $img Название поля с изображением
 * @return string Путь к сохраненному файлу
 */

function save_image(string $img) {
    if ($_FILES[$img]['error'] != 0) {
        return $file_name = $_POST[$img];
    } else {
        $file_name = $_FILES[$img]['name'];
        $file_path = __DIR__ . '/uploads/';
        move_uploaded_file($_FILES[$img]['tmp_name'], $file_path . $file_name);
        return '/uploads/' . $file_name;
    }
}

/**
 * Проверяет наличие по ссылке изображения
 *
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param  string $parameter_name Имя поля, содержащего ссылку на изображение
 * @return string Ошибка либо null
 */

function validate_image_url_content(array $input_array, string $parameter_name): ?string {
    if (!file_get_contents($input_array[$parameter_name])) {
        return 'По ссылке отсутствует изображение';
    }
    else {
        if (!in_array(@exif_imagetype($input_array[$parameter_name]), [1, 2, 3])) {
            return 'Недопустимый тип изображения';
        }
    }
    return null;
}

/**
 * Проверяет, что переданная ссылка ведет на доступное видео с youtube
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param string $parameter_name Ссылка на youtube видео
 * @return string Доступна или недоступна ссылка
 */

function validate_youtube_url(array $input_array, string $parameter_name): ?string {
    $id = extract_youtube_id($input_array[$parameter_name]);

    if ($id) {
        $api_data = ['id' => $id, 'part' => 'id,status', 'key' => 'AIzaSyD24lsJ4BL-azG188tHxXtbset3ehKXeJg'];
        $url = "https://www.googleapis.com/youtube/v3/videos?" . http_build_query($api_data);

        $resp = file_get_contents($url);

        if (!($resp && $json = json_decode($resp, true))) {
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
 * @param mysqli $db_connection Соединение с БД
 * @return array|string Массив со списком ошибок | Строка с ошибкой
 */

function validate(array $fields, array $validation_array, mysqli $db_connection) {
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
                array_push($method_parameters, $db_connection);
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
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @return string Ошибка или null
 */

function validate_repeat_password(array $input_array): ?string {
    if ($input_array['password'] !== $input_array['password-repeat']) {
        return 'Пароли не совпадают';
    }
    return null;
}

/**
 * Проверяет корректность введенного email-адреса
 *
 * @param  array $input_array Массив, полученный методом POST (из формы)
 * @param  string $parameter_name Проверяемый параметр, email
 * @return string Ошибка или null
 */

function validate_correct_email(array $input_array, string $parameter_name): ?string {
    if (!filter_var($input_array[$parameter_name], FILTER_VALIDATE_EMAIL)) {
        return 'Некорретный email';
    }
    return null;
}

/**
 * Подготавливает и выполняет "безопасный" запрос со связыванием (bind)
 *
 * @param mysqli $connection Данные для подключения к БД
 * @param string $sql Исходный запрос сплейсхолдерами
 * @param mixed $params Типы параметров 'i' - int,'s' - string
 * @return mixed Результат выполнения подготовленного запроса
 */
function secure_query_bind_result(mysqli $connection, string $sql, ...$params) {
    $param_types = '';
    foreach ($params as $param) {
        $param_types .= (gettype($param) == 'integer') ? 'i' : 's';
    }
    $prepared_sql = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($prepared_sql, $param_types, ...$params);
    mysqli_stmt_execute($prepared_sql);
    mysqli_stmt_bind_result($prepared_sql, $bind);
    mysqli_stmt_fetch($prepared_sql);
    mysqli_stmt_close($prepared_sql);
    return $bind;
}

/**
 * Проверяет отсутствие|наличие значения в БД
 *
 * @param array $validation_array Проверяемый массив
 * @param string $parameter_name Имя искомого параметра
 * @param array $parameter_settings Установки параметров (где искать, и ищем отсутствие или наличие)
 * @param mysqli $db_connection Параметры подключения к БД
 * @return string Сообщение об ошибке, если нет ошибки - null
 */

function validate_exists(array $validation_array, string $parameter_name, array $parameter_settings, mysqli $db_connection): ?string {
    $table_name = $parameter_settings[0];
    $column_name = $parameter_settings[1];
    $not = $parameter_settings[2];
    $sql = "SELECT COUNT(*) AS amount FROM $table_name WHERE $column_name = ?";
    $amount = secure_query_bind_result($db_connection, $sql, $validation_array[$parameter_name]);
    if (($amount > 0) && (!in_array('not', $parameter_settings))) {
        return "Запись с таким $parameter_name уже присутствует в базе данных";
    }
    elseif (($amount === 0) && (in_array('not', $parameter_settings))) {
        return "Записи с таким $parameter_name нет в базе данных";
    }
    return null;
}

/**
 * Проверяет правильность введенного пароля
 *
 * @param array $validation_array Валидируемый массив
 * @param string $parameter_name Имя искомого параметра
 * @param $parameter_settings
 * @param mysqli $db_connection Данные для подключения к БД
 * @return string Сообщение об ошибке или null
 */

function validate_correct_password(array $validation_array, string $parameter_name, $parameter_settings, mysqli $db_connection): ?string {
    $table_name = $parameter_settings[0];
    $users_column_name = $parameter_settings[1];
    $password_column_name = $parameter_settings[2];
    $sql = "SELECT $password_column_name AS db_password FROM $table_name WHERE $users_column_name = ?";
    $db_password = secure_query_bind_result($db_connection, $sql, $validation_array[$parameter_name]);
    return !password_verify($validation_array[$parameter_name], $db_password) ? "Вы ввели неверный email/пароль" : null;
}

/**
 * Производит подключение к БД. Если доступ не получен - возвращает ошибку 500
 *
 * @param string $host Местоположение БД
 * @param string $user Логин
 * @param string $pass Пароль
 * @param string $db Имя БД
 *
 * @return mysqli Результат подключения или null
 */

function db_connect(string $host,string $user,string $pass,string $db) {
    $con = mysqli_connect($host, $user, $pass, $db);

    if ($con == false) {
        $error = mysqli_connect_error();
        print($error);
        http_response_code(500);
        exit();
    }

    mysqli_set_charset($con, "utf8mb4");
    return $con;
}

function get_user_data($db_connection, $email) {
    $result = mysqli_query($db_connection, "SELECT username, avatar FROM users WHERE email = '$email'");
    $users_name = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $users_name[0];
}