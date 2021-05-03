<?php
require_once __DIR__ . '/libs/base.php';

$user = get_user($connection);

if ($user === null) {
    header("Location: index.php");
    exit();
}

parse_str(parse_url($_SERVER['HTTP_REFERER'])['query'], $get);
$title = $site_name . ': Популярное';
$page_number = $_GET['page'] ?? '1';
$page_limit = $_GET['limit'] ?? $page_limit;
$page_offset = ($page_number - 1) * $page_limit;
$content_types = get_content_types($connection);
$content_type_names = array_column($content_types, 'type_class');
$filter = get_filter($_GET['filter'], $content_type_names);
$sort = $_GET['sort'] ?? 'view_count';
$sort = get_filter($sort, ["likes", "view_count", "dt_add"]);
$total_posts = get_total_posts($connection, $filter);
$reverse = get_reverse($_GET['reverse'], $get, $sort, $filter);
$posts = get_popular_posts($connection, $filter, $sort, $reverse, $page_limit, $page_offset);

$page_content = include_template(
    'popular-template.php',
    [
        'posts' => $posts,
        'total_posts' => $total_posts,
        'filter' => $filter,
        'sort' => $sort,
        'page_number' => $page_number,
        'page_limit' => $page_limit,
        'content_types' => $content_types,
        'now_time' => $now_time,
        'reverse' => $reverse,
    ]
);
$layout_content = include_template(
    'layout.php',
    [
        'title' => $title,
        'user' => $user,
        'content' => $page_content,
        'active_section' => 'popular',
    ]
);

print($layout_content);