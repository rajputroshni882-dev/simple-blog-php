<?php
// dashboard.php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = (int)$_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? 'User';

// Total news count (for user)
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM news WHERE user_id = ?");
$stmt->bind_param("i", $uid);
// label removed by accident? ignore
$stmt->execute();
$result = $stmt->get_result();
$totalRow = $result->fetch_assoc();
$totalNews = $totalRow['total'];
$stmt->close();

// News per day (last 7 days) for user
$stmt = $conn->prepare("SELECT DATE(created_at) AS date, COUNT(*) AS count FROM news WHERE user_id = ? GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
$dates = [];
$counts = [];
while ($r = $res->fetch_assoc()) {
    $dates[] = $r['date'];
    $counts[] = $r['count'];
}
$stmt->close();
$dates = array_reverse($dates);
$counts = array_reverse($counts);

// Pagination + Search
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// total records (with search)
if ($search !== '') {
    $like = "%{$search}%";
    $cntStmt = $conn->prepare("SELECT COUNT(*) AS total FROM news WHERE user_id = ? AND title LIKE ?");
    $cntStmt->bind_param("is", $uid, $like);
    $cntStmt->execute();
    $cntRes = $cntStmt->get_result();
    $totalRecords = $cntRes->fetch_assoc()['total'] ?? 0;
    $cntStmt->close();

    $listStmt = $conn->prepare("SELECT id, title, created_at, image FROM news WHERE user_id = ? AND title LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $listStmt->bind_param("isii", $uid, $like, $limit, $start);
} else {
    $cntStmt = $conn->prepare("SELECT COUNT(*) AS total FROM news WHERE user_id = ?");
    $cntStmt->bind_param("i", $uid);
    $cntStmt->execute();
    $cntRes = $cntStmt->get_result();
    $totalRecords = $cntRes->fetch_assoc()['total'] ?? 0;
    $cntStmt->close();

    $listStmt = $conn->prepare("SELECT id, title, created_at, image FROM news WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $listStmt->bind_param("iii", $uid, $limit, $start);
}
$listStmt->execute();
$newsResult = $listStmt->get_result();
$listStmt->close();

$totalPages = max(1, (int)ceil($totalRecords / $limit));
$msg = $_GET['msg'] ?? '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>📊 News Dashboard</h2>
    <div>
              <a href="global_news.php" class="btn btn-primary me-2">Global</a>

      <a href="create_news.php" class="btn btn-success me-2">+ Create News</a>
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>

  <?php if ($msg === 'created'): ?>
    <div class="alert alert-success">News created.</div>
  <?php elseif ($msg === 'updated'): ?>
    <div class="alert alert-success">News updated.</div>
  <?php elseif ($msg === 'deleted'): ?>
    <div class="alert alert-success">News deleted.</div>
  <?php elseif ($msg === 'notfound'): ?>
    <div class="alert alert-warning">Item not found or access denied.</div>
  <?php endif; ?>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5>Total News</h5>
          <h2 class="text-primary"><?php echo (int)$totalNews; ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5>User</h5>
          <h3 class="text-success"><?php echo htmlspecialchars($username); ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-dark text-white">🗓 News per Day (Last 7 Days)</div>
    <div class="card-body">
      <canvas id="newsChart" height="100"></canvas>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
      <span>📰 Manage News</span>
      <form class="d-flex" method="get" action="">
        <input name="search" class="form-control form-control-sm me-2" placeholder="Search by title" value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-light btn-sm">Search</button>
      </form>
    </div>
    <div class="card-body">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($newsResult->num_rows > 0): ?>
            <?php while ($r = $newsResult->fetch_assoc()): ?>
              <tr>
                <td><?php echo (int)$r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['title']); ?></td>
                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                <td>
                  <a class="btn btn-sm btn-primary" href="edit.php?id=<?php echo (int)$r['id']; ?>">Edit</a>
                  <a class="btn btn-sm btn-danger" href="delete.php?id=<?php echo (int)$r['id']; ?>" onclick="return confirm('Delete this news?');">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-center text-muted">No news found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <nav>
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">Previous</a></li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
              <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">Next</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('newsChart').getContext('2d');
const newsChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($dates); ?>,
    datasets: [{
      label: 'News Count',
      data: <?php echo json_encode($counts); ?>,
      backgroundColor: 'rgba(54,162,235,0.6)',
      borderColor: 'rgba(54,162,235,1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true, precision: 0 }
    }
  }
});
</script>
</body>
</html>
