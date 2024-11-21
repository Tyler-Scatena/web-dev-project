<?php
session_start();
include './config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calorie Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Calorie</span> Tracker</h1>
            </div>
            <nav>
                <ul>
                    <li class="current"><a href="index.php">Home</a></li>
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="calendar.php">Calendar</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Welcome to Calorie Tracker</h2>
        <p>Track your daily calorie intake, search for foods, and maintain a healthy lifestyle!</p>
        <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
            <a href="register.php" class="btn">Get Started</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>
