<?php
include "../auth/mw_admin.php";
include '../config/db_connect.php';


$limit = 10; // jumlah data yang ingin ditampilkan per halaman

// ambil halaman saat ini dari parameter GET
$page = $_GET['page'] ?? 1;

// hitung offset dari halaman saat ini
$offset = ($page - 1) * $limit;

$keyword = $_GET['search'] ?? "";
$query = "SELECT 
            p.name AS product_name,
            pc.name AS category_name,
            t.transaction_id AS transaction_id,
            t.quantity AS quantity,
            t.created_at AS created_at,
            t.quantity * p.price AS revenue,
            t.is_confirmed AS is_confirmed,
            t.payment_proof AS payment_proof,
            t.payment_method AS payment_method,  -- New field
            t.total_cost AS total_cost,          -- New field
            t.user_cost AS user_cost,            -- New field
            t.refund AS refund                   -- New field
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        JOIN product_categories pc ON p.product_category_id = pc.id
        WHERE t.type = 'offline'
        ORDER BY t.created_at DESC";

if ($keyword !== "") {
  $query .= " WHERE products.name LIKE '%$keyword%'";
}

$query .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);

// hitung total data untuk menentukan jumlah halaman
$countQuery = "SELECT COUNT(*) FROM transactions WHERE type = 'offline'";

if ($keyword !== "") {
  $countQuery .= " WHERE name LIKE '%$keyword%' ";
}

$countResult = mysqli_query($conn, $countQuery);
$total = mysqli_fetch_row($countResult)[0];
$pages = ceil($total / $limit);

$reports = mysqli_query($conn, "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') AS month, 
    SUM(quantity * product_price) AS total_revenue
FROM (
    SELECT 
        t.quantity, 
        p.price AS product_price, 
        t.created_at
    FROM transactions t
    JOIN products p ON t.product_id = p.id WHERE t.type = 'offline'
) AS monthly_revenue
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC;");

$labels = array();
$data = array();

while ($report = mysqli_fetch_assoc($reports)) {
  $labels[] = $report['month'];
  $data[] = $report['total_revenue'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Transaksi</title>
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
              <h3>Daftar Transaksi Offline</h3>
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
          <div class="card-header">
            <h5 class="card-title">Daftar Transaksi Offline</h5>
          </div>
          <div class="card-body">
            <div class="row justify-content-between mb-4">
              <div class="col-md-5">
                <!-- button trigger for  Vertically Centered modal -->
                <a href="../admin/transaksi_offline_add.php" class="btn btn-outline-primary block">
                  Tambah Transaksi
                </a>
                <!-- Vertically Centered modal Modal -->
                <div class="modal fade" id="modalAddOfflineTransaction" tabindex="-1" role="dialog"
                  aria-labelledby="modalAddOfflineTransactionTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
                    role="document">
                    <form method="post" action="add_transaksi_offline.php" enctype="multipart/form-data">
                      <div style="width: 500px;" class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalAddOfflineTransactionTitle">Buat Transaksi Offline
                          </h5>
                          <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i data-feather="x"></i>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label for="customer_name" class="form-label">Nama Customer</label>
                            <input type="text" name="customer_name" class="form-control">
                          </div>
                          <div class="form-group">
                            <div class="form-label">Produk ID</div>
                            <input type="number" name="product_id" class="form-control">
                          </div>
                          <div class="form-group">
                            <label for="quantity" class="form-label">Jumlah Produk</label>
                            <input type="number" name="quantity" class="form-control">
                          </div>
                          <div class="mb-3">
                            <label for="payment_proof" class="form-label">Bukti Pembayaran</label>
                            <input class="form-control" name="payment_proof" type="file" id="payment_proof">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light-secondary"
                            data-bs-dismiss="modal">
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
                  </div>

                </div>
              </div>
            </div>

            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">ID Transaksi</th>
                  <th scope="col">
                    <div style="width: max-content;">Tanggal Transaksi</div>
                  </th>
                  <th scope="col">Produk</th>
                  <th scope="col">Kategori</th>
                  <th scope="col">Jumlah</th>
                  <th scope="col">Pendapatan</th>
                  <th scope="col">Metode Pembayaran</th> <!-- New Column -->
                  <th scope="col">Total Harga</th> <!-- New Column -->
                  <th scope="col">Jumlah Bayar</th> <!-- New Column -->
                  <th scope="col">Kembalian</th> <!-- New Column -->
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                <?php foreach ($result as $product) : ?>
                  <tr>
                    <td><?= ($i + ($limit * ($page - 1))) ?></td>
                    <td><?= $product['transaction_id'] ?></td>
                    <td>
                      <?php
                      $date = new DateTime($product['created_at']);
                      echo $date->format('d-M-Y, H:i');
                      ?>
                    </td>
                    <td><?= $product['product_name'] ?></td>
                    <td><?= $product['category_name'] ?></td>
                    <td><?= $product['quantity'] ?></td>
                    <td>Rp <?= number_format($product['revenue'], 0, ',', '.') ?></td>
                    <td><?= $product['payment_method'] ?></td> <!-- New Data -->
                    <td>Rp <?= number_format($product['total_cost'], 0, ',', '.') ?></td> <!-- New Data -->
                    <td>Rp <?= number_format($product['user_cost'], 0, ',', '.') ?></td> <!-- New Data -->
                    <td>Rp <?= number_format($product['refund'], 0, ',', '.') ?></td> <!-- New Data -->
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

  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    const errorMessage = urlParams.get('error');

    if (successMessage) {
      alert(successMessage);
    }

    if (errorMessage) {
      alert(errorMessage);
    }

    var optionsReport = {
      annotations: {
        position: "back",
      },
      dataLabels: {
        enabled: true, // Show data labels for the line chart
      },
      chart: {
        type: "line", // Changed to line chart
        height: 300,
        toolbar: {
          show: false, // Hide toolbar for a cleaner UI
        },
      },
      stroke: {
        curve: "smooth", // Makes the line smoother
        width: 2, // Thickness of the line
      },
      series: [{
        name: "Monthly Report",
        data: <?php echo json_encode($data); ?>,
      }, ],
      colors: ["#eab308"], // Use an array for the color
      xaxis: {
        categories: <?php echo json_encode($labels); ?>, // Label for months
        title: {
          text: "Bulan", // X-axis title
        },
      },
      yaxis: {
        title: {
          text: "Pendapatan", // Y-axis title
        },
      },
      tooltip: {
        theme: "light", // Tooltip theme
        x: {
          format: "MM-yyyy", // Format X-axis dates in tooltip
        },
        y: {
          formatter: function(value) {
            return "Rp " + value.toLocaleString(); // Format revenue with currency
          },
        },
      },
    };

    var chartReports = new ApexCharts(
      document.querySelector("#chart-report"),
      optionsReport
    );
    chartReports.render();
  </script>

</body>

</html>