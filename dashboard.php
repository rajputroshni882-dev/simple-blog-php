<?php
// dashboard.php
session_start();
include 'config.php';
include 'header.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user_name'];
$user_id  = $_SESSION['user_id'];

$success = "";
$error   = "";

// 🗑 Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM news WHERE id = $delete_id AND user_id = $user_id");
    header("Location: dashboard.php");
    exit();
}

// ✍ Handle new post (with file upload)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'])) {
    $title       = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $imagePath   = null;

    // ✅ Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/"; // make sure this folder exists and is writable
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, recursive: true);
        }

        $fileName   = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $error = "❌ Failed to upload image.";
            }
        } else {
            $error = "⚠️ Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    if (!empty($title) && !empty($description) && empty($error)) {
        $sql = "INSERT INTO news (user_id, title, description, image) 
                VALUES ('$user_id', '$title', '$description', " . ($imagePath ? "'$imagePath'" : "NULL") . ")";
        if ($conn->query($sql) === TRUE) {
            $success = "✅ News saved successfully!";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    } elseif (empty($error)) {
        $error = "⚠️ Please fill in all fields.";
    }
}

// 📌 Fetch all news
$newsSql    = "SELECT * FROM news WHERE user_id = '$user_id' ORDER BY created_at DESC";
$newsResult = $conn->query($newsSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome to your Dashboard</h2>
    <p>Hello, <strong><?= htmlspecialchars($username); ?></strong> 👋</p>
    <a href="logout.php">Logout</a>

    <hr>

    <h3>Create News</h3>
    <?php if ($success): ?><p style="color:green;"><?= $success; ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red;"><?= $error; ?></p><?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="5" required></textarea><br><br>

        <label>Upload Image:</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Save News</button>
    </form>

    <hr>

    <h3>Your News</h3>
    <?php if ($newsResult->num_rows > 0): ?>
        <ul>
            <?php while ($row = $newsResult->fetch_assoc()): ?>
                <li>
                    <a href="view_news.php?id=<?= $row['id']; ?>">
                        <?= htmlspecialchars($row['title']); ?>
                    </a> 
                    (<?= date("d M Y, h:i A", strtotime($row['created_at'])); ?>)

                    <?php if ($row['image']): ?>
                        <br><img src="<?= $row['image']; ?>" width="120" style="margin:5px 0;">
                    <?php endif; ?>

                    <!-- 🗑 Delete link -->
                    <br><a href="dashboard.php?delete=<?= $row['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this news?');">
                           🗑 Delete
                        
                        </a>
                         <a href="edit.php?id=<?= $row['id']; ?>">
            Edit
                    </a> 
                </li>
                <hr>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No news posted yet.</p>
    <?php endif; ?>
</body>
</html>
