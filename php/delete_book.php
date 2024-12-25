<?php
// delete_book.php
session_start();

// Database connection
include 'db.php';

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $book_id = (int)$_POST['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // First delete from wishlist
        $wishlist_query = "DELETE FROM wishlist WHERE book_id = ?";
        $stmt = $conn->prepare($wishlist_query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();

        // Then delete from cart
        $cart_query = "DELETE FROM cart WHERE book_id = ?";
        $stmt = $conn->prepare($cart_query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();

        // Finally delete the book
        $book_query = "DELETE FROM books WHERE id = ?";
        $stmt = $conn->prepare($book_query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();

        // If everything is successful, commit the transaction
        $conn->commit();
        $_SESSION['message'] = "Book deleted successfully!";

    } catch (Exception $e) {
        // If there's an error, rollback the changes
        $conn->rollback();
        $_SESSION['error'] = "Error deleting book: " . $e->getMessage();
    }

    $conn->close();
    header("Location: ../admin.php");
    exit();
}
?>