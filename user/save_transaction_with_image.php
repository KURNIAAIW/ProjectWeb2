<?php
include '../config/db_connect.php';
session_start();

// Set response headers
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $address = $_POST['address'] ?? null; // Get address

    if (!$address) {
        echo json_encode(['success' => false, 'message' => 'Alamat pengiriman diperlukan.']);
        exit;
    }

    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Invalid file upload.']);
        exit;
    }

    $transaction_id = $_POST['transaction_id'] ?? null;
    $items = json_decode($_POST['items'], true);

    if (!$transaction_id || !$items || !is_array($items)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        exit;
    }

    $uploadDir = '../storage/payment_proofs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['payment_proof']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        foreach ($items as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];

            $stmt = $conn->prepare(
                "INSERT INTO transactions (transaction_id, user_id, product_id, quantity, payment_proof, address) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iiisss", $transaction_id, $user_id, $product_id, $quantity, $fileName, $address);

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert transaction for product_id: $product_id");
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Transaction saved successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
