<?php
require_once 'config.php';
 
$search_query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
 
if(empty($search_query)) {
    header("Location: index.php");
    exit();
}
 
// Search articles
$articles_query = "SELECT a.*, c.name as category_name, c.slug as category_slug,
                  au.name as author_name
                  FROM articles a 
                  LEFT JOIN categories c ON a.category_id = c.id 
                  LEFT JOIN authors au ON a.author_id = au.id 
                  WHERE a.title LIKE '%$search_query%' 
                  OR a.content LIKE '%$search_query%'
                  OR a.excerpt LIKE '%$search_query%'
                  ORDER BY a.published_at DESC";
$articles_result = $conn->query($articles_query);
$total_results = $articles_result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results: "<?php echo htmlspecialchars($search_query); ?>" - <?php echo SITE_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
 
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
 
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
 
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
 
        .logo {
            font-size: 2.2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            cursor: pointer;
        }
 
        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 30px;
            padding: 0.3rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
 
        .search-box input {
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            outline: none;
            width: 250px;
            font-size: 0.9rem;
        }
 
        .search-box button {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
 
        .search-box button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255,107,107,0.4);
        }
 
        .back-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 1rem;
        }
 
        .back-btn:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
 
        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
 
        .search-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            animation: slideDown 0.5s ease;
        }
 
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
 
        .search-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
 
        .search-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
 
        .results-count {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            margin-top: 1rem;
        }
 
        .articles-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
 
        .article-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
 
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
 
        .article-item:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
 
        .article-item h3 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }
 
        .article-item p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
 
        .article-meta {
            display: flex;
            gap: 1rem;
            color: #999;
            font-size: 0.9rem;
        }
 
        .no-results {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 3rem;
            text-align: center;
            border-radius: 15px;
            color: white;
        }
 
        .no-results h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
 
        .suggestions {
            margin-top: 2rem;
            color: rgba(255,255,255,0.8);
        }
 
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
 
            .search-box {
                width: 100%;
            }
 
            .search-box input {
                width: 100%;
            }
 
            .search-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo" onclick="redirectTo('index.php')">CNN NEWS</div>
            <div style="display: flex; gap: 1rem;">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search news..." value="<?php echo htmlspecialchars($search_query); ?>" onkeypress="handleSearchKeyPress(event)">
                    <button onclick="performSearch()">Search</button>
                </div>
                <button class="back-btn" onclick="redirectTo('index.php')">Home</button>
            </div>
        </div>
    </header>
 
    <div class="main-container">
        <div class="search-header">
            <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
            <p>Found <?php echo $total_results; ?> articles matching your search</p>
            <span class="results-count">🔍 <?php echo $total_results; ?> results</span>
        </div>
 
        <?php if($total_results > 0): ?>
        <div class="articles-list">
            <?php while($article = $articles_result->fetch_assoc()): ?>
            <div class="article-item" onclick="redirectTo('article.php?slug=<?php echo $article['slug']; ?>')">
                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                <p><?php echo htmlspecialchars(substr($article['excerpt'], 0, 200)) . '...'; ?></p>
                <div class="article-meta">
                    <span>📁 <?php echo htmlspecialchars($article['category_name']); ?></span>
                    <span>✍️ By <?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></span>
                    <span>📅 <?php echo date('M d, Y', strtotime($article['published_at'])); ?></span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-results">
            <h2>😕 No articles found</h2>
            <p>We couldn't find any articles matching "<?php echo htmlspecialchars($search_query); ?>"</p>
            <div class="suggestions">
                <p>Suggestions:</p>
                <ul style="list-style: none; margin-top: 1rem;">
                    <li>• Check your spelling</li>
                    <li>• Try more general keywords</li>
                    <li>• Try different keywords</li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
 
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
 
        function performSearch() {
            const query = document.getElementById('searchInput').value.trim();
            if(query) {
                redirectTo('search.php?q=' + encodeURIComponent(query));
            }
        }
 
        function handleSearchKeyPress(event) {
            if(event.key === 'Enter') {
                performSearch();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
