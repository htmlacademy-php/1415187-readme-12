<?php

$site_name = 'ReadMe';
$connection = db_connect("localhost", "mysql", "mysql", "readme");
$page_limit = 6;
$now_time = new DateTime('now');