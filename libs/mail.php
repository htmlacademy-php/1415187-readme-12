<?php
require_once __DIR__ . '/../vendor/autoload.php';

$mail_settings = [
    'user' => 'keks@phpdemo.ru',
    'sender' => 'keks@phpdemo.ru',
    'password' => 'htmlacademy',
    'server' => 'phpdemo.ru',
    'port' => 25,
    'encryption' => false,
];

$mailer = apply_mail_settings($mail_settings, $site_name);