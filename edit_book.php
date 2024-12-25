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

$book = null;
$error = '';
$success = '';

// Get book details if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if (!$book) {
        header("Location: admin.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    $current_image = $_POST['current_image'];

    // Validate inputs
    if (empty($title) || empty($author) || empty($category) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        // Handle image URL
        $image_url = $current_image;
        if (!empty($_POST['image_url'])) {
            $image_url = trim($_POST['image_url']);
            if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
                $error = "Invalid image URL format.";
            } else {
                // Delete old image if exists and different
                if ($current_image && $current_image != $image_url && file_exists($current_image)) {
                    unlink($current_image);
                }
            }
        }

        // Update database
        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE books SET title=?, author=?, category=?, price=?, image_url=?, description=? WHERE id=?");
            $stmt->bind_param("sssdssi", $title, $author, $category, $price, $image_url, $description, $id);

            if ($stmt->execute()) {
                $success = "Book updated successfully!";
            } else {
                $error = "Error updating book: " . $conn->error;
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
    <title>Edit Book</title>
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
            <h2>Edit Book</h2>
            <a href="admin.php" class="back-button">Back to List</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($book): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $book['image_url']; ?>">

            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="author">Author *</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo $book['category'] == $cat ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $book['price']; ?>" required>
            </div>

            <div class="form-group">
                <label for="image_url">Book Cover Image URL</label>
                <?php if ($book['image_url']): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Current book cover" class="current-image">
                    </div>
                <?php endif; ?>
                <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($book['image_url']); ?>" placeholder="Enter image URL">
                <small>Leave empty to keep the current image</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($book['description']); ?></textarea>
            </div>

            <button type="submit" class="submit-button">Update Book</button>
        </form>
        <?php else: ?>
            <div class="alert alert-error">Book not found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
