<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: signup_login.php");
    exit();
}

// Include database connection to fetch user data
include 'php/db.php';

// Fetch user information
$user_id = $_SESSION['user_id'];
$query = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch Wishlist data for the user
$wishlistQuery = "
    SELECT books.*
    FROM wishlist
    INNER JOIN books ON wishlist.book_id = books.id
    WHERE wishlist.user_id = ?";
$wishlistStmt = $conn->prepare($wishlistQuery);
$wishlistStmt->bind_param('i', $user_id);
$wishlistStmt->execute();
$wishlistResult = $wishlistStmt->get_result();
$wishlistItems = $wishlistResult->fetch_all(MYSQLI_ASSOC);

// Handle AJAX User actions
include 'php/user-store-actions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/store.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/account.css?v=<?php echo time(); ?>">
    <title>Account</title>
</head>
<body>
    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main>
        <div class="container">
            <section class="user-info">
                <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                <p>This is your account page.</p>
                <a class="logout-button" href="php/logout.php">Logout</a>
            </section>

            <!-- Display Wishlist -->
            <section class="wishlist-section">
                <h2>Your Wishlist</h2>
                <div class="books">
                <?php if (count($wishlistItems) > 0): ?>
                    <?php foreach ($wishlistItems as $book): ?>
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
                            <button class="add-to-wishlist active" data-book-id="<?php echo $book['id']; ?>">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your Wishlist is empty.</p>
                <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer>
    <?php include 'includes/footer.php'; ?>
    </footer>

    <script src="script/user-store-actions.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        const wishlistButtons = document.querySelectorAll('.add-to-wishlist');

        wishlistButtons.forEach(button => {
            button.addEventListener('click', async (event) => {
                location.reload()
            });
        });
    });

    </script>
</body>
</html>
