<?php
require_once __DIR__ . '/libs/base.php';

$user = get_user();

if ($user === null) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];
$post = get_post($connection, $post_id);

if ($post === null) {
    display_404_page($user);
    exit();
}
increase_post_views($connection, $user['id'], $post_id);
$comment_errors = [];
$show_all_comments = $_GET['showall'] ?? false;

if (!empty($_SESSION['errors'])) {
    $comment_errors = $_SESSION['errors'];
    $comment_text = $_SESSION['comment_value'];
    unset($_SESSION['errors']);
    unset($_SESSION['comment_value']);
}

$author_id = $post['author_id'];
$author = get_post_author($connection, $author_id);
$comments = get_post_comments($connection, $post_id);
$user['subscribed'] = user_subscribe($connection, false, $user['id'], $author_id);
$title = $site_name . ': Публикация "' . $post['heading'] . '"';
$count_comments = count($comments);

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
        'comment_text' => $comment_text ?? '',
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title,
        'active_section' => $active_section,
    ]
);

print($layout_content);