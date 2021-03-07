<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

session_start();

if ($_SESSION['is_auth'] == 1) {
    $user = $_SESSION['username'];
    $page_content = include_template('user-feed.php', ['user' => $user]);
    print($page_content);
    exit();
}
header("Location: index.php");
