<?php
include 'config.php';

// How many news per page
$limit = 5;

// Current page (default = 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Search keyword
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Base query
$where = "";
if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $where = "WHERE title LIKE '%$safeSearch%'";
}

// Count total news
$totalResult = $conn->query("SELECT COUNT(*) as total FROM news $where");
$totalRow = $totalResult->fetch_assoc();
$totalNews = $totalRow['total'];

// Total pages
$totalPages = ceil($totalNews / $limit);

// Offset
$offset = ($page - 1) * $limit;

// Fetch news
$sql = "SELECT * FROM news $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>News Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">📰 News Dashboard</h3>
            <form method="GET" action="" class="d-flex">
                <input type="text" name="search" class="form-control me-2" 
                       placeholder="Search by title..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-light" type="submit">Search</button>
            </form>
        </div>
        <div class="card-body">

            <?php if ($result->num_rows > 0) { ?>
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <?php if ($row['image']) { ?>
                                    <img src="uploads/<?php echo $row['image']; ?>" width="80" class="img-thumbnail">
                                <?php } else { ?>
                                    <span class="text-muted">No Image</span>
                                <?php } ?>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <a href="edit_news.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete_news.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this news?');" 
                                   class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">« Prev</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next »</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

            <?php } else { ?>
                <div class="alert alert-warning text-center">⚠️ No news found.</div>
            <?php } ?>
        </div>
    </div>
</div>

</body>
</html>
