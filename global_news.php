<?php
// Simple Global News page using GNews API

$apiKey = "e2d7f558fd8020ccf2ebb5e2075168de"; // 🔑 Replace with your free GNews API key
$url = "https://gnews.io/api/v4/top-headlines?category=entertainment&country=in&max=20&lang=en&apikey=" . $apiKey;

// Call API with cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decode JSON
$data = json_decode($response, true);
//echo " <pre>";
//print_r( $data);
//die;
$articles = $data['articles'] ?? $data['results'] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Global News</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="text-center mb-4">🌍 Global News (Top 20)</h2>

    <?php if (!empty($articles)) { ?>
        <div class="row">
            <?php foreach ($articles as $news) { ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($news['urlToImage'])) { ?>
                            <img src="<?php echo $news['urlToImage']; ?>" class="card-img-top" style="height:200px;object-fit:cover;">
                        <?php } ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars(substr($news['description'], 0, 100)) . '...'; ?>
                            </p>
                            <a href="<?php echo $news['url']; ?>" target="_blank" class="btn btn-dark mt-auto">Read More</a>
                        </div>
                        <div class="card-footer text-muted small">
                            <?php echo $news['source']['name'] ?? "Unknown Source"; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger text-center">❌ Failed to load news. Please try again later.</div>
    <?php } ?>
</div>

</body>
</html>