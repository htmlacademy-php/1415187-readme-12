<?php
require_once(__DIR__ . '/libs/base.php');

$validation_rules = [
    'email' => 'filled|correct_email|exists:users,email',
    'login' => 'filled',
    'password' => 'filled|repeat_password',
    'password-repeat' => 'filled|repeat_password'
];

$form_error_codes = [
    'email' => 'Email',
    'login' => 'Логин',
    'password' => 'Пароль',
    'password-repeat' => 'Повторный пароль'
];

$form = [
    'values' => [],
    'errors' => [],
];

$img_folder = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;

$title = $site_name . ': Регистрация';

if (count($_POST) > 0) {
    $form['values'] = $_POST;
    $form['values']['userpic-file'] = $_FILES['userpic-file'];
    $form['errors'] = validate($form['values'], $validation_rules, $connection);
    $form['errors'] = array_filter($form['errors']);
    if (empty($form['errors'])) {
        $current_time = date('Y-m-d H:i:s');
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $avatar = save_image('userpic-file', $img_folder);
        secure_query_bind_result($connection,
            "INSERT into users SET username = ?, email = ?, password = ?, avatar = ?, dt_add =?", false,
            $_POST['login'],
            $_POST['email'],
            $password_hash,
            $avatar,
            $current_time
        );
        $post_id = mysqli_insert_id($connection);
        $URL = '/';
        header("Location: $URL");
    }
}

$form_values = $form['values'] ?? [];

$page_content = include_template(
    'reg-template.php',
    [
        'form_values' => $form['values'] ?? [],
        'form_errors' => $form['errors'] ?? [],
        'form_error_codes' => $form_error_codes
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'title' => $title,
    ]
);

print($layout_content);
