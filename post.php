<?php
require_once __DIR__ . '/libs/base.php';

$user = get_user($connection);

if ($user === null) {
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
$show_all_comments = $_GET['showall'] ?? false;

if ($post === null) {
    display_404_page($user);
    exit();
}

if (!empty($_SESSION['errors'])) {
    $comment_errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

$author_id = $post['author_id'];
$author = get_post_author($connection, $author_id);
$comments = get_post_comments($connection, $post_id);
$user['subscribed'] = user_subscribe($connection, false, $user['id'], $author_id);
$title = $site_name . ': Публикация' . $post['heading'];
$count_comments = count($comments);

if (explode('?', $_SERVER['REQUEST_URI'])[0] != explode('?', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH))[0]) {
    $views_mysqli = increase_post_views($connection, $post_id);
}

$page_content = include_template(
    'posts/' . 'post-details.php',
    [
        'user' => $user,
        'post' => $post,
        'author' => $author,
        'comments' => $comments,
        'comment_errors' => $comment_errors,
        'now_time' => $now_time,
        'show_all' => $show_all_comments,
        'count_comments' => $count_comments,
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title,
    ]
);

print($layout_content);