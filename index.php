<?php
require_once('helpers.php');
require_once('functions.php');

$is_auth = rand(0, 1);
$user_name = 'Mark';
$page_title = 'readme: популярное';
$now_time = new DateTime('now');
$con = mysqli_connect("localhost", "mysql", "mysql", "readme");
$sql_select_content_types = "SELECT * FROM content_types;";
$sql_select_posts = 
    "SELECT
        posts.*,
        users.username,
        users.avatar,
        content_types.type_class       
    FROM posts
    INNER JOIN users ON posts.author_id=users.id
    INNER JOIN content_types ON posts.post_type=content_types.id ";

if (!$con) {
    http_response_code(500);
    exit();
}

mysqli_set_charset($con, "utf8");

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
$content_types_mysqli = mysqli_query($con, $sql_select_content_types);
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);

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
require_once('debug.php');