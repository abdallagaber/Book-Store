<?php
session_start();

include 'php/db.php';
include 'php/user-store-actions.php';

// Pagination settings
$results_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Categories array
$categories = [
    'All Categories', 'fiction', 'non-fiction', 'science', 'history', 'fantasy', 'art', 'biography',
    'business', 'children', 'comics', 'cookbooks', 'health', 'literature', 'music',
    'philosophy', 'poetry', 'psychology', 'religion', 'romance', 'self-help', 'social-science',
    'sports', 'technology', 'travel'
];

// Get search and category filters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : 'All Categories';

// Build the base WHERE clause
$where_clause = "WHERE 1=1";
$params = array($userId);
$types = "i";

if (!empty($search)) {
    $where_clause .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

if ($categoryFilter && $categoryFilter != 'All Categories') {
    $where_clause .= " AND category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

// Count total records
$count_sql = "SELECT COUNT(DISTINCT books.id) as total
              FROM books
              LEFT JOIN wishlist w ON books.id = w.book_id AND w.user_id = ?
              $where_clause";

$stmt = $conn->prepare($count_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $results_per_page);

// Main query for books
$sql = "SELECT books.*,
        CASE WHEN w.book_id IS NOT NULL THEN 1 ELSE 0 END AS in_wishlist
        FROM books
        LEFT JOIN wishlist w ON books.id = w.book_id AND w.user_id = ?
        $where_clause
        LIMIT ?, ?";

// Add pagination parameters
$params[] = $offset;
$params[] = $results_per_page;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/store.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/footer.css?v=<?php echo time(); ?>">

</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="store">
        <aside>
            <h3>Categories</h3>
            <ul class="list-unstyled">
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="store.php?category=<?php echo urlencode($category); ?>&search=<?php echo htmlspecialchars($search); ?>"
                           class="<?php echo $categoryFilter === $category ? 'active' : ''; ?>">
                            <?php echo ucfirst($category); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
        <div class="main-content">
            <!-- Search Form -->
            <form class="search-form" method="GET">
                <?php if ($categoryFilter && $categoryFilter !== 'All Categories'): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="Search by title or author"
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>

            <div class="books">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($book = $result->fetch_assoc()): ?>
                        <div class="book" data-book-id="<?php echo $book['id']; ?>">
                            <img src="<?php echo $book['image_url']; ?>" class="card-img-top book-image" alt="Book Image">
                            <h3><?php echo $book['title']; ?></h3>
                            <p>By <?php echo $book['author']; ?></p>
                            <p class="book-price"><strong>$<?php echo number_format($book['price'], 2); ?></strong></p>
                            <div class="book-actions">
                                <button class="add-to-cart" data-book-id="<?php echo $book['id']; ?>">
                                    Add to Cart
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4 4a1 1 0 0 1 1-1h1.5a1 1 0 0 1 .979.796L7.939 6H19a1 1 0 0 1 .979 1.204l-1.25 6a1 1 0 0 1-.979.796H9.605l.208 1H17a3 3 0 1 1-2.83 2h-2.34a3 3 0 1 1-4.009-1.76L5.686 5H5a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <button class="add-to-wishlist <?php echo $book['in_wishlist'] ? 'active' : ''; ?>"
                                        data-book-id="<?php echo $book['id']; ?>">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-results">No books found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo ($page-1); ?>&category=<?php echo urlencode($categoryFilter); ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                <?php else: ?>
                    <span class="disabled">Previous</span>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($start_page + 4, $total_pages);

                if ($end_page - $start_page < 4) {
                    $start_page = max(1, $end_page - 4);
                }

                if ($start_page > 1) {
                    echo '<a href="?page=1&category=' . urlencode($categoryFilter) . '&search=' . urlencode($search) . '">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="disabled">...</span>';
                    }
                }

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo '<span class="active">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '&category=' . urlencode($categoryFilter) . '&search=' . urlencode($search) . '">' . $i . '</a>';
                    }
                }

                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="disabled">...</span>';
                    }
                    echo '<a href="?page=' . $total_pages . '&category=' . urlencode($categoryFilter) . '&search=' . urlencode($search) . '">' . $total_pages . '</a>';
                }
                ?>

                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo ($page+1); ?>&category=<?php echo urlencode($categoryFilter); ?>&search=<?php echo urlencode($search); ?>">Next</a>
                <?php else: ?>
                    <span class="disabled">Next</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="script/user-store-actions.js"></script>
</body>
</html>

<?php $conn->close(); ?>