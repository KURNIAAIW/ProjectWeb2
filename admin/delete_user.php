<?php
include '../config/db_connect.php';

$id = $_POST['id'];
mysqli_query($conn, "DELETE FROM users WHERE id = $id");
header("Location: user.php");
exit();
