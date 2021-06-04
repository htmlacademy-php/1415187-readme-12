<?php

require_once __DIR__ . '/libs/base.php';

$user = get_user();

if ($user === null) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];
like_post($connection, $user['id'], $post_id);

header('Location: ' . $_SERVER['HTTP_REFERER']);
