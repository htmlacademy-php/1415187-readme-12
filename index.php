<?php
$is_auth = rand(0, 1);
$user_name = 'Mark';
$page_title = 'readme: популярное';

$sql_select_content_types = "SELECT * FROM content_types;";
$sql_select_posts_users = 
    "SELECT
        users.username,
        users.avatar,
        heading,
        content,
        view_count,
        content_types.type_class        
    FROM posts
    INNER JOIN users ON posts.author_id=users.id
    INNER JOIN content_types ON posts.post_type=content_types.id
    ORDER  BY view_count DESC;";

require_once('helpers.php');
require_once('functions.php');

$con = mysqli_connect("localhost", "mysql", "mysql", "readme");

if (!$con) {
    $error = mysqli_connect_errno($con) . ": " . mysqli_connect_error($con) . "\n";
    print($error);
} else {
    mysqli_set_charset($con, "utf8");
    $content_types = select_query($con, $sql_select_content_types);
    $popular_posts = select_query($con, $sql_select_posts_users);
}

$page_content = include_template('main.php', ['popular_posts' => $popular_posts]);
$layout_content = include_template('layout.php', ['content' => $page_content,
                                                  'user_name' => $user_name,
                                                  'page_title' => $page_title,
                                                  'is_auth' => $is_auth]);

print($layout_content);