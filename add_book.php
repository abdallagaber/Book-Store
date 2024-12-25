<?php
session_start();
// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: signup_login.php");
    exit();
}

// Database connection
include 'php/db.php';

// Predefined categories
$categories = [
    'fiction', 'non-fiction', 'science', 'history', 'fantasy', 'art', 'biography',
    'business', 'children', 'comics', 'cookbooks', 'health', 'literature', 'music',
    'philosophy', 'poetry', 'psychology', 'religion', 'romance', 'self-help', 'social-science',
    'sports', 'technology', 'travel'
];

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);

    // Validate inputs
    if (empty($title) || empty($author) || empty($category) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        // Validate image URL
        if (!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL)) {
            $error = "Invalid image URL format.";
        }

        // Insert data into database
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO books (title, author, category, price, image_url, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdss", $title, $author, $category, $price, $image_url, $description);

            if ($stmt->execute()) {
                $success = "Book added successfully!";
                header("Location: admin.php");
                exit();
            } else {
                $error = "Error adding book: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-button:hover {
            background-color: #218838;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .current-image {
            max-width: 200px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Add Book</h2>
            <a href="admin.php" class="back-button">Back to List</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="author">Author *</label>
                <input type="text" id="author" name="author" required>
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo ucfirst($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="image_url">Book Cover Image URL</label>
                <input type="text" id="image_url" name="image_url" placeholder="Enter image URL">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <button type="submit" class="submit-button">Add Book</button>
        </form>
    </div>
</body>
</html>
