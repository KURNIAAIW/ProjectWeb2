<?php
include "../auth/mw_user.php";
include '../config/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  die("You must be logged in to view this page.");
}

$user_id = $_SESSION['user_id'];

// Handle fetching transactions
$transactions = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $sql = "
  SELECT 
      t.transaction_id, 
      GROUP_CONCAT(CONCAT('- ',p.name, ' (', t.quantity, ')') SEPARATOR '<br>') AS products,
      SUM(t.quantity * p.price) AS total_price,
      t.payment_proof,            
      t.created_at,
      t.is_confirmed
  FROM transactions t
  JOIN products p ON t.product_id = p.id
  WHERE t.user_id = ?
  GROUP BY t.transaction_id, t.payment_proof, t.created_at, t.is_confirmed
  ORDER BY t.created_at DESC
";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Transaksi</title>
  <link rel="stylesheet" href="../styles/userDashboard.css">
  <?php include '../config/links_cdn.php' ?>

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
    }

    .container {
      max-width: 800px;
      margin: auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f5f5f5;
    }

    .payment-proof {
      max-width: 100px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>
  <?php include '../config/scripts_cookie.php' ?>

  <?php include './header.php' ?>


  <div style="margin: auto;margin-top:40px">
    <div class="px-5 pb-5">
      <h1>Riwayat Transaksi</h1>
      <table style="background-color: white;">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Produk</th>
            <th>Total</th>
            <th>Bukti Pembayaran</th>
            <th>Tanggal</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($transactions)): ?>
            <tr>
              <td colspan="5">No transactions found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($transactions as $transaction): ?>
              <tr>
                <td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
                <td>
                  <div style="width: max-content;">
                    <?= $transaction['products'] ?>
                  </div>
                </td>
                <td>
                  <p style="width: max-content;">Rp <?= number_format($transaction['total_price'], 0, ',', '.') ?></p>
                </td>
                <td>
                  <img
                    src="../storage/payment_proofs/<?= htmlspecialchars($transaction['payment_proof']) ?>"
                    alt="Payment Proof"
                    class="payment-proof"
                    onclick="viewPaymentProof('<?= htmlspecialchars($transaction['payment_proof']) ?>')" />
                </td>
                <td>
                  <div style="width: max-content;"><?= date("d-m-Y H:i:s", strtotime($transaction['created_at'])) ?></div>
                </td>
                <td>                
                  <?=
                  $transaction['is_confirmed'] === 0
                    ? '<span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>'
                    : ($transaction['is_confirmed'] === 1
                      ? '<span class="badge bg-success">Dikonfirmasi</span>'
                      : '<span class="badge bg-danger">Ditolak</span>')
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <script>
      function viewPaymentProof(fileName) {
        window.open(`../storage/payment_proofs/${fileName}`, '_blank');
      }
    </script>
  </div>

  <?php include '../config/scripts_cdn.php' ?>

  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>

  <?php include '../config/scripts_cart.php' ?>
</body>

</html>