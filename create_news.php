<?php
// create_news.php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = (int)$_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // handle image
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg','image/png','image/gif','image/jpg'];
        if (!in_array($_FILES['image']['type'], $allowed)) {
            $message = '<div class="alert alert-danger">Invalid image type.</div>';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $message = '<div class="alert alert-danger">Image too large (max 5MB).</div>';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $imageName)) {
                $message = '<div class="alert alert-danger">Failed to move uploaded file.</div>';
                $imageName = null;
            }
        }
    }

    if ($message === '') {
        $stmt = $conn->prepare("INSERT INTO news (user_id, title, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $uid, $title, $description, $imageName);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: dashboard.php?msg=created');
            exit;
        } else {
            $message = '<div class="alert alert-danger">DB Error: ' . htmlspecialchars($stmt->error) . '</div>';
            $stmt->close();
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create News</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="card mx-auto" style="max-width:700px;">
    <div class="card-header bg-dark text-white">Create News</div>
    <div class="card-body">
      <?php echo $message; ?>
      <form method="post" action="" enctype="multipart/form-data">
        <div class="mb-2">
          <label class="form-label">Title</label>
          <input name="title" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">description</label>
          <textarea name="description" rows="6" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Image (optional)</label>
          <input name="image" type="file" class="form-control">
        </div>
        <div class="d-flex justify-description-between">
          <a href="dashboard.php" class="btn btn-secondary">Back</a>
          <button class="btn btn-dark" type="submit">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
