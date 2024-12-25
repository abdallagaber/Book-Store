<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signup_login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch cart items for the user
$sql = "SELECT cart.id AS cart_id, books.title, books.price, books.image_url, cart.quantity
        FROM cart
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$totalPrice = 0; // Initialize total price
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/cart.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="cart-container container">
        <h2>Shooping Cart</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $subtotal = $row['price'] * $row['quantity'];
                        $totalPrice += $subtotal; // Add to total price
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Book Image" style="width: 100px; height: auto;">
                            </td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form action="php/update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit">+</button>
                                </form>
                                <form action="php/update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit">-</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="total-price">
                <h3>Total Price: $<?php echo number_format($totalPrice, 2); ?></h3>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
    <footer>
    <?php include 'includes/footer.php'; ?>
    </footer>
</body>
</html>
