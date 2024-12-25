<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
    integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="css/footer.css?v=<?php echo time(); ?>" />
  <title>Bookstore - Home</title>
</head>

<body>
  <!-- Include the header -->
  <?php include 'includes/header.php'; ?>

  <!-- Main Content -->
  <main>
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <div class="text-content">
          <h1>Welcome to Our Bookly store</h1>
          <p>Discover your next favorite book from our wide collection.</p>
          <a href="store.php" class="btn">Browse Books</a>
        </div>
        <section class="image-content">
          <img src="images/book_vec.png" alt="book vector" class="book_vec">
        </section>
      </div>
    </section>


    <!-- Categories Section  -->
    <section class="category-slider">
      <div class="container">
        <h2>Shop by Category</h2>
        <div class="category-container">
          <?php
          // Define categories with images and descriptions (You can adjust as needed)
          $categories = [
            ['title' => 'Fiction', 'image' => 'fiction.jpg', 'description' => 'Explore the best of fictional stories.'],
            ['title' => 'Non-fiction', 'image' => 'non-fiction.jpg', 'description' => 'Discover facts, biographies, and more.'],
            ['title' => 'Science', 'image' => 'scientific.jpg', 'description' => 'Uncover the wonders of science.'],
            ['title' => 'History', 'image' => 'history.jpg', 'description' => 'Learn about the past and how it shaped us.'],
            ['title' => 'Fantasy', 'image' => 'fantasy.jpg', 'description' => 'Delve into magical and fantastical worlds.'],
            ['title' => 'Art', 'image' => 'art.jpg', 'description' => 'Explore works from different artistic movements.'],
          ];

          // Loop through categories and display them as cards
          foreach ($categories as $category) {
            echo "<a href='store.php?category=" . urlencode($category['title']) . "' class='category-link'>
              <div class='category-item'>
                <img src='images/{$category['image']}' alt='{$category['title']}'>
                <h3>{$category['title']}</h3>
                <p>{$category['description']}</p>
              </div>
            </a>";
          }

          ?>
        </div>
      </div>
    </section>


    <!-- Featured Books Section -->
    <section class="featured">
      <div class="container">
        <h2>Featured Books</h2>
        <div class="book-list">
          <?php
          // Include the database connection
          include 'php/db.php';

          // Query to fetch featured books
          $query = "SELECT * FROM books WHERE category IS NOT NULL LIMIT 4";  // Adjust the condition as necessary
          $result = mysqli_query($conn, $query);

          // Check if there are any books to display
          if (mysqli_num_rows($result) > 0) {
            // Loop through the books and display them
            while ($row = mysqli_fetch_assoc($result)) {
              // Use the image_url directly from the database
              $image_url = $row['image_url'];
              // Display book details
              echo "<div class='book'>
                        <img src='" . $image_url . "' alt='" . $row['title'] . "' />
                        <h3>" . $row['title'] . "</h3>
                        <p><strong>Author:</strong> " . $row['author'] . "</p>
                        <p><strong>Category:</strong> " . $row['category'] . "</p>
                        <p><strong>Price:</strong> $" . number_format($row['price'], 2) . "</p>
                        <p>" . substr($row['description'], 0, 100) . "...</p>
                      </div>";
            }
          } else {
            echo "<p>No featured books available at the moment.</p>";
          }

          // Close the database connection
          mysqli_close($conn);
          ?>
        </div>
      </div>
    </section>
  </main>



  <footer>
    <?php include 'includes/footer.php'; ?>
  </footer>
</body>

</html>