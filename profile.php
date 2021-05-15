<?php
require_once __DIR__ . '/libs/base.php';

$user = get_user();

if ($user === null) {
    header("Location: index.php");
    exit();
}

$profile_id = isset($_GET['id']) ? (int) $_GET['id'] : $user['id'];
$owner = get_profile($connection, $profile_id);
if ($owner === null) {
    display_404_page($user);
    exit();
}
$title = $site_name . ': Профиль ' . $owner['username'];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'posts';
$user['subscribed'] = user_subscribe($connection, false, $user['id'], $profile_id);
$posts = get_profile_posts($connection, $profile_id);
$likes = get_profile_likes($connection, $profile_id);
$subscribes = get_profile_subscribes($connection, $user['id'], $profile_id);

$page_content = include_template(
    'profile-template.php',
    [
        'user' => $user,
        'tab' => $tab,
        'owner' => $owner,
        'posts' => $posts,
        'likes' => $likes,
        'subscribes' => $subscribes,
        'now_time' => $now_time,
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
