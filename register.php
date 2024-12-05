<?php
session_start();
include './config/db_connect.php';

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $password_confirmation = $_POST['password_confirmation'];

  // Validate input
  if (empty($name) || empty($email) || empty($password) || empty($password_confirmation)) {
    $error = "All fields are required.";
  } elseif ($password !== $password_confirmation) {
    $error = "Passwords do not match.";
  } else {
    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $error = "Email is already registered.";
    } else {
      // Hash password
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Insert new user into the database
      $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
      $stmt->bind_param("sss", $name, $email, $hashed_password);
      $stmt->execute();
      if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit();
      } else {
        $error = "Error: " . mysqli_error($conn);
      }
    }
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>



  <?php include './config/links_cdn.php' ?>
</head>

<body>
  <script src="assets/static/js/initTheme.js"></script>
  <div id="auth">

    <div class="row h-100 bg-white">
      <div class="col-lg-5 col-12 container py-5 px-4">
        <div id="auth-left" class="d-flex flex-column justify-content-center mx-auto align-items-center">
          <div class="auth-logo"><img src="./assets/logo.jpg" alt="Logo">
          </div>

          <h3 class="auth-title">Daftar</h3>
          <p class="auth-subtitle mb-5">Daftarkan diri Anda dengan mengisi data di bawah ini.</p>

          <form action="register.php" method="post" style="width: 95%;">
            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap</label>
              <input type="name" class="form-control" id="name" name="name">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>
            <div class="row px-5 mt-2">
              <button type="submit" class="btn btn-warning">Daftar</button>
            </div>
            <?php
            if (!empty($error)) {
              echo "<p class='text-danger'>$error</p>";
            }
            ?>

            <div class="text-center mt-3">

            </div>
          </form>
          <div class="text-center mt-5">
            <p>Sudah punya akun? <a href="index.php">Login</a></p>
          </div>
        </div>
      </div>
      <div class="col-lg-7 d-none d-lg-block">
        <div id="auth-right" style="background-image: url('./assets/auth-image.jpg'); background-size: cover;height: 100%">
        </div>
      </div>
    </div>

  </div>
</body>

</html>