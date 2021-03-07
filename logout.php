<?php

session_start();
unset($_SESSION['username']);
unset($_SESSION['is_auth']);
header("Location: /index.php");
