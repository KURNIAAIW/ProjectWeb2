<?php
include "../auth/mw_admin.php";
include '../config/db_connect.php';

$productId = $_GET['id'];
$productQuery = "SELECT name FROM products WHERE id = $productId";
$result = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($result);
$productName = $product['name'];
$query = "DELETE FROM products WHERE id = $productId";
if (mysqli_query($conn, $query)) {
  $_SESSION['success_message'] = "Produk $productName berhasil dihapus.";
  header("Location: products.php");
  exit;
} else {
  echo "Error: " . $query . "<br>" . mysqli_error($conn);
}
