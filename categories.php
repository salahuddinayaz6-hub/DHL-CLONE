<?php
require_once 'config.php';
 
// Fetch all categories with article counts
$categories_query = "SELECT c.*, COUNT(a.id) as article_count 
                     FROM categories c 
                     LEFT JOIN articles a ON c.id = a.category_id 
                     GROUP BY c.id 
                     ORDER BY c.name";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - <?php echo SITE_NAME; ?></title>
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
            margin: 3rem auto;
            padding: 0 2rem;
        }
 
        .page-title {
            text-align: center;
            color: white;
            font-size: 3rem;
            margin-bottom: 3rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: glow 2s ease-in-out infinite;
        }
 
        @keyframes glow {
            0%, 100% { text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
            50% { text-shadow: 0 0 20px rgba(255,255,255,0.8); }
        }
 
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
 
        .category-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
 
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
 
        .category-card:hover::before {
            left: 100%;
        }
 
        .category-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
 
        .category-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
 
        .category-card h3 {
            color: white;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
 
        .category-card p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 1rem;
        }
 
        .article-count {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
 
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
 
            .page-title {
                font-size: 2rem;
            }
 
            .categories-grid {
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
        <h1 class="page-title">NEWS CATEGORIES</h1>
 
        <div class="categories-grid">
            <?php while($category = $categories_result->fetch_assoc()): ?>
            <div class="category-card" onclick="redirectTo('category.php?slug=<?php echo $category['slug']; ?>')">
                <div class="category-icon">
                    <?php
                    $icons = [
                        'World' => '🌍',
                        'Politics' => '🏛️',
                        'Business' => '💼',
                        'Technology' => '💻',
                        'Sports' => '⚽',
                        'Entertainment' => '🎬'
                    ];
                    echo $icons[$category['name']] ?? '📰';
                    ?>
                </div>
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                <p><?php echo htmlspecialchars($category['description']); ?></p>
                <span class="article-count"><?php echo $category['article_count']; ?> Articles</span>
            </div>
            <?php endwhile; ?>
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
