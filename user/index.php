<?php
include "../auth/mw_user.php";
include '../config/db_connect.php';

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

$reports = mysqli_query($conn, "SELECT 
    p.name AS product_name, 
    SUM(t.quantity) AS total_quantity
FROM transactions t
JOIN products p ON t.product_id = p.id
GROUP BY p.name
ORDER BY total_quantity DESC;");

$labels = array();
$data = array();

while ($report = mysqli_fetch_assoc($reports)) {
  $labels[] = $report['product_name'];
  $data[] = $report['total_quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Page</title>
  <link rel="stylesheet" href="../styles/userDashboard.css">
  <?php include '../config/links_cdn.php' ?>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>
  <?php include '../config/scripts_cookie.php' ?>
  <?php include './header.php' ?>

  <!-- Menu Section -->
  <div style="max-width: 992px;margin: auto;margin-top:40px">
    <h3 style="text-align: center;">Daftar Menu</h3>
    <form action="" method="get" class="mb-5" style="max-width: 440px; margin: 12px auto;">
      <div class="input-group mb-3">
        <span class="input-group-text text-white" id="basic-addon1"><svg width="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
            <path d="M11.625 16.5a1.875 1.875 0 1 0 0-3.75 1.875 1.875 0 0 0 0 3.75Z" />
            <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875Zm6 16.5c.66 0 1.277-.19 1.797-.518l1.048 1.048a.75.75 0 0 0 1.06-1.06l-1.047-1.048A3.375 3.375 0 1 0 11.625 18Z" clip-rule="evenodd" />
            <path d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
          </svg>

        </span>
        <input type="text" class="form-control" placeholder="Masukkan nama kue" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
        <button class="btn btn-primary" type="submit">Cari</button>
        <?php if (!empty($keyword)): ?>
          <button class="btn btn-secondary" type="button" onclick="location.href='index.php'">Reset</button>
        <?php endif; ?>

      </div>
    </form>
    <main id="menu">
      <?php foreach ($products as $product): ?>
        <div class="menu-item">
          <img style="margin-top: -75px;" src="../storage/<?= $product["image"] ?>" alt="<?= $product["name"] ?>">
          <h3><?= $product["name"] ?></h3>
          <p><?= $product["category_name"] ?></p>
          <p>Rp <?= number_format($product["price"], 0, ',', '.') ?></p>
          <button class="btn-favorite btn-danger" style="background-color: red;" data-id="<?= $product["id"] ?>" data-item="<?= $product["name"] ?>" data-type="<?= $product["category_name"] ?>" data-price="Rp <?= $product["price"] ?>" data-image="../storage/<?= $product["image"] ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6" style="height: 16px;width: 16px;">
              <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
            </svg>
          </button>
          <button class="view-details" data-id="<?= $product["id"] ?>" data-item="<?= $product["name"] ?>" data-type="<?= $product["category_name"] ?>" data-price="Rp <?= $product["price"] ?>" data-image="../storage/<?= $product["image"] ?>">+</button>
        </div>
      <?php endforeach; ?>
    </main>

  </div>

  <?php include '../config/scripts_cdn.php' ?>
  <?php include '../config/scripts_cart.php' ?>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const favoriteButtons = document.querySelectorAll(".btn-favorite");

      favoriteButtons.forEach(button => {
        button.addEventListener("click", function() {
          const productId = this.dataset.id;

          // Retrieve existing favorites from cookies
          let favorites = getCookie("favorites");
          favorites = favorites ? JSON.parse(favorites) : [];

          // Check if the product is already in favorites
          if (favorites.includes(productId)) {
            alert("Product is already in your favorites!");
          } else {
            // Add new product ID to favorites
            favorites.push(productId);

            // Update the 'favorites' cookie
            setCookie("favorites", JSON.stringify(favorites), 7); // Cookie expires in 7 days
            alert("Product added to favorites!");
          }
        });
      });

      // Helper function to get a cookie by name
      function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(";").shift();
        return null;
      }

      // Helper function to set a cookie
      function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value}; ${expires}; path=/`;
      }
    });
  </script>

</body>

</html>