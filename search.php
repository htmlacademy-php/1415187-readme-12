<?php

require_once __DIR__ . '/libs/base.php';

$user = get_user();
$title = $site_name . ': Cтраница результатов поиска';

if ($user === null) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['keywords'])) {
    display_404_page();
    exit();
}

$keywords = trim($_GET['keywords']);

$search_results = search_posts($connection, $keywords);

if (count($search_results) == 0) {
    $title .= ' (нет результатов)';
}

$page_content = include_template(
    'search-template.php',
    [
        'keywords' => $keywords,
        'posts'    => $search_results,
        'now_time' => $now_time,
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'title'          => $title,
        'user'           => $user,
        'content'        => $page_content,
        'active_section' => $active_section,
    ]
);

print($layout_content);
