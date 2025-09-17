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
<html>
<head>
  <title>Global News - Top 20</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .news-item { border-bottom: 1px solid #ccc; padding: 10px 0; }
    .news-item h3 { margin: 0; font-size: 18px; }
    .news-item p { margin: 5px 0; color: #555; }
  </style>
</head>
<body>
  <h1>🌍 Global News (Top 20)</h1>

  <?php if (empty($articles)): ?>
    <p>No news found. Please check your API key or try later.</p>
  <?php else: ?>
    <?php foreach ($articles as $article): ?>
      <div class="news-item">
        <h3>
          <a href="<?php echo htmlspecialchars($article['url']); ?>" target="_blank">
            <?php echo htmlspecialchars($article['title']); ?>
          </a>
        </h3>
        <p><?php echo htmlspecialchars($article['description'] ?? ''); ?></p>
                <p> <img src="<?php echo htmlspecialchars($article['image'] ?? ''); ?>"/></p>

      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
