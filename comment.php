<?php

require_once __DIR__ . '/libs/base.php';

$validation_rules = [
    'post-id' => 'exists:posts,id,not',
    'comment' => 'filled|length:3,200',
];

$user = get_user();

if ($user === null) {
    header("Location: index.php");
    exit();
}

if (count($_POST) == 0 || !isset($_POST['post-id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_POST['post-id'];
$form['values'] = $_POST;
$form['errors'] = validate($form['values'], $validation_rules, $connection);
$form['errors'] = array_filter($form['errors']);

if (empty($form['errors'])) {
    $comment = post_comment($connection, $user['id'], $post_id, $_POST['comment']);
} else {
    $_SESSION['errors'] = $form['errors'];
    $_SESSION['comment_value'] = $form['values']['comment'];
}

$URL = '/post.php?id=' . $post_id;

header("Location: $URL");
