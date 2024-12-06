<?php

include '../config/db_connect.php';


$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM UserComplain WHERE id = '$id'");
print_r($id);

header("Location: user_complain.php");
