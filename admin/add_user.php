<?php
include '../config/db_connect.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];
$phone = $_POST['phone'];

$profile_picture = $_FILES['profile_picture'];
$profile_path = '';
if (!empty($profile_picture['name'])) {
  $fileName = $profile_picture['name'];
  $fileTmpName = $profile_picture['tmp_name'];
  $fileSize = $profile_picture['size'];
  $fileError = $profile_picture['error'];
  $fileType = $profile_picture['type'];

  $fileExt = explode('.', $fileName);
  $fileActualExt = strtolower(end($fileExt));

  $allowed = array('jpg', 'jpeg', 'png');


  if (in_array($fileActualExt, $allowed)) {
    if ($fileError === 0) {
      if ($fileSize < 1000000) {
        $fileNameNew = uniqid('', true) . "." . $fileActualExt;
        $fileDestination = '../storage/' . $fileNameNew;
        $profile_path = $fileNameNew;
        move_uploaded_file($fileTmpName, $fileDestination);
      } else {
        echo "File size is too large";
      }
    } else {
      echo "There was an error uploading your file";
    }
  } else {
    echo "You cannot upload files of this type";
  }
} else {
  echo "No file selected";
  header("Location: user.php");
}


$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $email, $password, $role, $phone, $profile_path);

if ($stmt->execute()) {
  header("Location: user.php");
  exit();
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
