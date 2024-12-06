<?php
include '../config/db_connect.php';
include "../auth/mw_admin.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $product_category_id = $_POST['product_category_id'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $uploadDir = '../storage/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        // Check if upload is successful
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Insert product data into the database
            $query = "INSERT INTO products (product_category_id, name, stock, price, description, image)
                      VALUES ('$product_category_id', '$name', '$stock', '$price', '$description', '$fileName')";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success_message'] = "Produk berhasil ditambahkan!";
                // Redirect back to products page
                header("Location: ../admin/products.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan produk! Error: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['error_message'] = "Gagal mengupload gambar produk!";
        }
    } else {
        $_SESSION['error_message'] = "Tidak ada gambar yang diupload!";
    }

    // Redirect back to products page
    header("Location: ../admin/products.php");
    exit;
}
