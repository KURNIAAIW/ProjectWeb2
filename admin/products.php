<?php
include "../auth/mw_admin.php";
include '../config/db_connect.php';

$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$failed = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$limit = 10; // jumlah data yang ingin ditampilkan per halaman

unset($_SESSION['success_message']);

// ambil halaman saat ini dari parameter GET
$page = $_GET['page'] ?? 1;

// hitung offset dari halaman saat ini
$offset = ($page - 1) * $limit;

$keyword = $_GET['search'] ?? "";
$query = "SELECT products.*, product_categories.name AS category FROM products 
          LEFT JOIN product_categories ON products.product_category_id = product_categories.id";

if ($keyword !== "") {
  $query .= " WHERE products.name LIKE '%$keyword%'";
}

$query .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);

// hitung total data untuk menentukan jumlah halaman
$countQuery = "SELECT COUNT(*) FROM products";

if ($keyword !== "") {
  $countQuery .= " WHERE name LIKE '%$keyword%' ";
}

$countResult = mysqli_query($conn, $countQuery);
$total = mysqli_fetch_row($countResult)[0];
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Produk</title>
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
              <h3>Daftar Produk</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="">
        <?php
        if (!empty($success)) {
          echo "<p class='alert-success alert mt-2'>$success</p>";
        }
        ?>

        <?php
        if (!empty($failed)) {
          echo "<p class='alert-error alert mt-2'>$failed</p>";
        }
        ?>
      </div>

      <!-- section here -->
      <section class="section">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Daftar Produk</h5>
          </div>
          <div class="card-body">
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Tambah Produk</button>

            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="add_product.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="productName" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="productName" name="name" required>
                      </div>
                      <div class="mb-3">
                        <label for="productCategory" class="form-label">Kategori Produk</label>
                        <select class="form-select" id="productCategory" name="product_category_id" required>
                          <?php
                          $categories = mysqli_query($conn, "SELECT * FROM product_categories");
                          while ($category = mysqli_fetch_assoc($categories)) {
                            echo "<option value='{$category['id']}'>{$category['name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="productStock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="productStock" name="stock" required>
                      </div>
                      <div class="mb-3">
                        <label for="productPrice" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="productPrice" name="price" required>
                      </div>
                      <div class="mb-3">
                        <label for="productDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="productImage" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="productImage" name="image" accept="image/*">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                      <button type="submit" class="btn btn-primary">Tambah Produk</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="row justify-content-between">
              <div class="col-md-5">
                <form action="" method="get" class="mb-3">
                  <div class="input-group mb-3">
                    <span class="input-group-text text-white" id="basic-addon1"><svg width="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path d="M11.625 16.5a1.875 1.875 0 1 0 0-3.75 1.875 1.875 0 0 0 0 3.75Z" />
                        <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875Zm6 16.5c.66 0 1.277-.19 1.797-.518l1.048 1.048a.75.75 0 0 0 1.06-1.06l-1.047-1.048A3.375 3.375 0 1 0 11.625 18Z" clip-rule="evenodd" />
                        <path d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
                      </svg>

                    </span>
                    <input type="text" class="form-control" placeholder="Masukkan nama kue" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    <button class="btn btn-primary" type="submit">Cari</button>
                  </div>
                </form>
              </div>
            </div>

            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Kategori</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                <?php foreach ($result as $product) : ?>
                  <tr>
                    <th scope="row"><?= ($i + ($limit * ($page - 1))) ?></th>
                    <td><?= $product['name'] ?></td>
                    <td><?= $product['category'] ?></td>
                    <td>
                      <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-primary"><svg width="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                          <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                          <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                        </svg>
                      </a>
                      <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                          <path fill="#fff" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z" />
                        </svg>
                      </a>

                      <form action="delete_product.php" method="get" class="d-inline">
                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn btn-danger"><svg width="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                          </svg>
                        </button>
                      </form>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="text-center d-flex justify-content-center">
              <nav>
                <?php
                $total_products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
                $total_pages = ceil($total_products / $limit);
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                ?>
                <ul class="pagination pagination-primary">
                  <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&limit=<?php echo $limit; ?>">Prev</a>
                  </li>
                  <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                      <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a>
                    </li>
                  <?php endfor; ?>
                  <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&limit=<?php echo $limit; ?>">Next</a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>




  <?php include '../config/scripts_cdn.php' ?>


</body>

</html>