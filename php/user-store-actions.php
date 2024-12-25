<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Ensure user is logged in for wishlist actions
    if (!$isLoggedIn) {
        echo json_encode(['status' => 'error', 'message' => 'Please login first']);
        exit;
    }

    $bookId = intval($_POST['book_id']);

    if ($_POST['action'] === 'add_to_wishlist') {
        // Check if book is already in wishlist
        $checkStmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND book_id = ?");
        $checkStmt->bind_param("ii", $userId, $bookId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows == 0) {
            // Add to wishlist
            $stmt = $conn->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $bookId);
            $success = $stmt->execute();
            echo json_encode(['status' => $success ? 'added' : 'error']);
        } else {
            // Already in wishlist, remove
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param("ii", $userId, $bookId);
            $success = $stmt->execute();
            echo json_encode(['status' => $success ? 'removed' : 'error']);
        }
        exit;
    }
    if ($_POST['action'] === 'add_to_cart') {
        // Check if the book is already in the cart
        $checkStmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND book_id = ?");
        $checkStmt->bind_param("ii", $userId, $bookId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows == 0) {
            // Add to cart
            $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $bookId);
            $success = $stmt->execute();
            echo json_encode(['status' => $success ? 'added' : 'error']);
        } else {
            // Increment quantity if already in cart
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param("ii", $userId, $bookId);
            $success = $stmt->execute();
            echo json_encode(['status' => $success ? 'updated' : 'error']);
        }
        exit;
    }
}
?>