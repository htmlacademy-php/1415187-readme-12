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

function time_difference ($time, $current_time) {
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
 * @param  mysqli $con Данные для соединения с БД
 * @param  string $sql Исходный запрос с плейсхолдерами
 * @param  mixed  $params Типы параметров в формате 'i' - integer,'s' - string
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
 * @param  string $type Тип контента
 *
 * @return array Массив из двух значений: ширина и высота иконки.
 */

function filter_size_ico($type) {
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

function get_content_types($con) {
    $result = mysqli_query($con, "SELECT * FROM content_types");
    $content_types = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $content_types;
}

/**
 * Добавляет новый пост из списка постов. Версия для цитаты
 *
 * @param mysqli $con Параметры соединения с БД
 * @param string $heading Заголовок поста
 * @param int $form_type Тип поста
 * @param string $content Основное одержимое поста
 * @param string $author Автор цитаты
 *
 */

function form_add_post_quote ($con, $heading, $form_type, $content, $author) {
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
 *
 */

function form_add_post_text ($con, $heading, $form_type, $content) {
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
 *
 */

function form_add_post_link ($con, $heading, $form_type, $link) {
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
 *
 */

function form_add_post_video ($con, $heading, $form_type, $content, $youtube_link) {
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
 *
 */

function form_add_post_photo ($con, $heading, $form_type, $content) {
    if (isset($form['values']['photo-file'])) {
        $file_name = $form['values']['photo-file']['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES['photo-file']['tmp_name'], $file_path . $file_name);
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
 * @param array $new_tags Массив из тегов, указанных при создании поста
 * @param int $post_id ID поста, к которому добавляются теги
 */

function form_add_post_tags ($con, $post_id, $new_tags) {
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

function getValidationRules(array $rules): array {
    $result = [];
    foreach ($rules as $fieldName => $rule) {
        $result[$fieldName] = explode('|', $rule);
    }
    return $result;
}

/**
 * Формирует имя функции валидации для дальнейшего вызова
 *
 * @param  string $name Название метода валидации
 * @return string Имя функции валидации
 */

function getValidationMethodName(string $name): string {
    $studlyWords = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    return "validate{$studlyWords}";
}

/**
 * Разделяет название метода валидации и его параметры
 *
 * @param  string $rule Связка правило-параметры
 * @return array Массив из названия и массива параметров
 */

function getValidationNameAndParameters(string $rule): array {
    $nameParams = explode(':', $rule);
    $parameters = [];
    $name = $nameParams[0];
    if (isset($nameParams[1])) {
        $parameters = explode(',', $nameParams[1]);
    }
    return [$name, $parameters];
}

/**
 * Валидация заполненого поля
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Параметр, по которому будет проводиться валидация
 * @return string Ошибка или null
 */

function validateFilled(array $inputArray, string $parameterName): ?string {
    if (empty($inputArray[$parameterName])) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/**
 * Валидация длины поля заголовка
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Имя поля
 * @return string Ошибка или null
 */

function validateLengthHeading(array $inputArray, string $parameterName): ?string {
    $len = strlen($inputArray[$parameterName]);
    if ($len < 10 or $len > 50) {
           return 'Длина поля должна быть от 10 до 50 символов';
    }
    return null;
}

/**
 * Валидация длины текстового поля
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Имя текстового поля
 * @return string Ошибка или null
 */

function validateLengthContent(array $inputArray, string $parameterName): ?string {
    $len = strlen($inputArray[$parameterName]);
    if ($len < 50 or $len > 500) {
           return 'Длина поля должна быть от 50 до 500 символов';
    }
    return null;
}

/**
 * Проверяет корректность URL-адреса
 *
 * @param array $inputArray Массив, полученный методом POST (из формы)
 * @param string $parameterName Параметр, по которому будет проводиться валидация
 * @return string Ошибка либо Null
 */

function validateCorrectURL(array $inputArray, string $parameterName): ?string {
    if (!filter_var($inputArray[$parameterName], FILTER_VALIDATE_URL)) {
        return 'Некорретный URL-адрес';
    }
    return null;
}

/**
 * Проверяет отсутствие значения в БД
 *
 * @param  array $validationArray Проверяемый массив
 * @param  string $parameterName Имя искомого параметра
 * @param  string $tableName Имя таблицы БД
 * @param  string $columnName Имя столбца таблицы
 * @param  mysqli $dbConnection Параметры подключения к БД
 * @return string Сообщение об ошибке, если нет ошибки - null
 */

function validateExists(array $validationArray, string $parameterName, $tableName, $columnName, mysqli $dbConnection): ?string {
    $sql = "SELECT COUNT(*) AS amount FROM $tableName WHERE $columnName = ?";
    $prepared_sql = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($prepared_sql, 's', $validationArray[$parameterName]);
    mysqli_stmt_execute($prepared_sql);
    mysqli_stmt_bind_result($prepared_sql, $amount);
    mysqli_stmt_fetch($prepared_sql);
    mysqli_stmt_close($prepared_sql);
    if ($amount > 0) {
        return "Запись с таким $parameterName уже присутствует в базе данных";
    }
    return null;
}

/**
 * Проверяет загружен ли файл и является ли он изображением
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Имя поля, через которое загружен файл
 * @return string Ошибка либо null
 */

function validateImgLoaded(array $inputArray, string $parameterName): ?string {
    if ($inputArray[$parameterName]['error'] != 0) {
        return 'Код ошибки:' . $inputArray[$parameterName]['error'];
    }
    else {
        if (!in_array(exif_imagetype($inputArray[$parameterName]['tmp_name']), [1, 2, 3])) {
            return 'Недопустимый тип изображения';
        }
    }
    return null;
}

/**
 * Сохраняет файл в папку "@host/uploads/"
 *
 * @param  string $img Название поля с изображением
 * @return string Путь к сохраненному файлу
 */

function save_image($img) {
    if ($_FILES[$img]['error'] != 0) {
        return $file_name = $_POST[$img];
    } else {
        $file_name = $_FILES[$img]['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES[$img]['tmp_name'], $file_path . $file_name);
        return $file_url;
    }
}

/**
 * Проверяет наличие по ссылке изображения
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Имя поля, содержащего ссылку на изображение
 * @return string Ошибка либо null
 */

function validateImageURLContent(array $inputArray, string $parameterName): ?string {
    if (!file_get_contents($inputArray[$parameterName])) {
        return 'По ссылке отсутствует изображение';
    }
    else {
        if (!in_array(@exif_imagetype($inputArray[$parameterName]), [1, 2, 3])) {
            return 'Недопустимый тип изображения';
        }
    }
    return null;
}

/**
 * Проверяет, что переданная ссылка ведет на доступное видео с youtube
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param string $parameterName Ссылка на youtube видео
 * @return string Доступна или недоступна ссылка
 */

function validateYoutubeURL(array $inputArray, string $parameterName): ?string {
    $id = extract_youtube_id($inputArray[$parameterName]);

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
 * @param  mysqli $db_connection Соединение с БД
 * @param  array $fields Проверяемый массив связками поле - значение
 * @param  array $validationArray Массив правил валидации вида поле - список правил валидации
 * @return array Массив со списком ошибок
 */

function validate($fields, $validationArray, $db_connection) {
    $validations = getValidationRules($validationArray);
    $errors = [];
    foreach ($validations as $field => $rules) {
        foreach ($rules as $rule) {
            [$name, $parameters] = getValidationNameAndParameters($rule);
            $methodName = getValidationMethodName($name);
            $methodParameters = array_merge([$fields, $field], $parameters);
            if (!function_exists($methodName)) {
                return 'Функции валидации ' . $methodName . ' не существует';
            }
            if ($methodName == 'validateExists') {
                array_push($methodParameters, $db_connection);
            }
            if ($errors[$field] = call_user_func_array($methodName, $methodParameters)) {
                break;
            }
        }
    }
    return $errors;
}

/**
 * Проверяет совпадение вводов пароля
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @return string Ошибка или null
 */

function validateRepeatPassword(array $inputArray): ?string {
    if ($inputArray['password'] !== $inputArray['password-repeat']) {
        return 'Пароли не совпадают';
    }
    return null;
}

/**
 * Проверяет корректность введенного email-адреса
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Проверяемый параметр, email
 * @return string Ошибка или null
 */

function validateCorrectEmail(array $inputArray, string $parameterName): ?string {
    if (!filter_var($inputArray[$parameterName], FILTER_VALIDATE_EMAIL)) {
        return 'Некорретный email';
    }
    return null;
}

/**
 * Проверяет корректность введенного email-адреса
 *
 * @param  array $inputArray Массив, полученный методом POST (из формы)
 * @param  string $parameterName Проверяемый параметр, email
 * @return string Ошибка или null
 */

function db_connect($host, $user, $pass, $db) {
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
