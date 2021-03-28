<?php
require_once(__DIR__ . '/libs/base.php');

$validation_rules = [
    'author_id' => 'exists:users,id,not',
];

$user = get_user($connection);

if ($user === NULL) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    display_404_page($user);
    exit();
}

$author_id = $_GET['id'];

if ($author_id == $user['id']) {
    header("Location: profile.php?id=".$author_id);
    exit();
}

$subscribe_error = validate(['author_id' => $author_id], $validation_rules, $connection);
$subscribe_error = array_filter($subscribe_error);

if (empty($subscribe_error)) {
    user_subscribe($connection, true, $user['id'], $author_id);
}

header("Location: " . $_SERVER['HTTP_REFERER']);