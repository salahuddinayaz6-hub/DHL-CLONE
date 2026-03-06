<?php
require_once 'config.php';
 
// Fetch featured news
$featured_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                   au.name as author_name 
                   FROM articles a 
                   LEFT JOIN categories c ON a.category_id = c.id 
                   LEFT JOIN authors au ON a.author_id = au.id 
                   WHERE a.is_featured = 1 
                   ORDER BY a.published_at DESC 
                   LIMIT 3";
$featured_result = $conn->query($featured_query);
 
// Fetch breaking headlines
$breaking_query = "SELECT a.*, c.name as category_name 
                   FROM articles a 
                   LEFT JOIN categories c ON a.category_id = c.id 
                   WHERE a.is_breaking = 1 
                   ORDER BY a.published_at DESC 
                   LIMIT 5";
$breaking_result = $conn->query($breaking_query);
 
// Fetch trending stories (most viewed)
$trending_query = "SELECT a.*, c.name as category_name 
                   FROM articles a 
                   LEFT JOIN categories c ON a.category_id = c.id 
                   ORDER BY a.views DESC, a.published_at DESC 
                   LIMIT 5";
$trending_result = $conn->query($trending_query);
 
// Fetch all categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Breaking News, Latest News and Videos</title>
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
 
        /* Header Styles */
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
            animation: gradient 3s ease infinite;
        }
 
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
 
        .nav-menu {
            display: flex;
            gap: 1.5rem;
        }
 
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
        }
 
        .nav-menu a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
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
 
        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
 
        /* Breaking News Ticker */
        .breaking-news {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 30px rgba(255,107,107,0.3);
        }
 
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
 
        .breaking-label {
            background: white;
            color: #ff6b6b;
            padding: 0.3rem 1rem;
            border-radius: 5px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
 
        .breaking-titles {
            display: flex;
            gap: 2rem;
            overflow-x: auto;
            white-space: nowrap;
            padding: 0.5rem 0;
        }
 
        .breaking-titles a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
 
        .breaking-titles a:hover {
            text-decoration: underline;
            transform: translateX(5px);
        }
 
        /* Featured News Section */
        .featured-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
 
        .featured-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
 
        .featured-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
 
        .featured-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: all 0.5s ease;
        }
 
        .featured-card:hover img {
            transform: scale(1.05);
        }
 
        .featured-content {
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
 
        .featured-content h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
 
        .featured-content p {
            opacity: 0.9;
            font-size: 0.9rem;
            line-height: 1.5;
        }
 
        .category-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
        }
 
        /* Categories Section */
        .categories-section {
            margin-bottom: 3rem;
        }
 
        .section-title {
            color: white;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
 
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
 
        .category-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
 
        .category-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
 
        .category-item:hover::before {
            left: 100%;
        }
 
        .category-item:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
 
        /* Trending Section */
        .trending-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 3rem;
        }
 
        .trending-title {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            border-left: 5px solid #ff6b6b;
        }
 
        .trending-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
 
        .trending-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            border-radius: 10px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
 
        .trending-item:hover {
            transform: translateX(10px);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
 
        .trending-item h4 {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
 
        .trending-item .views {
            font-size: 0.8rem;
            opacity: 0.8;
        }
 
        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 3rem;
        }
 
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
 
        .footer-section h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
 
        .footer-section p {
            opacity: 0.8;
            line-height: 1.6;
        }
 
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
 
        /* Responsive Design */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
 
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
 
            .search-box input {
                width: 180px;
            }
 
            .featured-section {
                grid-template-columns: 1fr;
            }
 
            .breaking-titles {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
 
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
 
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo" onclick="redirectTo('index.php')">CNN NEWS</div>
            <nav class="nav-menu">
                <a href="#" onclick="redirectTo('index.php')">Home</a>
                <a href="#" onclick="redirectTo('categories.php')">Categories</a>
                <?php 
                if($categories_result->num_rows > 0) {
                    $categories_result->data_seek(0);
                    while($cat = $categories_result->fetch_assoc()) {
                        echo '<a href="#" onclick="redirectTo(\'category.php?slug='.$cat['slug'].'\')">'.$cat['name'].'</a>';
                    }
                }
                ?>
            </nav>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search news..." onkeypress="handleSearchKeyPress(event)">
                <button onclick="performSearch()">Search</button>
            </div>
        </div>
    </header>
 
    <!-- Main Content -->
    <div class="main-container">
        <!-- Breaking News -->
        <?php if($breaking_result->num_rows > 0): ?>
        <div class="breaking-news">
            <span class="breaking-label">BREAKING</span>
            <div class="breaking-titles">
                <?php while($breaking = $breaking_result->fetch_assoc()): ?>
                <a href="#" onclick="redirectTo('article.php?slug=<?php echo $breaking['slug']; ?>')">
                    <?php echo htmlspecialchars($breaking['title']); ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
 
        <!-- Featured News -->
        <?php if($featured_result->num_rows > 0): ?>
        <h2 class="section-title">FEATURED STORIES</h2>
        <div class="featured-section">
            <?php while($featured = $featured_result->fetch_assoc()): ?>
            <div class="featured-card" onclick="redirectTo('article.php?slug=<?php echo $featured['slug']; ?>')">
                <?php if($featured['featured_image']): ?>
                <img src="<?php echo htmlspecialchars($featured['featured_image']); ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
                <?php endif; ?>
                <span class="category-badge"><?php echo htmlspecialchars($featured['category_name']); ?></span>
                <div class="featured-content">
                    <h3><?php echo htmlspecialchars($featured['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($featured['excerpt'], 0, 100)) . '...'; ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
 
        <!-- Categories Section -->
        <h2 class="section-title">NEWS CATEGORIES</h2>
        <div class="categories-section">
            <div class="category-grid">
                <?php 
                $categories_result->data_seek(0);
                while($category = $categories_result->fetch_assoc()): 
                ?>
                <a href="#" class="category-item" onclick="redirectTo('category.php?slug=<?php echo $category['slug']; ?>')">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
 
        <!-- Trending Stories -->
        <?php if($trending_result->num_rows > 0): ?>
        <div class="trending-section">
            <h2 class="trending-title">🔥 TRENDING NOW</h2>
            <div class="trending-list">
                <?php while($trending = $trending_result->fetch_assoc()): ?>
                <a href="#" class="trending-item" onclick="redirectTo('article.php?slug=<?php echo $trending['slug']; ?>')">
                    <h4><?php echo htmlspecialchars($trending['title']); ?></h4>
                    <span class="views">👁️ <?php echo number_format($trending['views']); ?> views</span>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
 
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Your trusted source for breaking news, exclusive stories, and in-depth coverage from around the world.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p>Terms of Service</p>
                <p>Privacy Policy</p>
                <p>Advertise With Us</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <p>Facebook</p>
                <p>Twitter</p>
                <p>Instagram</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>
 
    <script>
        // Redirect function using JavaScript
        function redirectTo(url) {
            window.location.href = url;
        }
 
        // Search function
        function performSearch() {
            const query = document.getElementById('searchInput').value.trim();
            if(query) {
                redirectTo('search.php?q=' + encodeURIComponent(query));
            }
        }
 
        // Handle enter key in search
        function handleSearchKeyPress(event) {
            if(event.key === 'Enter') {
                performSearch();
            }
        }
 
        // Add loading animation on navigation
        document.addEventListener('click', function(e) {
            if(e.target.tagName === 'A' && e.target.getAttribute('href') !== '#') {
                document.body.style.cursor = 'wait';
            }
        });
 
        // Smooth scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    </script>
</body>
</html>
<?php $conn->close(); ?>
