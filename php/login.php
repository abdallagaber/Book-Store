<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['admin'];
            if ($user['admin']) {
                header("Location: ../admin.php");
                exit();
            } else {
                header("Location: ../account.php");
                exit();
            }
        } else {
            header("Location: ../signup_login.php?error=Incorrect+password");
            exit();
        }
    } else {
        header("Location: ../signup_login.php?error=User+not+found");
        exit();
    }
}
?>
