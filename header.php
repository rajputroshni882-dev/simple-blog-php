<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <nav>
        <a href="index.php">home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">dashboard</a>
            <a href="logout.php">logout</a>
        <?php else: ?>
            <a href="signup.php">signup</a>
            <a href="login.php">login</a>
        <?php endif; ?>
    </nav>
</body>
</html>
