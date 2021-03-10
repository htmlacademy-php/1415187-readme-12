<?php

require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$is_auth = rand(0, 1);
$user_name = 'Mark';
$page_title = 'readme: популярное';
$now_time = new DateTime('now');

$sql_select_posts =
    "SELECT
        posts.*,
        users.username,
        users.avatar,
        content_types.type_class
    FROM posts
    INNER JOIN users ON posts.author_id=users.id
    INNER JOIN content_types ON posts.post_type=content_types.id ";

if (isset($_GET['post_type'])) {
    $post_type = $_GET['post_type'];
    $sql_select_posts .= "WHERE content_types.id = ? ORDER BY view_count DESC;";
    $posts_mysqli = secure_query($con, $sql_select_posts, 'i', $post_type);
    $popular_posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
} else {
    $post_type='';
    $sql_select_posts .= "ORDER BY view_count DESC;";
    $posts_mysqli = mysqli_query($con, $sql_select_posts);
    $popular_posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
}

$content_types = get_content_types($con);

$page_content = include_template('main.php', [
                                                 'popular_posts' => $popular_posts,
                                                 'now_time' => $now_time,
                                                 'content_types' => $content_types,
                                                 'post_type' => $post_type
                                             ]);

$layout_content = include_template('layout.php', [
                                                     'content' => $page_content,
                                                     'user_name' => $user_name,
                                                     'page_title' => $page_title,
                                                     'is_auth' => $is_auth
                                                 ]);

print($layout_content);