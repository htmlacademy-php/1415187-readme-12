<?php
require_once(__DIR__ . '/libs/base.php');

$user = get_user();

if ($user === NULL) {
    header("Location: index.php");
    exit();
}

$title = $site_name . ': Моя лента';
$content_types = get_content_types($connection);
$content_type_names = array_column($content_types, 'type_class');
$filter = get_filter($_GET['filter'], $content_type_names);
$posts = get_feed_posts($connection, $filter, $user['id']);

$page_content = include_template(
    'feed-template.php',
    [
        'posts' => $posts,
        'filter' => $filter,
        'content_types' => $content_types,
        'now_time' => $now_time
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'title' => $title,
        'active_section' => 'feed',
        'user' => $user,
        'content' => $page_content,
    ]
);

print($layout_content);
