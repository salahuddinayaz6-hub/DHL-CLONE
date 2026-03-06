<?php
require_once 'config.php';
 
$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';
 
// Update view count
$update_views = "UPDATE articles SET views = views + 1 WHERE slug = '$slug'";
$conn->query($update_views);
 
// Get article details
$article_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                  au.name as author_name, au.bio as author_bio, au.avatar as author_avatar
                  FROM articles a 
                  LEFT JOIN categories c ON a.category_id = c.id 
                  LEFT JOIN authors au ON a.author_id = au.id 
                  WHERE a.slug = '$slug'";
$article_result = $conn->query($article_query);
 
if($article_result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
 
$article = $article_result->fetch_assoc();
 
// Get related links
$related_query = "SELECT * FROM related_links WHERE article_id = " . $article['id'] . " ORDER BY created_at DESC";
$related_result = $conn->query($related_query);
 
// Get comments
$comments_query = "SELECT * FROM comments WHERE article_id = " . $article['id'] . " AND is_approved = 1 ORDER BY created_at DESC";
$comments_result = $conn->query($comments_query);
 
// Handle comment submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $content = $conn->real_escape_string($_POST['content']);
 
    $insert_comment = "INSERT INTO comments (article_id, author_name, author_email, content) 
                       VALUES (" . $article['id'] . ", '$name', '$email', '$content')";
 
    if($conn->query($insert_comment)) {
        echo "<script>alert('Comment submitted for approval!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - <?php echo SITE_NAME; ?></title>
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
 
        .nav-buttons {
            display: flex;
            gap: 1rem;
        }
 
        .nav-btn {
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
 
        .nav-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
 
        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
 
        .article-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: slideUp 0.5s ease;
        }
 
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
 
        .article-header {
            margin-bottom: 2rem;
        }
 
        .category-tag {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
 
        .category-tag:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255,107,107,0.3);
        }
 
        .article-header h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
 
        .article-meta {
            display: flex;
            gap: 2rem;
            color: #666;
            font-size: 0.95rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
 
        .author-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0;
            padding: 1rem;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
        }
 
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
 
        .author-details h3 {
            color: #333;
            margin-bottom: 0.3rem;
        }
 
        .author-details p {
            color: #666;
            font-size: 0.9rem;
        }
 
        .featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
 
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            margin: 2rem 0;
        }
 
        .article-content p {
            margin-bottom: 1.5rem;
        }
 
        .related-links {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
 
        .related-links h3 {
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
 
        .related-links ul {
            list-style: none;
        }
 
        .related-links li {
            margin-bottom: 0.5rem;
        }
 
        .related-links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
 
        .related-links a:hover {
            transform: translateX(10px);
            text-decoration: underline;
        }
 
        .comments-section {
            margin-top: 3rem;
        }
 
        .comments-section h3 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
 
        .comment {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            animation: fadeIn 0.5s ease;
        }
 
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }
 
        .comment-name {
            font-weight: 600;
            color: #333;
        }
 
        .comment-content {
            color: #555;
            line-height: 1.6;
        }
 
        .comment-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
        }
 
        .comment-form h4 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
 
        .form-group {
            margin-bottom: 1rem;
        }
 
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }
 
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
 
        .submit-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
 
        .submit-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255,107,107,0.3);
        }
 
        .view-count {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: #f0f0f0;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
 
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
 
            .article-container {
                padding: 1.5rem;
            }
 
            .article-header h1 {
                font-size: 1.8rem;
            }
 
            .article-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo" onclick="redirectTo('index.php')">CNN NEWS</div>
            <div class="nav-buttons">
                <button class="nav-btn" onclick="redirectTo('index.php')">Home</button>
                <button class="nav-btn" onclick="redirectTo('categories.php')">Categories</button>
                <button class="nav-btn" onclick="goBack()">← Back</button>
            </div>
        </div>
    </header>
 
    <div class="main-container">
        <article class="article-container">
            <div class="article-header">
                <span class="category-tag" onclick="redirectTo('category.php?slug=<?php echo $article['category_slug']; ?>')">
                    <?php echo htmlspecialchars($article['category_name']); ?>
                </span>
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="article-meta">
                    <span>📅 <?php echo date('F j, Y', strtotime($article['published_at'])); ?></span>
                    <span>⏱️ <?php echo rand(3, 8); ?> min read</span>
                    <span class="view-count">👁️ <?php echo number_format($article['views']); ?> views</span>
                </div>
            </div>
 
            <?php if($article['author_name']): ?>
            <div class="author-info">
                <img src="<?php echo htmlspecialchars($article['author_avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($article['author_name']).'&size=60'); ?>" 
                     alt="<?php echo htmlspecialchars($article['author_name']); ?>" 
                     class="author-avatar">
                <div class="author-details">
                    <h3><?php echo htmlspecialchars($article['author_name']); ?></h3>
                    <p><?php echo htmlspecialchars($article['author_bio'] ?? 'Journalist'); ?></p>
                </div>
            </div>
            <?php endif; ?>
 
            <?php if($article['featured_image']): ?>
            <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" 
                 alt="<?php echo htmlspecialchars($article['title']); ?>" 
                 class="featured-image">
            <?php endif; ?>
 
            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($article['content'])); ?>
            </div>
 
            <?php if($related_result->num_rows > 0): ?>
            <div class="related-links">
                <h3>📰 Related News</h3>
                <ul>
                    <?php while($related = $related_result->fetch_assoc()): ?>
                    <li><a href="#" onclick="redirectTo('<?php echo $related['url']; ?>')"><?php echo htmlspecialchars($related['title']); ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php endif; ?>
 
            <div class="comments-section">
                <h3>💬 Comments (<?php echo $comments_result->num_rows; ?>)</h3>
 
                <?php if($comments_result->num_rows > 0): ?>
                    <?php while($comment = $comments_result->fetch_assoc()): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <span class="comment-name"><?php echo htmlspecialchars($comment['author_name']); ?></span>
                            <span><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></span>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #666; margin-bottom: 1rem;">No comments yet. Be the first to comment!</p>
                <?php endif; ?>
 
                <div class="comment-form">
                    <h4>Leave a Comment</h4>
                    <form method="POST" action="">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Your Email (optional)">
                        </div>
                        <div class="form-group">
                            <textarea name="content" placeholder="Your Comment" required></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="submit-btn">Post Comment</button>
                    </form>
                </div>
            </div>
        </article>
    </div>
 
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
 
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
