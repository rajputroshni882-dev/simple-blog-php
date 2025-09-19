<?php
include 'config.php'; 
include 'header.php'; 

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   

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
  <title>Signup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4">📝 Signup</h3>

        <!-- Messages -->
        <?php if ($success): ?>
        <p style="color:green;"><?= $success; ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error; ?></p>
    <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            
            <button type="submit" class="btn btn-dark w-100">Signup</button>
        </form>

        <p class="mt-3 text-center">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</div>

</body>