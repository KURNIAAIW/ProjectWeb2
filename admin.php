<?php
session_start();
include './config/db_connect.php';

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $correct_password = password_verify($password, mysqli_fetch_assoc($result)['password']);
    if ($correct_password) {
      $_SESSION['email'] = $email;
      $_SESSION['user_id'] = mysqli_fetch_assoc($result)['id'];
      $_SESSION['user_role'] = 'admin';
      header("Location: admin/dashboard.php");
      exit();
    } else {
      $error = "Invalid id karyawan or password";
    }
  } else {
    $error = "Invalid id karyawan or password";
  }
}

?>


<!DOCTYPE html>
<html style="width: 100vw; height: 100vh;" lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>



  <?php include './config/links_cdn.php' ?>
</head>

<body style="width: 100vw; height: 100vh;">
  <script src="assets/static/js/initTheme.js"></script>
  <div style="width: 100vw; height: 100vh;" id="auth">

    <div class="row h-100 bg-white">
      <div class="col-lg-5 col-12 container py-5 px-4">
        <div id="auth-left" class="d-flex flex-column justify-content-center mx-auto align-items-center">
          <div class="auth-logo"><img src="./assets//logo.jpg" alt="Logo">
          </div>

          <h3 class="auth-title">Log in Admin.</h3>
          <p class="auth-subtitle mb-5">Masuk dengan data yang Anda masukkan saat pendaftaran.</p>

          <form action="admin.php" method="post" style="width: 80%;">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password">
                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" id="eyeIcon" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" width="20" height="20">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                  </svg>
                </span>
              </div>
            </div>

            <div class="row px-5 mt-2">
              <button type="submit" class="btn btn-warning" style="background-color: #f59e0b;">Login</button>
            </div>
            <?php
            if (!empty($error)) {
              echo "<p class='text-danger'>$error</p>";
            }
            ?>
          </form>
          <div class="text-center mt-5">
            <p class="text-gray-600">Belum memiliki akun?<a href="register.php" class="font-bold">Daftar</a>.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-7 d-none d-lg-block">
        <div id="auth-right" style="background-image: url('./assets/auth-image.jpg'); background-size: cover;height: 100%">
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const passwordField = document.getElementById("password");
      const togglePassword = document.getElementById("togglePassword");
      const eyeIcon = document.getElementById("eyeIcon");

      togglePassword.addEventListener("click", () => {
        // Toggle the type attribute
        const isPassword = passwordField.getAttribute("type") === "password";
        passwordField.setAttribute("type", isPassword ? "text" : "password");

        // Toggle the eye icon
        eyeIcon.setAttribute(
          "d",
          isPassword ?
          "M1.5 8a8 8 0 0 1 13 0 8 8 0 0 1-13 0z M8 10a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" :
          "M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"
        );
      });
    });
  </script>
</body>

</html>