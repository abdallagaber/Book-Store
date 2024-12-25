<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include 'php/db.php';

    // Sanitize and fetch form inputs
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Insert into database
    $sql = "INSERT INTO messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
      echo "<script>
              window.onload = function() {
                alert('Your message has been sent successfully!');
              }
            </script>";
    } else {
      echo "<script>
              window.onload = function() {
                alert('Error: " . $conn->error . "');
              }
            </script>";
    }

    $conn->close();
  }
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/contact.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/footer.css?v=<?php echo time(); ?>" />
    <title>Contact Us</title>
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <main>
      <div class="container2">
        <span class="big-circle"></span>
        <img src="images/shape.png" class="square" alt="" />
        <div class="form">
          <div class="contact-info">
            <h3 class="title">Let's get in touch</h3>
            <p class="text">
              At Bookly, we’re here to help you find your next favorite read! Whether you have questions, need recommendations, or want to collaborate, feel free to reach out.
            </p>

            <div class="info">
              <div class="information">
                <img src="images/location.png" class="icon" alt="" />
                <p>123 Book Haven Lane, Novel City, NY 11553</p>
              </div>
              <div class="information">
                <img src="images/email.png" class="icon" alt="" />
                <p>support@bookly.com</p>
              </div>
              <div class="information">
                <img src="images/phone.png" class="icon" alt="" />
                <p>123-456-789</p>
              </div>
            </div>

            <div class="social-media">
              <p>Connect with us :</p>
              <div class="social-icons">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin"></i></a>
              </div>
            </div>
          </div>

          <div class="contact-form">
            <span class="circle one"></span>
            <span class="circle two"></span>

            <form action="contact.php" method="POST" autocomplete="off">
              <h3 class="title">Contact us</h3>
              <div class="input-container">
                <input type="text" name="name" class="input" required />
                <label for="">Name</label>
                <span>Name</span>
              </div>
              <div class="input-container">
                <input type="email" name="email" class="input" required />
                <label for="">Email</label>
                <span>Email</span>
              </div>
              <div class="input-container">
                <input type="text" name="subject" class="input" required />
                <label for="">Subject</label>
                <span>Subject</span>
              </div>
              <div class="input-container textarea">
                <textarea name="message" class="input" required></textarea>
                <label for="">Message</label>
                <span>Message</span>
              </div>
              <input type="submit" value="Send" class="btn" />
            </form>
          </div>
        </div>
      </div>
    </main>

    <footer>
      <?php include 'includes/footer.php'; ?>
    </footer>
    <script src="script/contact.js"></script>
  </body>
</html>
