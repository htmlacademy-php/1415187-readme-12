<?php

require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

session_start();


$validation_rules = [
    'login' => 'filled|exists:users,email,not',
    'password' => 'filled|correct_password:users,email,password'
];
$form_error_codes = [
    'login' => 'Логин',
    'password' => 'Пароль',
];

if (count($_POST) > 0) {
    foreach ($_POST as $field_name => $field_value) {
        $form['values'][$field_name] = $field_value;
    }
    
    $form['errors'] = validate($form['values'], $validation_rules, $con);
    $form['errors'] = array_filter($form['errors']);
    
    if (!empty($form['errors']['login'])) {
        unset($form['errors']['password']);
    }

    if (empty($form['errors'])) {
        $user_data = get_user_data($con, $form['values']['login']);
        $_SESSION['username'] = $user_data[0];
        $_SESSION['avatar'] = $user_data[1];
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