<?php
require_once __DIR__ . '/libs/base.php';

$user = get_user();

if ($user === null) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    display_404_page($user);
    exit();
}

$post_id = $_GET['id'];
$URL = '/post.php?id=' . repost_post($connection, $user['id'], $post_id);

header("Location: $URL");