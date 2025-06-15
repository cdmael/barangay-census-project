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
    <link rel="stylesheet" href="reg_style.css">
  
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="Title.png" alt="Barangay Litlit Logo">
            <div class="logo-text">iTala</div>
        </div>
        <div class="title">Barangay Litlit Resident Information System</div>
    </div>

    <div class="content">
        <img src="WATERMARK LOGO.png" alt="Barangay Seal" class="bg-seal">
        <a href="registration.php" class="button">New Registration</a>
        <a href="dashboard.php" class="button">View Data</a>
    </div>
    
</body>
</html>
