<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

session_start();

$form_error_codes = [
    'login' => 'Логин',
    'password' => 'Пароль',
];


$validation_rules = [
    'login' => 'filled',
    'password' => 'filled|correct_password:users,username,password'
];

if (count($_POST) > 0) {
    foreach ($_POST as $field_name => $field_value) {
        $form['values'][$field_name] = $field_value;
    }
    $form['errors'] = validate($form['values'], $validation_rules, $con);
    $form['errors'] = array_filter($form['errors']);
    if (empty($form['errors'])) {
        $_SESSION['username'] = $form['values']['login'];
        $_SESSION['is_auth'] = 1;
        header("Location: feed.php");
        exit();
    }
}

$page_content = include_template('anonym.php', [
                                                'form_values' => $form['values'],
                                                'form_errors' => $form['errors'],
                                                'form_error_codes' => $form_error_codes
                                                ]);
print($page_content);