<?php
require_once(__DIR__ . '/libs/base.php');

$validation_rules = [
    'owner_id' => 'exists:users,id,not',
];

$user = get_user($connection);

if ($user === NULL) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    display_404_page($user);
    exit();
}

$owner_id = $_GET['id'];

if ($owner_id == $user['id']) {
    header("Location: profile.php?id=" . $owner_id);
    exit();
}

$subscribe_error = validate(['owner_id' => $owner_id], $validation_rules, $connection);
$subscribe_error = array_filter($subscribe_error);

if (empty($subscribe_error)) {
    if (user_subscribe($connection, true, $user['id'], $owner_id)) {
        $owner = mysqli_fetch_assoc(secure_query_bind_result($connection, "SELECT email, username FROM users WHERE id = ?", false, $owner_id));
        new_follower_notification($mail_settings['sender'], $owner, $user, $mailer);
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);