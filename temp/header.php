<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<header>
  <div class="container">
    <div class="logo-box">
      <a class="logo" href="index.php">
        <img src="images/book.png" alt="Website Logo" />
        <span>Bookly</span>
      </a>
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="store.php">Store</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
    </nav>
    <div class="user-actions">
      <!-- Dynamically change link based on login status -->
      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- User is logged in -->
        <a href="account.php" class="signup-button">
          <img src="images/user.png" alt="Account icon" />
        </a>
      <?php else: ?>
        <!-- User is NOT logged in -->
        <a href="signup_login.php" class="signup-button">
          <img src="images/user.png" alt="Account icon" />
        </a>
      <?php endif; ?>

      <!-- Cart icon (stays static) -->
      <a href="cart.php" class="login-button">
        <img src="images/shopping-bag.png" alt="Cart icon" />
      </a>
    </div>
  </div>
</header>
