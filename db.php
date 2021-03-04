<?php

$con = mysqli_connect("localhost", "mysql", "mysql", "readme");
mysqli_set_charset($con, "utf8mb4");

if ($con == false) {
    http_response_code(500);
    exit();
}