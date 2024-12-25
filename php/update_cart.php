<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = $_POST['cart_id'];
    $action = $_POST['action'];

    if ($action === 'increase') {
        // Increase quantity
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE id = ?";
    } elseif ($action === 'decrease') {
        // Decrease quantity
        $sql = "UPDATE cart SET quantity = quantity - 1 WHERE id = ?";
    } else {
        die("Invalid action.");
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cartId);
    $stmt->execute();

    // Check if quantity has reached zero
    if ($action === 'decrease') {
        $checkSql = "SELECT quantity FROM cart WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $cartId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $row = $checkResult->fetch_assoc();

        if ($row['quantity'] <= 0) {
            // Delete the product from the cart
            $deleteSql = "DELETE FROM cart WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $cartId);
            $deleteStmt->execute();
        }
    }

    header("Location: ../cart.php");
    exit();
}
?>
