<?php

require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

session_start();

$count_post_likes_query = "SELECT COUNT(*) FROM likes WHERE post_id = ?;";
$count_post_comments_query = "SELECT COUNT(*) FROM comments WHERE post_id = ?;";
$search_query =
    "SELECT
        posts.*,
        users.username,
        users.avatar,
        content_types.type_class
    FROM posts
    INNER JOIN users ON posts.author_id=users.id
    INNER JOIN content_types ON posts.post_type=content_types.id
    WHERE MATCH(heading, content) AGAINST(?)";

if ($_SESSION['is_auth'] == 1) {

    $user = $_SESSION['username'];

    if (count($_GET) > 0) {
        
        $keywords = trim($_GET['keywords']);
        
        if ($keywords != '') {
            
            $posts_mysqli = secure_query_bind_result($con, $search_query, false, $keywords);
            $search_results = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
            
            foreach ($search_results as $index => $search_result) {
                
                $likes_mysqli = secure_query_bind_result($con, $count_post_likes_query, false, $search_result['id']);
                $search_results[$index]['likes'] = mysqli_fetch_row($likes_mysqli)[0];
                
                $comments_mysqli = secure_query_bind_result($con, $count_post_comments_query, false, $search_result['id']);
                $search_results[$index]['comments'] = mysqli_fetch_row($comments_mysqli)[0];     
            }
        }
    }

    $page_content = include_template('search-results.php', [
                                                            'keywords' => $keywords,
                                                            'posts' => $search_results
                                                            ]);

    print($page_content);
    exit();
}

header("Location: index.php");  