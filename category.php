<!-- category.php - Category page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - Category</title>
    <style>
        /* Internal CSS - Amazing, real-looking, responsive design (similar to homepage) */
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
        .category-section { background-color: white; margin: 20px; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .category-section h2 { color: #c00; }
        .news-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .news-item { background-color: white; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); cursor: pointer; }
        .news-item img { width: 100%; height: auto; border-radius: 4px; }
        .news-item h3 { margin: 10px 0; }
        .news-item p { font-size: 0.9em; color: #666; }
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
    <section class="category-section">
        <?php
        include 'db.php';
        $cat = isset($_GET['cat']) ? $_GET['cat'] : 'World';
        echo '<h2>' . htmlspecialchars($cat) . ' News</h2>';
        $sql = "SELECT * FROM news_articles WHERE category = ? ORDER BY publish_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cat);
        $stmt->execute();
        $result = $stmt->get_result();
        echo '<div class="news-grid">';
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="news-item" onclick="redirectTo(\'article.php?id=' . $row['id'] . '\')">';
                if ($row['image_url']) echo '<img src="' . $row['image_url'] . '" alt="' . $row['title'] . '">';
                echo '<h3>' . $row['title'] . '</h3>';
                echo '<p>' . substr($row['content'], 0, 100) . '...</p>';
                echo '<p>By ' . $row['author'] . ' | ' . $row['publish_date'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No news in this category.</p>';
        }
        echo '</div>';
        $stmt->close();
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
