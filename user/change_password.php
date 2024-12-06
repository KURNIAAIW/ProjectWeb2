<?php

session_start();
include '../config/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    $error = "Password baru dan konfirmasi password tidak sama";
  } else {
    $employee_id = $_SESSION['employee_id'];
    $result = mysqli_query($conn, "SELECT * FROM User WHERE employee_id = '$employee_id'");
    $user = mysqli_fetch_assoc($result);
    if ($user) {
      if (password_verify($current_password, $user['password'])) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE User SET password = '$new_password_hash' WHERE employee_id = '$employee_id'");
        header("Location: profile.php?success=password_changed");
        $_SESSION['success'] = "Password berhasil diubah";
        $_SESSION['error'] = '';
        exit();
      } else {
        $error = "Password lama salah";
      }
    } else {
      $error = "User tidak ditemukan";
    }
  }
}

$_SESSION['error'] = $error;

header("Location: profile.php");
