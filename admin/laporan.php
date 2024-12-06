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
            t.created_at AS created_at,
            t.quantity AS quantity,
            t.quantity * p.price AS revenue,
            t.is_confirmed AS is_confirmed,
            t.type AS type,
            t.payment_proof AS payment_proof
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        -- JOIN users u ON t.user_id = u.id
        JOIN product_categories pc ON p.product_category_id = pc.id
        ORDER BY t.created_at DESC";

if ($keyword !== "") {
  $query .= " WHERE products.name LIKE '%$keyword%'";
}

$query .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);

// hitung total data untuk menentukan jumlah halaman
$countQuery = "SELECT COUNT(*) FROM transactions";

if ($keyword !== "") {
  $countQuery .= " WHERE name LIKE '%$keyword%' ";
}

$countResult = mysqli_query($conn, $countQuery);
$total = mysqli_fetch_row($countResult)[0];
$pages = ceil($total / $limit);

/* $reports = mysqli_query($conn, "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') AS month, 
    SUM(quantity * product_price) AS total_revenue
FROM (
    SELECT 
        t.quantity, 
        p.price AS product_price, 
        t.type,
        t.created_at
    FROM transactions t
    JOIN products p ON t.product_id = p.id
) AS monthly_revenue
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC;"); */

$reports = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month, 
        SUM(CASE WHEN type = 'offline' THEN quantity * product_price ELSE 0 END) AS total_revenue_offline,
        SUM(CASE WHEN type = 'online' THEN quantity * product_price ELSE 0 END) AS total_revenue_online
    FROM (
        SELECT 
            t.quantity, 
            p.price AS product_price, 
            t.type,
            t.created_at
        FROM transactions t
        JOIN products p ON t.product_id = p.id
    ) AS monthly_revenue
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC;
");



$labels = array();
$data = array();
$data_offline = array();

while ($report = mysqli_fetch_assoc($reports)) {
  $labels[] = $report['month'];
  $data[] = $report['total_revenue_online'];
  $data_offline[] = $report['total_revenue_offline'];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pengaduan</title>
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
              <h3>Daftar Transaksi</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- section here -->
      <section class="section">
        <div class="row">
          <div class="col-12">
            <!-- <div class="card">
              <div class="card-header">
                <h4>Laporan Penjualan</h4>
              </div>
              <div class="card-body">
                <div id="chart-report"></div>
              </div>
            </div> -->
            <div class="card">
              <div class="card-header">
                <h4>Line Area Chart</h4>
              </div>
              <div class="card-body">
                <div id="area"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Daftar Transaksi</h5>
          </div>
          <div class="card-body">
            <div class="row justify-content-between">
              <div class="col-md-5">

              </div>
              <div class="col-md-5">
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
                  <th scope="col">Tipe Transaksi</th>
                  <th scope="col">Jumlah</th>
                  <th scope="col">Pendapatan</th>
                  <!-- <th scope="col">Bukti Pembayaran</th>
                  <th scope="col">Persetujuan</th> -->
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
                    <td><?= $product['type'] ?></td>
                    <td><?= $product['quantity'] ?></td>
                    <td>Rp <?= $product['revenue'] ?></td>
                    <!-- <td><img width="200" src="../storage/payment_proofs/<?= $product['payment_proof'] ?>" alt=""></td> -->
                    <!-- <td>
                      <?php if ($product['is_confirmed'] == 0): ?>
                        <a href="approve.php?id=<?= $product['transaction_id'] ?>" class="btn btn-primary">
                        <svg fill="#00ff00" height="188px" width="188px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve" stroke="#00ff00" stroke-width="3.05"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M40.267,14.628L20.974,33.921l-9.293-9.293c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414l10,10 c0.195,0.195,0.451,0.293,0.707,0.293s0.512-0.098,0.707-0.293l20-20c0.391-0.391,0.391-1.023,0-1.414S40.657,14.237,40.267,14.628z "></path> </g></svg>
                        </a>
                        <a href="decline.php?id=<?= $product['transaction_id'] ?>" class="btn btn-primary">
                        <svg height="200px" width="200px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve" fill="#ff0000" stroke="#ff0000" stroke-width="3.1"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="#231F20" d="M9.016,40.837c0.195,0.195,0.451,0.292,0.707,0.292c0.256,0,0.512-0.098,0.708-0.293l14.292-14.309 l14.292,14.309c0.195,0.196,0.451,0.293,0.708,0.293c0.256,0,0.512-0.098,0.707-0.292c0.391-0.39,0.391-1.023,0.001-1.414 L26.153,25.129L40.43,10.836c0.39-0.391,0.39-1.024-0.001-1.414c-0.392-0.391-1.024-0.391-1.414,0.001L24.722,23.732L10.43,9.423 c-0.391-0.391-1.024-0.391-1.414-0.001c-0.391,0.39-0.391,1.023-0.001,1.414l14.276,14.293L9.015,39.423 C8.625,39.813,8.625,40.447,9.016,40.837z"></path> </g></svg>
                        </a>
                      <?php endif; ?>
                      <?php if ($product['is_confirmed'] == 1): ?>
                        Disetujui
                      <?php endif; ?>
                      <?php if ($product['is_confirmed'] == 2): ?>
                        Ditolak
                      <?php endif; ?>
                    </td> -->
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

  <script>
    var areaOptions = {
      series: [{
          name: "online",
          data: <?php echo json_encode($data); ?>,

        },
        {
          name: "offline",
          data: <?php echo json_encode($data_offline); ?>,
        },
      ],
      chart: {
        height: 350,
        type: "area",
      },
      colors: ["#eab308", "#e74c3c"],
      dataLabels: {
        enabled: false,
      },
      stroke: {
        curve: "smooth",
      },
      xaxis: {
        title: {
          text: "Bulan", // X-axis title
        },
        categories: <?php echo json_encode($labels); ?>,
      },
      tooltip: {
        x: {
          format: "dd/MM/yy HH:mm",
        },
      },
    }
    var area = new ApexCharts(document.querySelector("#area"), areaOptions)
    area.render()
  </script>

</body>

</html>