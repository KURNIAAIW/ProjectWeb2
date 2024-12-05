<?php
include "../auth/mw_user.php";
include '../config/db_connect.php';


$favoriteProducts = [];
if (isset($_COOKIE['favorites'])) {
  $favoriteIds = json_decode($_COOKIE['favorites'], true);
  foreach ($favoriteIds as $id) {
    $result = mysqli_query($conn, "SELECT 
            products.*, 
            product_categories.name AS category_name
        FROM 
            products
        LEFT JOIN 
            product_categories
        ON 
            products.product_category_id = product_categories.id
        WHERE products.id = $id
        ");
    $favoriteProducts[] = mysqli_fetch_assoc($result);
  }
}

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

$monthlyReports = mysqli_query($conn, "
    SELECT 
        p.name AS product_name,
        DATE_FORMAT(t.created_at, '%Y-%m') AS sale_month,
        SUM(t.quantity) AS total_quantity
    FROM transactions t
    JOIN products p ON t.product_id = p.id
    GROUP BY p.name, sale_month
    ORDER BY sale_month ASC, p.name ASC
");

$groupedData = [];
while ($report = mysqli_fetch_assoc($monthlyReports)) {
  $groupedData[$report['sale_month']][$report['product_name']] = $report['total_quantity'];
}

$months = array_keys($groupedData);
$productNames = [];
foreach ($groupedData as $monthData) {
  $productNames = array_merge($productNames, array_keys($monthData));
}
$productNames = array_unique($productNames);

$datasets = [];
foreach ($productNames as $productName) {
  $dataPoints = [];
  foreach ($months as $month) {
    $dataPoints[] = $groupedData[$month][$productName] ?? 0;
  }
  $datasets[] = [
    'name' => $productName,
    'data' => $dataPoints,
  ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favorite Page</title>
  <link rel="stylesheet" href="../styles/userDashboard.css">
  <?php include '../config/links_cdn.php' ?>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>
  <?php include '../config/scripts_cookie.php' ?>

  <?php include './header.php' ?>


  <div style="max-width: 992px;margin: auto;margin-top:40px">
    <h3 style="text-align: center;" class="mb-4">Favorite</h3>
    <div style="padding: 2rem;" class="card">
      <div id="chart-report"></div>
    </div>

    <main id="menu">
      <?php foreach ($favoriteProducts as $product): ?>
        <div class="menu-item">
          <img style="margin-top: -75px;" src="../storage/<?= $product["image"] ?>" alt="<?= $product["name"] ?>">
          <h3><?= $product["name"] ?></h3>
          <p><?= $product["category_name"] ?></p>
          <p>Rp <?= number_format($product["price"], 0, ',', '.') ?></p>
          <button class="btn-favorite btn btn-danger" style="background-color: red;" data-id="<?= $product["id"] ?>" data-item="<?= $product["name"] ?>" data-type="<?= $product["category_name"] ?>" data-price="Rp <?= $product["price"] ?>" data-image="../storage/<?= $product["image"] ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6" style="height: 16px;width: 16px;">
              <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
            </svg>
          </button>
          <button class="view-details" data-id="<?= $product["id"] ?>" data-item="<?= $product["name"] ?>" data-type="<?= $product["category_name"] ?>" data-price="Rp <?= $product["price"] ?>" data-image="../storage/<?= $product["image"] ?>">+</button>
        </div>
      <?php endforeach; ?>
    </main>


  </div>



  <?php include '../config/scripts_cdn.php' ?>

  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>

  <script>
    var optionsStackedBar = {
      chart: {
        type: "bar", // Set chart type to bar
        height: 400,
        stacked: true, // Enable stacking
      },
      series: <?php echo json_encode($datasets); ?>, // Use PHP-generated datasets
      xaxis: {
        categories: <?php echo json_encode($months); ?>, // Months as categories
        title: {
          text: "Bulan",
        },
        labels: {
          formatter: function(value) {
            // Format date to "November 2024" style
            const date = new Date(value); // Assumes value is in 'YYYY-MM' format
            const options = {
              year: 'numeric',
              month: 'long'
            };
            return date.toLocaleDateString('en-US', options); // Converts to "Month Year" format
          },
        },
      },
      yaxis: {
        title: {
          text: "Jumlah Terjual",
        },
      },
      dataLabels: {
        enabled: false, // Hide data labels for cleaner visualization
      },
      tooltip: {
        theme: "light",
        y: {
          formatter: function(value) {
            return value + " items"; // Tooltip format
          },
        },
      },
      legend: {
        position: "top", // Position legend above chart
        horizontalAlign: "center",
      },
      colors: [
        '#FF5733', // Vibrant Orange
        '#33FF57', // Bright Green
        '#3357FF', // Strong Blue
        '#FF33A1', // Pink
        '#FFC733', // Warm Yellow
        '#33FFF5', // Aqua
        '#FF3333', // Red
        '#B833FF', // Purple
        '#33B8FF', // Light Blue
        '#33FF83', // Lime Green
        '#FF8333', // Orange
        '#8C33FF', // Violet
        '#FF5733', // Coral
        '#5733FF', // Indigo
        '#FFD633', // Goldenrod
        '#33D1FF', // Sky Blue
        '#FF337D', // Magenta
        '#7DFF33', // Chartreuse
        '#FFB833', // Amber
        '#33FFB8', // Mint
      ],
      plotOptions: {
        bar: {
          horizontal: false, // Set bars to be vertical
        },
      },
      grid: {
        padding: {
          top: 10,
          right: 20,
          bottom: 10,
          left: 20,
        },
      },
    };

    var stackedBarChart = new ApexCharts(
      document.querySelector("#chart-report"),
      optionsStackedBar
    );
    stackedBarChart.render();
  </script>

  <!-- <script>
    var optionsReport = {
      annotations: {
        position: "back",
      },
      dataLabels: {
        enabled: true, // Show data labels for the bar chart
      },
      chart: {
        type: "bar", // Changed to bar chart
        height: 300,
        toolbar: {
          show: false, // Hide toolbar for a cleaner UI
        },
      },
      colors: ["#eab308"], // Use an array for the color
      series: [{
        name: "Monthly Report",
        data: <?php echo json_encode($data); ?>,
      }, ],
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
  </script> -->



  <?php include '../config/scripts_cart.php' ?>
  <script>
    const favoriteDeleteButtons = document.querySelectorAll(".btn-favorite");
    favoriteDeleteButtons.forEach(button => {
      button.addEventListener("click", (event) => {
        const productId = button.getAttribute("data-id");
        const favoriteArray = getCookie("favorites") || [];

        const updatedFavorites = favoriteArray.filter(id => id !== productId);
        setCookie("favorites", updatedFavorites, 7);
        location.reload();
      })
    })
  </script>


</body>

</html>