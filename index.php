<?php
session_start();
$loginErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correct_username = "admin";
    $correct_password = "password";

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === $correct_username && $password === $correct_password) {
        $_SESSION['user_id'] = $username;
        header("Location: home.php");
        exit();
    } else {
        $loginErr = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style/index_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="nav">
        <div class="image-container">
            <img src="style/litlitLogo.png" alt="Logo">
        </div>
        <div class="nav-title">
            <p>Province of Cavite, Municipality of Silang,</p>
            <p>Barangay Litlit - Census IS</p>
        </div>
    </div>

    <div class="white-part">
        <div class="itala-header">
            <img src="style/logo.png" alt="iTala Logo">
            <h1 class="itala-text">iTala</h1>
        </div>

        <div class="light-green-part">
            <form class="Login-Form" action="index.php" method="post">
                <p class="login-head">Admin Login</p>

                <?php if ($loginErr): ?>
                    <div class="error"><?php echo $loginErr; ?></div>
                <?php endif; ?>

                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <input type="submit" value="Login" id="log-button">
            </form>
        </div>
    </div>
</body>
</html>
