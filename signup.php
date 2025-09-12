<?php
include 'config.php'; 
include 'header.php'; 

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_SERVER);


    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
 
    if (!empty($name) && !empty($email) && !empty($password)) {
        // Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email = '$email'";
        $checkResult = $conn->query($checkSql);
         //die("kukur");
        if ($checkResult->num_rows > 0) 
            {
          
            $error = "⚠️ Email already registered. Please <a href='login.php'>login</a>.";
        

        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $insertSql = "INSERT INTO users (username, email, password) 
                          VALUES ('$name', '$email', '$hashedPassword')";

            if ($conn->query($insertSql) === TRUE) {
                $success = "✅ Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "❌ Error: " . $conn->error;
            }
        }
    } else {
        $error = "⚠️ Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>
<body>
    <h2>Signup</h2>

    <?php if ($success): ?>
        <p style="color:green;"><?= $success; ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Signup</button>
    </form>
</body>
</html>
