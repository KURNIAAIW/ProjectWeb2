<?php
include "../auth/mw_user.php";
include '../config/db_connect.php';

$phone = "08123123123";
$email = 'test@mail.com';
$address = 'lorem ipsum dolor sit amet consectetur adipisicing elit. Natus, eum! lore ipsum dolor sit amet consectetur adipisicing elit. Natus, eum!';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favorite Page</title>
  <link rel="stylesheet" href="../styles/userDashboard.css">
  <?php include '../config/links_cdn.php' ?>

  <style>
    svg {
      width: 48px;
      display: block;
      margin-bottom: 12px;
    }

    .flex {
      display: flex;
      flex-direction: row;
      gap: 24px;
    }

    .flex>div {
      flex: 1;
    }
  </style>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>

  <?php include './header.php' ?>


  <div style="max-width: 668px;margin: auto;margin-top:40px">
    <h3 style="text-align: center;" class="mb-4">Contact Us</h3>
    <div class="flex">
      <div class="card">
        <div class="card-body">
          <div class="form-group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
              <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" />
            </svg>
            <label class="form-label">No. HP</label>
            <p>
              <a href="tel:<?= $phone ?>"><?= $phone ?></a>
            </p>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="form-group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
              <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
              <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
            </svg>
            <label class="form-label">Email</label>
            <p><a href="mailto:<?= $email ?>"><?= $email ?></a></p>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
            <path fill-rule="evenodd" d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
          </svg>
          <label class="form-label">Alamat</label>
          <p><?= $address ?></p>
        </div>
      </div>
    </div>
    <?php include '../config/scripts_cdn.php' ?>

</body>

</html>