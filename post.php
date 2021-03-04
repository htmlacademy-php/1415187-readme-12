<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}

$select_post_by_id = 
    "SELECT
        posts.*,
        users.username,
        users.avatar,
        content_types.type_class
    FROM posts
    INNER JOIN users ON posts.author_id=users.id
    INNER JOIN content_types ON posts.post_type=content_types.id
    WHERE posts.id = ?
    ORDER BY view_count DESC;";

$id = $_GET['id'];
$posts_mysqli = secure_query($con, $select_post_by_id, 'i', $id);
if (!mysqli_num_rows($posts_mysqli)) {
    $page_not_found = true;
    display_404_page();
    exit();
}

$posts_array = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
$post_author_id = $posts_array[0]['author_id'];
$count_posts_by_author = "SELECT COUNT(*) FROM posts WHERE author_id = ?;";
$author_posts_count_mysqli = secure_query($con, $count_posts_by_author, 'i', $post_author_id);
$author_posts_count = mysqli_fetch_row($author_posts_count_mysqli)[0];
$count_author_followers = "SELECT COUNT(*) FROM subscribe WHERE author_id = ?;";
$author_followers_count_mysqli = secure_query($con, $count_author_followers, 'i', $post_author_id);
$author_followers_count = mysqli_fetch_row($author_followers_count_mysqli)[0];
$page_content = include_template('post-details.php', ['post' => $posts_array[0],'author_posts_count' => $author_posts_count, 'author_followers_count' => $author_followers_count]);

print($page_content);

mysqli_close($con);