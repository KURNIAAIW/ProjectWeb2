<?php

include '../config/db_connect.php';
include '../auth/mw_user.php';

$employee_id = $_SESSION['employee_id'];

$result = mysqli_query($conn, "SELECT * FROM User WHERE employee_id = '$employee_id'");

$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <?php include '../config/links_cdn.php' ?>
</head>


<body>
  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>


  <div id="app">
    <?php include '../components/user/sidebar.php' ?>
    <div id="main">
      <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
          <i class="bi bi-justify fs-3"></i>
        </a>
      </header>

      <div class="page-heading">
        <div class="page-title">
          <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
              <h3>Profile</h3>
            </div>
          </div>
        </div>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-12 col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-center align-items-center flex-column">
                  <div class="avatar avatar-2xl">
                    <?php if (!empty($user['profile_picture'])) : ?>
                      <img src="../storage/<?= $user['profile_picture'] ?>" alt="profile picture" class="img-fluid rounded-circle">
                    <?php else : ?>
                      <img src="../storage/default.jpg" alt="profile picture" class="img-fluid rounded-circle">
                    <?php endif; ?>
                  </div>

                  <h3 class="mt-3"><?= $user['name'] ?></h3>
                  <p class="text-small"><?= $user['role'] ?></p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-8">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Data Pribadi</h5>
              </div>
              <div class="card-body">
                <form action="#" method="get">
                  <div class="form-group">
                    <label class="form-label">Name</label>
                    <p><?= $user['name'] ?></p>
                  </div>
                  <div class="form-group">
                    <label class="form-label">ID Karyawan</label>
                    <p><?= $user['employee_id'] ?></p>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Email</label>
                    <p><?= $user['email'] ?></p>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Phone</label>
                    <p><?= $user['phone'] ?></p>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Unit</label>
                    <p><?= $user['unit'] ?></p>
                  </div>
                </form>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Edit Password</h5>
              </div>
              <div class="card-body">
                <form action="change_password.php" method="post">
                  <div class="form-group my-2">
                    <label for="current_password" class="form-label">Password Sekarang</label>
                    <input type="password" name="current_password" id="current_password"
                      class="form-control" placeholder="Masukkan password sekarang">
                  </div>
                  <div class="form-group my-2">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" class="form-control"
                      placeholder="Masukkan password baru">
                  </div>
                  <div class="form-group my-2">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" id="confirm_password"
                      class="form-control" placeholder="Masukkan konfirmasi password baru">
                  </div>

                  <div class="form-group my-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                  <?php
                  $error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
                  if (!empty($error)) {
                    echo "<p class='alert alert-danger mt-2'>$error</p>";
                  }
                  $success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
                  if (!empty($success)) {
                    echo "<p class='alert alert-success mt-2'>$success</p>";
                  }
                  ?>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>




  <?php include '../config/scripts_cdn.php' ?>

</body>

</html>