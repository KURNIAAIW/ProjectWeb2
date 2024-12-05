<?php
include '../config/db_connect.php';

$reports = mysqli_query($conn, "SELECT * FROM report WHERE year = YEAR(CURDATE()) ORDER BY year ASC, month ASC");

$labels = array();
$data = array();

while ($report = mysqli_fetch_assoc($reports)) {
  $date = new DateTime($report['year'] . '-' . $report['month']);
  $labels[] = $date->format('F Y');
  $data[] = $report['total_sales'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola User</title>
  <?php include '../config/links_cdn.php' ?>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>


  <div id="app" style="background-color: #fffbeb;">
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
              <h3>Dashboard</h3>
            </div>
          </div>
        </div>
        <section class="section">
          <div class="row">
            <div class="col-6 col-lg-4 col-md-6">
              <div class="card">
                <div class="card-body px-4 py-4-5">
                  <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                      <div class="stats-icon purple mb-2">
                        <span class="fas fa-solid fa-paperclip text-white"></span>
                      </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                      <h6 class="text-muted font-semibold">Admin</h6>
                      <?php
                      $query = "SELECT COUNT(*) FROM users WHERE role = 'admin'";
                      $result = mysqli_query($conn, $query);
                      $total = mysqli_fetch_row($result)[0];
                      ?>
                      <h6 class="font-extrabold mb-0"><?php echo $total; ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-6 col-lg-4 col-md-6">
              <div class="card">
                <div class="card-body px-4 py-4-5">
                  <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                      <div class="stats-icon mb-2" style="background-color: #FE77E1">
                        <span class="fas fa-solid fa-check text-white"></span>
                        <i class="fa-solid fa-list-check"></i>
                      </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                      <h6 class="text-muted font-semibold">User</h6>
                      <?php
                      $query = "SELECT COUNT(*) FROM users WHERE role = 'user'";
                      $result = mysqli_query($conn, $query);
                      $total = mysqli_fetch_row($result)[0];
                      ?>
                      <h6 class="font-extrabold mb-0"><?php echo $total; ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-6 col-lg-4 col-md-6">
              <div class="card">
                <div class="card-body px-4 py-4-5">
                  <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                      <div class="stats-icon mb-2" style="background-color: #FED87D">
                        <span class="fas fa-solid fa-spinner text-white"></span>
                      </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                      <h6 class="text-muted font-semibold">Total Penjualan</h6>
                      <?php
                      $query = "SELECT SUM(quantity) FROM transactions";
                      $result = mysqli_query($conn, $query);
                      $total = mysqli_fetch_row($result)[0];
                      ?>
                      <h6 class="font-extrabold mb-0"><?= $total ?> kue</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4>Power BI Report</h4>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <tr>
                      <td>
                        <iframe 
                          title="ProjectwebKel1Inter" 
                          style="width: 100%; height: calc(100vh - 200px);"
                          src="https://app.powerbi.com/view?r=eyJrIjoiNTMxZTRhZDQtZmQxZC00NGFmLWI2MTYtMjUwYTlhNjMxMGY4IiwidCI6IjUyNjNjYzgxLTU5MTItNDJjNC1hYmMxLWQwZjFiNjY4YjUzMCIsImMiOjEwfQ%3D%3D" 
                          frameborder="0" 
                          allowFullScreen="true">
                        </iframe>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
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
          </div>
        </section>
      </div>


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
        enabled: false,
      },
      chart: {
        type: "bar",
        height: 300,
      },
      fill: {
        opacity: 1,
      },
      plotOptions: {},
      series: [{
        name: "Monthly Report",
        data: <?php echo json_encode($data); ?>,
      }, ],
      colors: "#eab308",
      xaxis: {
        categories: <?php echo json_encode($labels); ?>,
      }

    };

    var chartReports = new ApexCharts(
      document.querySelector("#chart-report"),
      optionsReport
    )
    chartReports.render()
  </script>
</body>

</html>