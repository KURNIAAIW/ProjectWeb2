<?php
include '../config/db_connect.php';

$id = $_GET['id'];
mysqli_query($conn, "UPDATE transactions SET is_confirmed=2 WHERE transaction_id=$id");
header("Location: transaksi.php");
exit();
