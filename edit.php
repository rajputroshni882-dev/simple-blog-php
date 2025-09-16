<?php
include 'config.php';

// Fetch existing news data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM news WHERE id=$id");
    $news = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $title= mysqli_real_escape_string($conn, trim($_POST['title']));
      $content = mysqli_real_escape_string($conn, trim($_POST['description']));
    $image = $news['image']; // default old image

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);

        // Delete old image
        if (!empty($news['image']) && file_exists("uploads/" . $news['image'])) {
            unlink("uploads/" . $news['image']);
        }
    }

    // 🔹 Update query
    $sql = "UPDATE news SET 
                title='$title', 
                description='$content', 
                image='$image'
            WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: dashboard.php?msg=updated");
        exit;
    } else {
        echo "Error updating: " . $conn->error;
    }
}
?>

<h2>Edit News</h2>
<form method="post" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo $news['title']; ?>"><br><br>

    <label>Content:</label><br>
    <textarea name="description"><?php echo $news['description']; ?></textarea><br><br>

    <label>Current Image:</label><br>
    <?php if ($news['image']) { ?>
        <img src="uploads/<?php echo $news['image']; ?>" width="100"><br>
    <?php } ?>
    <input type="file" name="image"><br><br>

    <button type="submit" name="update">Update News</button>
</form>
