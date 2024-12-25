<?php

session_start();
// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: signup_login.php");
    exit();
}

// Database connection
include 'php/db.php';


// Pagination settings
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = " WHERE title LIKE '%$search%' OR id = '$search'";
}

// Get total number of records for pagination
$total_query = "SELECT COUNT(*) as count FROM books" . $search_condition;
$total_result = $conn->query($total_query);
$total_records = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_records / $results_per_page);

// Get books with pagination and search - Changed ORDER BY to ASC
$query = "SELECT * FROM books" . $search_condition . " ORDER BY id ASC LIMIT $offset, $results_per_page";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Store Admin</title>
    <link rel="stylesheet" href="css/admin.css?v=<?php echo time(); ?>" />
</head>
<body>
    <div class="container">
        <?php
        // Display success message
        if (isset($_SESSION['message'])) {
            echo '<div class="alert success">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']);
        }

        // Display error message
        if (isset($_SESSION['error'])) {
            echo '<div class="alert error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <div class="header">
            <h2>Admin Panel</h2>
            <a href="add_book.php" class="add-button">Add New Book</a>
        </div>

        <!-- Search Form -->
        <form class="search-form" method="GET">
            <input type="text" name="search" placeholder="Search by ID or Title" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Books Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>
                        <?php if($row['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Book cover">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 100))); ?>...</td>
                    <td class="action-buttons">
                        <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="edit-button">Edit</a>
                        <form action="php/delete_book.php" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this book? This will also remove it from all wishlists and carts.');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo ($page-1); ?>&search=<?php echo urlencode($search); ?>">Previous</a>
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
                echo '<a href="?page=1&search=' . urlencode($search) . '">1</a>';
                if ($start_page > 2) {
                    echo '<span class="disabled">...</span>';
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $page) {
                    echo '<span class="active">' . $i . '</span>';
                } else {
                    echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a>';
                }
            }

            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="disabled">...</span>';
                }
                echo '<a href="?page=' . $total_pages . '&search=' . urlencode($search) . '">' . $total_pages . '</a>';
            }
            ?>

            <?php if($page < $total_pages): ?>
                <a href="?page=<?php echo ($page+1); ?>&search=<?php echo urlencode($search); ?>">Next</a>
            <?php else: ?>
                <span class="disabled">Next</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
