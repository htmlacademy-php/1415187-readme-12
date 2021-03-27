<?php
require_once(__DIR__ . '/libs/base.php');

$user = get_user();

if ($user === NULL) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}

$post_id = $_GET['id'];
$post = get_post($connection, $post_id);
$comment_errors = [];

if ($post === NULL) {
    display_404_page();
    exit();
}

if (!empty($_SESSION['errors'])) {
    $comment_errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

$author_id = $post['author_id'];
$author = get_post_author($connection, $author_id);
$comments = get_post_comments($connection, $post_id);
$views_mysqli = increase_post_views($connection, $post_id);
$user['subscribed'] = user_subscribe($connection, false, $user['id'], $author_id);
$title = $site_name . ': Публикация' . $post['heading'];

$page_content = include_template(
    'posts/' . 'post-details.php',
    [
        'user' => $user,
        'post' => $post,
        'author' => $author,
        'comments' => $comments,
        'comment_errors' => $comment_errors,
        'now_time' => $now_time
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title
    ]
);

print($layout_content);