<?php
session_start();

    if (!isset($_SESSION['user_id'])) {
        header("location: index.php");
        exit();

    }
$user_id = $_SESSION['user_id'];



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iTala - Barangay Litlit</title>
    <link rel="stylesheet" href="style/home_style.css">
  
</head>
<body>
  <header>
    <div class="logo">
      <img src="style/logo.png" alt="Logo">
      <span>iTala</span>
    </div>
  </header>

    <div class="content">
        <img src="style/WATERMARK LOGO.png" alt="Barangay Seal" class="bg-seal">
        <a href="registration.php" class="button">New Registration</a>
        <a href="dashboard.php" class="button">View Data</a>
    </div>
    
</body>
</html>