<!-- article.php - Article page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - Article</title>
    <style>
        /* Internal CSS - Amazing, real-looking, responsive design */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        header { background-color: #c00; color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 2.5em; }
        nav { background-color: #333; padding: 10px; }
        nav ul { list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; }
        nav ul li { margin: 0 15px; }
        nav ul li a { color: white; text-decoration: none; font-weight: bold; }
        .search-bar { text-align: center; margin: 20px 0; }
        .search-bar input { padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        .search-bar button { padding: 10px 20px; background-color: #c00; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .article { background-color: white; margin: 20px; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .article img { width: 100%; height: auto; border-radius: 4px; margin-bottom: 20px; }
        .article h2 { color: #c00; }
        .article .meta { font-size: 0.9em; color: #666; margin-bottom: 20px; }
        .article .content { line-height: 1.6; }
        .related { margin-top: 30px; }
        .related h3 { color: #c00; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
        .related-item { cursor: pointer; }
        .comments { margin-top: 30px; }
        .comments h3 { color: #c00; }
        .comment-form { margin-top: 20px; }
        .comment-form input, .comment-form textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .comment-form button { padding: 10px 20px; background-color: #c00; color: white; border: none; border-radius: 4px; cursor: pointer; }
        footer { background-color: #333; color: white; text-align: center; padding: 10px; margin-top: 20px; }
        @media (max-width: 768px) {
            nav ul { flex-direction: column; }
            .search-bar input { width: 80%; }
        }
    </style>
</head>
<body>
    <header>
        <h1>DHL</h1>
    </header>
    <nav>
        <ul>
            <li><a href="#" onclick="redirectTo('index.php')">Home</a></li>
            <li><a href="#" onclick="redirectTo('category.php?cat=World')">World</a></li>
            <li><a href="#" onclick="redirectTo('category.php?cat=Politics')">Politics</a></li>
            <li><a href="#" onclick="redirectTo('category.php?cat=Business')">Business</a></li>
            <li><a href="#" onclick="redirectTo('category.php?cat=Technology')">Technology</a></li>
            <li><a href="#" onclick="redirectTo('category.php?cat=Sports')">Sports</a></li>
            <li><a href="#" onclick="redirectTo('category.php?cat=Entertainment')">Entertainment</a></li>
        </ul>
    </nav>
    <div class="search-bar">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search articles...">
            <button type="submit">Search</button>
        </form>
    </div>
    <section class="article">
        <?php
        include 'db.php';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            // Update views
            $conn->query("UPDATE news_articles SET views = views + 1 WHERE id = $id");
 
            $sql = "SELECT * FROM news_articles WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo '<h2>' . $row['title'] . '</h2>';
                echo '<div class="meta">By ' . $row['author'] . ' | ' . $row['publish_date'] . ' | Views: ' . $row['views'] . '</div>';
                if ($row['image_url']) echo '<img src="' . $row['image_url'] . '" alt="' . $row['title'] . '">';
                echo '<div class="content">' . nl2br($row['content']) . '</div>';
 
                // Related news
                echo '<div class="related">';
                echo '<h3>Related News</h3>';
                $cat = $row['category'];
                $related_sql = "SELECT * FROM news_articles WHERE category = ? AND id != ? ORDER BY publish_date DESC LIMIT 3";
                $related_stmt = $conn->prepare($related_sql);
                $related_stmt->bind_param("si", $cat, $id);
                $related_stmt->execute();
                $related_result = $related_stmt->get_result();
                echo '<div class="related-grid">';
                while($rel_row = $related_result->fetch_assoc()) {
                    echo '<div class="related-item" onclick="redirectTo(\'article.php?id=' . $rel_row['id'] . '\')">';
                    echo '<h4>' . $rel_row['title'] . '</h4>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
                $related_stmt->close();
 
                // Comments
                echo '<div class="comments">';
                echo '<h3>Comments</h3>';
                $comments_sql = "SELECT * FROM comments WHERE article_id = ? ORDER BY comment_date DESC";
                $comments_stmt = $conn->prepare($comments_sql);
                $comments_stmt->bind_param("i", $id);
                $comments_stmt->execute();
                $comments_result = $comments_stmt->get_result();
                while($com_row = $comments_result->fetch_assoc()) {
                    echo '<p><strong>' . $com_row['user_name'] . '</strong> (' . $com_row['comment_date'] . '): ' . nl2br($com_row['comment']) . '</p>';
                }
                $comments_stmt->close();
 
                // Comment form
                echo '<div class="comment-form">';
                echo '<form method="POST">';
                echo '<input type="text" name="user_name" placeholder="Your Name" required>';
                echo '<textarea name="comment" placeholder="Your Comment" required></textarea>';
                echo '<button type="submit">Post Comment</button>';
                echo '</form>';
                echo '</div>';
 
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $user_name = htmlspecialchars($_POST['user_name']);
                    $comment = htmlspecialchars($_POST['comment']);
                    $insert_sql = "INSERT INTO comments (article_id, user_name, comment) VALUES (?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("iss", $id, $user_name, $comment);
                    $insert_stmt->execute();
                    $insert_stmt->close();
                    echo '<script>redirectTo("article.php?id=' . $id . '");</script>';
                }
 
                echo '</div>';
            } else {
                echo '<p>Article not found.</p>';
            }
            $stmt->close();
        } else {
            echo '<p>Invalid article ID.</p>';
        }
        $conn->close();
        ?>
    </section>
    <footer>
        <p>&copy; 2025 News Website. All rights reserved.</p>
    </footer>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
