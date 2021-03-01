<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$add_user_query = "INSERT into users SET username = ?, email = ?, password = ?, avatar = ?";

$validation_rules = [
    'email' => 'filled|correctemail|exists:users,email',
    'login' => 'filled',
    'password' => 'filled|repeatpassword',
    'password-repeat' => 'filled|repeatpassword'
];

$form_error_codes = [
    'email' => 'Email',
    'login' => 'Логин',
    'password' => 'Пароль',
    'password-repeat' => 'Подтверждение пароля'
];

$con = db_connect("localhost", "mysql", "mysql", "readme");

if (count($_POST) > 0) {
    foreach ($_POST as $field_name => $field_value) {
        $form['values'][$field_name] = $field_value;
    }
    $form['errors'] = validate($form['values'], $validation_rules, $con);
    $form['errors'] = array_filter($form['errors']);
    if (empty($form['errors'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $avatar = save_image('userpic-file');
        secure_query($con, $add_user_query, 'ssss', $_POST['login'], $_POST['email'], $password_hash, $avatar);
        $post_id = mysqli_insert_id($con);
        $URL = '/';
        header("Location: $URL");
    }
}

$page_content = include_template('registration.php', [
                                                    'form_values' => $form['values'],
                                                    'form_errors' => $form['errors'],
                                                    'form_error_codes' => $form_error_codes
                                                    ]);
print($page_content);