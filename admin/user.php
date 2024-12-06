<?php

include "../auth/mw_admin.php";
include '../config/db_connect.php';


$limit = 10; // jumlah data yang ingin ditampilkan per halaman

// ambil halaman saat ini dari parameter GET
$page = $_GET['page'] ?? 1;

// hitung offset dari halaman saat ini
$offset = ($page - 1) * $limit;

$users = mysqli_query($conn, "SELECT * FROM users LIMIT $limit OFFSET $offset");

// hitung total data untuk menentukan jumlah halaman
$total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$total_pages = ceil($total_users / $limit);

if (isset($_GET['edit'])) {
  $id = $_GET['edit'];

  $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
  $user = mysqli_fetch_assoc($result);
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
              <h3>Kelola User</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- section here -->
      <section class="section">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> User</h3>
          </div>

          <div class="card-body">
            <?php if (isset($_GET['edit'])) { ?>
              <form action="edit_user.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">Nama asdf</label>
                      <input type="text" class="form-control" id="name" name="name" value="<?= $user['name'] ?>">
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input type="text" class="form-control" id="email" name="email" value="<?= $user['email'] ?>">
                    </div>
                    <div class="mb-3">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" class="form-control" id="password" name="password">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="phone" class="form-label">Phone</label>
                      <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                      <label for="profile_picture" class="form-label">Profile Picture</label>
                      <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>
                    <div class="mb-3">
                      <label for="role" class="form-label">Level</label>
                      <select class="form-select" id="role" name="role">
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                      </select>
                    </div>
                  </div>
                </div>
                <a href="user.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            <?php } else { ?>

              <form action="add_user.php" method="post" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">Nama</label>
                      <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input type="text" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" class="form-control" id="password" name="password">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="phone" class="form-label">Phone</label>
                      <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                      <label for="profile_picture" class="form-label">Profile Picture</label>
                      <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>
                    <div class="mb-3">
                      <label for="role" class="form-label">Level</label>
                      <select class="form-select" id="role" name="role">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                      </select>
                    </div>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            <?php } ?>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Daftar Pengaduan User</h5>
          </div>
          <div class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Email</th>
                  <th scope="col">Level</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; ?>
                <?php foreach ($users as $user) : ?>
                  <tr>
                    <th scope="row"><?= ($i + ($limit * ($page - 1))) ?></th>
                    <td><?= $user['name'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td scope="row"><span class="badge bg-<?= $user['role'] == 'admin' ? 'primary' : 'secondary' ?>"><?= $user['role'] ?></span></td>
                    <td>
                      <form action="delete_user.php" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <button type="submit" class="btn btn-danger"><svg width="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                          </svg>
                        </button>
                      </form>
                      <form action="user.php" method="get" class="d-inline">
                        <input type="hidden" name="edit" value="<?= $user['id'] ?>">
                        <button type="submit" class="btn btn-success"><svg width="24px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32L19.513 8.2Z" />
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
              <nav aria-label="Page navigation example">
                <?php
                $total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
                $total_pages = ceil($total_users / $limit);
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