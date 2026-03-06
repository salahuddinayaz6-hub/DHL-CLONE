<?php
require_once 'config.php';
 
$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';
 
// Get category info
$category_query = "SELECT * FROM categories WHERE slug = '$slug'";
$category_result = $conn->query($category_query);
 
if($category_result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
 
$category = $category_result->fetch_assoc();
 
// Get articles in this category
$articles_query = "SELECT a.*, au.name as author_name 
                   FROM articles a 
                   LEFT JOIN authors au ON a.author_id = au.id 
                   WHERE a.category_id = " . $category['id'] . " 
                   ORDER BY a.published_at DESC";
$articles_result = $conn->query($articles_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - <?php echo SITE_NAME; ?></title>
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
 
        .back-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
 
        .back-btn:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
 
        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
 
        .category-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 3rem 2rem;
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
 
        .category-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
 
        .category-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }
 
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
 
        .article-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            animation: fadeIn 0.5s ease;
        }
 
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
 
        .article-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
 
        .article-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: all 0.5s ease;
        }
 
        .article-card:hover img {
            transform: scale(1.1);
        }
 
        .article-content {
            padding: 1.5rem;
        }
 
        .article-content h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
 
        .article-content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
 
        .article-meta {
            display: flex;
            justify-content: space-between;
            color: #999;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }
 
        .no-articles {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 3rem;
            text-align: center;
            border-radius: 15px;
            color: white;
            font-size: 1.5rem;
            grid-column: 1 / -1;
        }
 
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
 
            .category-header h1 {
                font-size: 2rem;
            }
 
            .articles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo" onclick="redirectTo('index.php')">CNN NEWS</div>
            <button class="back-btn" onclick="redirectTo('index.php')">← Back to Home</button>
        </div>
    </header>
 
    <div class="main-container">
        <div class="category-header">
            <h1><?php echo htmlspecialchars($category['name']); ?></h1>
            <p><?php echo htmlspecialchars($category['description']); ?></p>
        </div>
 
        <div class="articles-grid">
            <?php if($articles_result->num_rows > 0): ?>
                <?php while($article = $articles_result->fetch_assoc()): ?>
                <div class="article-card" onclick="redirectTo('article.php?slug=<?php echo $article['slug']; ?>')">
                    <?php if($article['featured_image']): ?>
                    <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <?php endif; ?>
                    <div class="article-content">
                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($article['excerpt'], 0, 150)) . '...'; ?></p>
                        <div class="article-meta">
                            <span>By <?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></span>
                            <span>📅 <?php echo date('M d, Y', strtotime($article['published_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-articles">
                    No articles found in this category yet. Check back soon!
                </div>
            <?php endif; ?>
        </div>
    </div>
 
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
