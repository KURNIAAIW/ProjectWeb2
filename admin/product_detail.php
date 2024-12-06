<?php
include "../auth/mw_admin.php";
include '../config/db_connect.php';

$productId = $_GET['id'];

$productQuery = "SELECT products.*, product_categories.name AS category_name FROM products
                  LEFT JOIN product_categories 
                  ON products.product_category_id = product_categories.id
                  WHERE products.id = $productId";
$result = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk</title>
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

      <div class="page-heading">
        <div class="page-title">
          <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
              <h3>Detail Produk</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- section here -->
      <section class="section">
        <div class="row">
          <div class="col-12 col-lg-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-center align-items-center flex-column">
                  <div class="mx-4 mb-2">
                    <?php if (!empty($product['image'])) { ?>
                      <img height="200" src="../storage/<?= $product["image"] ?>" alt="product picture">
                    <?php } else {  ?>
                      <img src="https://placehold.co/600x400" alt="product picture" style="width: 100%; height: auto;">
                    <?php } ?>
                  </div>

                  <form action="delete_product.php" method="get" class="d-inline">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-danger">
                      Hapus
                    </button>
                  </form>


                </div>
              </div>
            </div>
          </div>
          <div class=" col-12 col-lg-8">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Data Produk</h5>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label class="form-label">Nama Produk</label>
                  <p><?= $product['name'] ?></p>
                </div>
                <div class="form-group">
                  <label class="form-label">Kategori</label>
                  <p><?= $product['category_name'] ?></p>
                </div>
                <div class="form-group">
                  <label class="form-label">Harga</label>
                  <p><?= $product['price'] ?></p>
                </div>
                <div class="form-group">
                  <label class="form-label">Deskripsi</label>
                  <p><?= $product['description'] ?></p>
                </div>
                <div class="form-group">
                  <label class="form-label">Dibuat pada</label>
                  <p><?= $product['created_at'] ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <?php include '../config/scripts_cdn.php' ?>
</body>

</html>