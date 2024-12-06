<?php
include '../config/db_connect.php';
include "../auth/mw_admin.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $product_category_id = $_POST['product_category_id'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $image = $_FILES['image']['name'];
    if ($image) {
        $targetDir = '../storage/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        $query = "UPDATE products SET 
                  product_category_id = '$product_category_id',
                  name = '$name',
                  stock = '$stock',
                  price = '$price',
                  description = '$description',
                  image = '$fileName'
                  WHERE id = $id";
    } else {
        $query = "UPDATE products SET 
                  product_category_id = '$product_category_id',
                  name = '$name',
                  stock = '$stock',
                  price = '$price',
                  description = '$description'
                  WHERE id = $id";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Produk berhasil diubah!";
        header("Location: ../admin/products.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Produk</title>
    <?php include '../config/links_cdn.php' ?>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>

    <div id="app">
        <?php include '../components/admin/sidebar.php' ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <!-- Back Button -->
            <div class="mb-3">
                <a href="products.php" class="">Kembali</a>
            </div>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Edit Produk</h3>
                        </div>
                    </div>
                </div>
            </div>

            <form action="" method="post" enctype="multipart/form-data" class="p-3">
                <!-- Product Name -->
                <div class="mb-3">
                    <label for="productName" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="productName" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <!-- Product Category -->
                <div class="mb-3">
                    <label for="productCategory" class="form-label">Kategori Produk</label>
                    <select class="form-select" id="productCategory" name="product_category_id" required>
                        <?php
                        $categories = mysqli_query($conn, "SELECT * FROM product_categories");
                        while ($category = mysqli_fetch_assoc($categories)) {
                            $selected = $category['id'] == $product['product_category_id'] ? 'selected' : '';
                            echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label for="productStock" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="productStock" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label for="productPrice" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="productPrice" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="productDescription" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="productDescription" name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="productImage" class="form-label">Gambar Produk</label>
                    <input class="form-control" type="file" id="productImage" name="image">
                </div>

                <!-- Submit Button -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</body>

</html>