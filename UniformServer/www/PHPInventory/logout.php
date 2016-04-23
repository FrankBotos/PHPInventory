<?php

session_start();
setcookie("PHPSESSID", "", time() - 61200,"/");
session_destroy();
header('Location: login.php');
?>
