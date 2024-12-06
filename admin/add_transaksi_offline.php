<?php
include "../auth/mw_admin.php";
include '../config/db_connect.php';

$transaction_id = date('YmdHis');
$customer_name = $_POST['customer_name'];
$product_ids = $_POST['product_id']; // Expecting an array of product IDs
$quantities = $_POST['quantity'];    // Expecting an array of quantities
$total_cost = $_POST['total_cost'];
$user_cost = $_POST['user_cost'];
$refund = $_POST['refund'];
$payment_method = $_POST['payment_method'];
$uploadDir = '../storage/payment_proofs/';

// Check if the arrays are valid and of the same length
if (count($product_ids) !== count($quantities)) {
  header("Location: /admin/transaksi_offline.php?error=Invalid product data.");
  exit();
}