<?php
include "../auth/mw_admin.php";
include '../config/db_connect.php';

// PRODUCT
$keyword = $_GET['search'] ?? "";
$productsQuery = "SELECT 
    products.*, 
    product_categories.name AS category_name
FROM 
    products
LEFT JOIN 
    product_categories
ON 
    products.product_category_id = product_categories.id";

if ($keyword !== "") {
  $productsQuery .= " WHERE products.name LIKE '%" . mysqli_real_escape_string($conn, $keyword) . "%'";
}

$products = mysqli_query($conn, $productsQuery);
// END PRODUCT
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Transaksi</title>
  <link rel="stylesheet" href="../styles/userDashboard.css">
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
              <h3>Tambah Transaksi Offline</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- section here -->
      <section class="section">
        <!-- <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4>Laporan Penjualan</h4>
              </div>
              <div class="card-body">
                <div id="chart-report"></div>
              </div>
            </div>
          </div>
        </div> -->

        <div class="card">
          <div class="card-body">
            <main id="menu">
              <?php foreach ($products as $product): ?>
                <div class="menu-item">
                  <img style="margin-top: -75px;" src="../storage/<?= $product["image"] ?>" alt="<?= $product["name"] ?>" />
                  <h3><?= $product["name"] ?></h3>
                  <p><?= $product["category_name"] ?></p>
                  <p>Rp <?= number_format($product["price"], 0, ',', '.') ?></p>

                  <div class="quantity-control">
                    <button class="btn-decrement" onclick="updateQuantity(<?= $product['id'] ?>, -1, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>)">-</button>
                    <span id="quantity-<?= $product['id'] ?>">0</span>
                    <button class="btn-increment" onclick="updateQuantity(<?= $product['id'] ?>, 1, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>)">+</button>
                  </div>
                </div>
              <?php endforeach; ?>
            </main>
          </div>
        </div>

        <div class="card">
          <div class="card-body row">
            <form method="post" class="col-5" id="transaction-form" action="add_transaksi_offline.php" enctype="multipart/form-data">
              <div class="modal-content">
                <div class="modal-body text-left text-start">
                  <div class="form-group">
                    <label for="customer_name" class="form-label">Nama Customer</label>
                    <input type="text" name="customer_name" class="form-control" id="customer_name">
                  </div>
                  <!-- <div class="mb-3">
                    <label for="payment_proof" class="form-label">Bukti Pembayaran</label>
                    <input class="form-control" name="payment_proof" type="file" id="payment_proof">
                  </div> -->
                  <div class="form-group">
                    <label for="total_cost" class="form-label">Jumlah Total</label>
                    <input type="text" readonly name="total_cost" class="form-control" id="total_cost" value="0">
                  </div>
                  <div class="form-group">
                    <label for="user_cost" class="form-label">Jumlah Bayar</label>
                    <input type="number" name="user_cost" class="form-control" id="user_cost">
                  </div>
                  <div class="form-group">
                    <label for="refund" class="form-label">Kembalian</label>
                    <input type="text" readonly name="refund" class="form-control" id="refund" value="0">
                  </div>
                  <div class="form-group">
                    <label for="payment_method" class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" class="form-control" id="payment_method">
                      <option value="Cash">Cash</option>
                      <option value="Transfer">Transfer</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Batal</span>
                  </button>
                  <button type="submit" class="btn btn-primary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Simpan</span>
                  </button>
                </div>
              </div>
            </form>

            <div class="col-7 p-4">
              <div class="" style="font-size: 32px;font-weight: bold;">Items</div>
              <div class="added-list"></div>
            </div>
          </div>
        </div>

      </section>
    </div>
  </div>




  <?php include '../config/scripts_cdn.php' ?>

  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>
  <script>
    const addedProducts = [];
    const quantities = {};


    function updateQuantity(productId, change, productName, productPrice) {
      if (!quantities[productId]) {
        quantities[productId] = 0;
      }

      // Update the quantity and ensure it's not negative
      quantities[productId] = Math.max(0, quantities[productId] + change);
      document.getElementById(`quantity-${productId}`).innerText = quantities[productId];

      const existingProductIndex = addedProducts.findIndex((product) => product.id === productId);

      if (quantities[productId] > 0) {
        if (existingProductIndex !== -1) {
          addedProducts[existingProductIndex].quantity = quantities[productId];
        } else {
          addedProducts.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: quantities[productId],
          });
        }
      } else if (existingProductIndex !== -1) {
        addedProducts.splice(existingProductIndex, 1);
      }

      renderAddedList();
      updateFormInputs(); // Update hidden inputs
    }

    function renderAddedList() {
      const addedListDiv = document.querySelector(".added-list");
      addedListDiv.innerHTML = ""; // Clear existing content

      let totalCost = 0;

      addedProducts.forEach((product) => {
        totalCost += product.price * product.quantity;

        const productRow = document.createElement("div");
        productRow.classList.add("product-row");
        productRow.innerHTML = `
      <p>${product.name} - Rp ${new Intl.NumberFormat("id-ID").format(product.price)} x ${product.quantity}</p>
    `;
        addedListDiv.appendChild(productRow);
      });

      // Update total_cost input field
      document.getElementById("total_cost").value = totalCost;
    }

    // Calculate refund when user_cost is input
    document.getElementById("user_cost").addEventListener("input", function() {
      const totalCost = parseInt(document.getElementById("total_cost").value) || 0;
      const userCost = parseInt(this.value) || 0;
      const refund = totalCost - userCost;

      document.getElementById("refund").value = refund * -1;
    });


    function updateFormInputs() {
      const form = document.getElementById("transaction-form");

      // Remove existing hidden inputs for product_id and quantity
      form.querySelectorAll("input[name='product_id[]'], input[name='quantity[]']").forEach((input) => input.remove());

      // Add new hidden inputs for each product in the list
      addedProducts.forEach((product) => {
        const productIdInput = document.createElement("input");
        productIdInput.type = "hidden";
        productIdInput.name = "product_id[]";
        productIdInput.value = product.id;

        const quantityInput = document.createElement("input");
        quantityInput.type = "hidden";
        quantityInput.name = "quantity[]";
        quantityInput.value = product.quantity;

        form.appendChild(productIdInput);
        form.appendChild(quantityInput);
      });
    }
  </script>
</body>

</html>