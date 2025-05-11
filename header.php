<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
}

if (!isset($conn)) {
    require_once 'db.php';
}

$id = $_SESSION['id'] ?? null;
$row = [];

if ($id) {
    $sql = "SELECT * FROM usr WHERE id = '$id'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        $row = mysqli_fetch_assoc($query);
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AR Map</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen pb-16">
  <!-- Top Logo/Header -->
  <header class="p-4 flex justify-center">
    <a href="index.php">
      <img src="assets/images/logoar.png" alt="AR Map logo" class="h-8">
    </a>
  </header>
  <main class="px-4">
