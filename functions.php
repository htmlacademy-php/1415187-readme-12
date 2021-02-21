<?php

function cut_text (string $text, int $length = 300) {
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length + 1);
        $end = mb_strlen(strrchr($text, ' '));
        $text = mb_substr($text, 0, -$end) . '...';
        }
    return $text;
}

function time_difference ($post_time, $current_time) {
    date_default_timezone_set('Europe/Moscow');
    
    $diff = date_diff($current_time, $post_time);

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

function secure_query(mysqli $con, string $sql, string $type, ...$params) {
    $prepared_sql = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($prepared_sql, $type, ...$params);
    mysqli_stmt_execute($prepared_sql);
    return mysqli_stmt_get_result($prepared_sql);
}

function display_404_page() {
    $page_content = include_template('404.php');
    $layout_content = include_template('layout.php',['content' => $page_content]);
    print($layout_content);
    http_response_code(404);
}

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

function validateFilled($var) {
    if (empty($_POST[$var])) {
        return 'Это поле должно быть заполнено';
    }
}

function validateURL($var) {
    if (!filter_var($_POST[$var], FILTER_VALIDATE_URL)) {
        return 'Некорретный URL-адрес';
    }
}

function validateImageURLContent($var) {
    if (!$content = @file_get_contents($_POST[$var])) {
        return 'По ссылке отсутствует изображение';
    }
}





function validateImageFields($var) {
    if (isset($_GET['photo-url']) || (isset($_GET['photo-file']))) {
       if ((!$content = @file_get_contents($_POST[$var]))&&(isset($_GET['photo-url']))) {
        return 'По ссылке отсутствует изображение';
    }
        if (($file['error'] != 0) && (isset($_GET['photo-file']))) {
        return 'Ошибка загрузки файла / файл не получен';
    } 
        elseif (isset($_GET['photo-file'])) {
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_name = $file['tmp_name'];
            $file_type = finfo_file($file_info, $file_name);
            if (!in_array($file_type, ['image/png','image/jpeg', 'image/gif'])) {
                return 'Недопустимый тип изображения';
            }
        }
    }
}






function validateImageFile($file) {
    if ($file['error'] != 0) {
        return 'Ошибка загрузки файла / файл не получен';
    } else {
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $file['tmp_name'];
        $file_type = finfo_file($file_info, $file_name);
        if (!in_array($file_type, ['image/png','image/jpeg', 'image/gif'])) {
            return 'Недопустимый тип изображения';
        }
    }
}

function validate($field, $validation_rules) {
    foreach ($validation_rules as $validation_rule) {
        if (!function_exists($validation_rule)) {
            return 'Функции валидации ' . $validation_rule. ' не существует';
        }
        return $validation_rule($field);
    }
}