<?php
include 'config.php';
include 'header.php';

// Check if ID is passed
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM news WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $news = $result->fetch_assoc();
} else {
    echo "❌ News not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']); ?></title>
</head>
<body>
    <h2><?= htmlspecialchars($news['title']); ?></h2>
    <p><em>Posted on <?= date("d M Y, h:i A", strtotime($news['created_at'])); ?></em></p>
    <p><?= nl2br(htmlspecialchars($news['description'])); ?></p>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
