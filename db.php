<?php
$db_host = "localhost";
$db_user = "ar_user2";
$db_pass = "FfMzqhGgvWsaQXENL5jDm.,12!.,@,.5kPe637CS92cJAVBrKTfMwUYbdF4n8xptzaV9D";
$db_name = "ar_location_db";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
