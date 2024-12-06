<?php
include '../config/db_connect.php';

$id = $_POST['id'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];
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
  $getUser = mysqli_query($conn, "SELECT profile_picture FROM users WHERE id = $id");
  $user = mysqli_fetch_assoc($getUser);
  $profile_path = $user['profile_picture'];
}


if (!empty($password)) {
  $password = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, profile_picture = ?, phone = ? WHERE id = ?");
  $stmt->bind_param("sssssii", $name, $email, $password, $role, $profile_path, $_POST['phone'], $id);
} else {
  $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, profile_picture = ?, phone = ? WHERE id = ?");
  $stmt->bind_param("ssssii", $name, $email, $role, $profile_path, $_POST['phone'], $id);
}


if ($stmt->execute()) {
  header("Location: user.php");
  exit();
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
