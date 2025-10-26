<?php
$host = "sql113.infinityfree.com";
$user = "if0_40002591";
$pass = "prachu040705";
$db   = "if0_40002591_users";

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
