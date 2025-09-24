<?php
// delete_news.php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = (int)$_SESSION['user_id'];
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT image FROM news WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    $stmt->close();
    header('Location: dashboard.php?msg=notfound');
    exit;
}
$row = $res->fetch_assoc();
$stmt->close();

// delete image file if exists
if (!empty($row['image']) && file_exists(__DIR__ . '/uploads/' . $row['image'])) {
    @unlink(__DIR__ . '/uploads/' . $row['image']);
}

// delete db record
$del = $conn->prepare("DELETE FROM news WHERE id = ? AND user_id = ?");
$del->bind_param("ii", $id, $uid);
$del->execute();
$del->close();

header('Location: dashboard.php?msg=deleted');
exit;
