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

// Begin transaction for database integrity
$conn->begin_transaction();
try {
  $stmt = $conn->prepare("
    INSERT INTO transactions 
    (transaction_id, user_id, product_id, quantity, type, payment_proof, total_cost, user_cost, refund, payment_method, is_confirmed) 
    VALUES (?, ?, ?, ?, 'offline', ?, ?, ?, ?, ?, 1)
");

  foreach ($product_ids as $index => $product_id) {
    $quantity = $quantities[$index];
    $stmt->bind_param(
      "siiisddds",
      $transaction_id,
      $customer_name,
      $product_id,
      $quantity,
      $fileName,
      $total_cost,
      $user_cost,
      $refund,
      $payment_method
    );
    $stmt->execute();
  }

   // Commit the transaction
   $conn->commit();
   header("Location: ../admin/transaksi_offline.php?success=Transaction data has been saved successfully.");
   exit();
 } catch (Exception $e) {
   // Rollback transaction in case of an error
   $conn->rollback();
   header("Location: ../admin/transaksi_offline.php?error=Failed to save transaction: " . $e->getMessage());
   exit();
 } finally {
   $stmt->close();
 }
 