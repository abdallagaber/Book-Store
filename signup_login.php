<?php

session_start();

if (isset($_SESSION['user_id'])) {
  header("Location: account.php");
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/signup_login.css?v=<?php echo time(); ?>"/>
    <title>SignUp-LogIn</title>
  </head>
  <body>
    <header>
    <?php include 'includes/header.php'; ?>
    </header>
    <main>
      <div class="login-container">
          <div class="form-box login">
                <form action="php/login.php" method="POST">
                  <h1>Login</h1>
                  <div class="input-box">
                      <input type="email" name="email" placeholder="Email" required>
                      <i class="fa-solid fa-user"></i>
                  </div>
                  <div class="input-box">
                      <input type="password" name="password" placeholder="Password" required>
                      <i class="fa-solid fa-lock"></i>
                  </div>
                  <div class="forgot-link">
                      <a href="#">Forgot password?</a>
                  </div>
                  <button type="submit" class="btn">Login</button>
              </form>
          </div>

          <div class="form-box register">
                <form action="php/register.php" method="POST">
                  <h1>Registration</h1>
                  <div class="input-box">
                      <input type="text" name="name" placeholder="Username" required>
                      <i class="fa-solid fa-user"></i>
                  </div>
                  <div class="input-box">
                      <input type="email" name="email" placeholder="Email" required>
                      <i class="fa-solid fa-envelope"></i>
                  </div>
                  <div class="input-box">
                      <input type="password" name="password" placeholder="Password" required>
                      <i class="fa-solid fa-lock"></i>
                  </div>
                  <button type="submit" class="btn">Register</button>
              </form>
          </div>

          <div class="toggle-box">
              <div class="toggle-panel toggle-left">
                  <h1>Hello, Welcome!</h1>
                  <p>Don't have an Account?</p>
                  <button class="btn register-btn">Register</button>
              </div>
              <div class="toggle-panel toggle-right">
                  <h1>Welcome Back!</h1>
                  <p>Already have an Account?</p>
                  <button class="btn login-btn">Login</button>
              </div>
          </div>
      </div>
    </main>
  </body>
  <script>
    const container = document.querySelector('.login-container');
    const registerbtn = document.querySelector('.register-btn');
    const loginbtn = document.querySelector('.login-btn');

    registerbtn.addEventListener('click', ()=>{
    container.classList.add('active');
    });

    loginbtn.addEventListener('click', ()=>{
        container.classList.remove('active');
    });

    function displayMessage() {
      const urlParams = new URLSearchParams(window.location.search);
      const error = urlParams.get("error");
      const success = urlParams.get("success");

      if (error) {
        alert(decodeURIComponent(error)); // Show error message as alert
        window.history.replaceState(
          {},
          document.title,
          window.location.pathname
        );
      } else if (success) {
        alert(decodeURIComponent(success)); // Show success message as alert

        // Toggle to the login form
        const container = document.querySelector('.login-container');
        container.classList.remove("active");

        window.history.replaceState(
          {},
          document.title,
          window.location.pathname
        );
      }
    }

    displayMessage();
  </script>
</html>
