<?php
require_once(__DIR__ . '/libs/base.php');

if (isset($_SESSION['id'])) {
    header("Location: feed.php");
}

$title = $site_name . ': Блог, каким он должен быть';
$validation_rules = [
    'login' => 'filled|exists:users,email,not',
    'password' => 'filled|correct_password:users,email,password'
];

$form_error_codes = [
    'login' => 'Логин',
    'password' => 'Пароль',
];

$form = [
    'values' => [],
    'errors' => [],
];

if (count($_POST) > 0) {

    $form['values'] = $_POST;
    $form['errors'] = validate($form['values'], $validation_rules, $connection);
    $form['errors'] = array_filter($form['errors']);

    if (empty($form['errors'])) {
        $user = get_user_data($connection, $form['values']['login']);
        $_SESSION['is_auth'] = 1;
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['id'] = $user['id'];
        header("Location: feed.php");
        exit();
    }
}

$page_content = include_template(
    'anonym.php',
    [
        'form_values' => $form['values'] ?? [],
        'form_errors' => $form['errors'] ?? [],
        'form_error_codes' => $form_error_codes
    ]
);
print($page_content);
