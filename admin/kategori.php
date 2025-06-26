<?php
session_start();
include('../db.php'); 
$page = "kategori";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Inisialisasi kategori dari database jika session categories kosong
if (empty($_SESSION['categories'])) {
    $_SESSION['categories'] = [];

    // Query untuk mengambil data kategori dari database
    $sql = "SELECT id_kategori, nama_kategori FROM kategori";
    $result = mysqli_query($kon, $sql);

    // Simpan kategori ke dalam session
    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['categories'][$row['id_kategori']] = $row['nama_kategori'];
    }
}

// Proses penambahan kategori
if (isset($_POST['add_category'])) {
    $newCategory = $_POST['category_name'];
    if (!empty($newCategory)) {
        // Tambahkan kategori baru ke database
        $sql = "INSERT INTO kategori (nama_kategori, deskripsi, created_at) VALUES ('$newCategory', '', NOW())";
        if (mysqli_query($kon, $sql)) {
            // Ambil ID kategori yang baru ditambahkan
            $newId = mysqli_insert_id($kon);
            // Tambahkan kategori baru ke session
            $_SESSION['categories'][$newId] = $newCategory;
        }
    }
}

// Proses penghapusan kategori
if (isset($_GET['delete_category'])) {
    $categoryId = $_GET['delete_category'];

    // Hapus kategori dari database
    $sql = "DELETE FROM kategori WHERE id_kategori = $categoryId";
    if (mysqli_query($kon, $sql)) {
        // Hapus kategori dari session
        unset($_SESSION['categories'][$categoryId]);
    }
}

// Menghitung jumlah produk per kategori
$categoryCounts = [];
if (isset($_SESSION['products'])) {
    foreach ($_SESSION['categories'] as $id => $category) {
        $count = 0;
        foreach ($_SESSION['products'] as $product) {
            if ($product['category'] == $category) {
                $count++;
            }
        }
        $categoryCounts[$id] = $count;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Category</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
   <!-- Sidebar -->
   <div class="sidebar">
    <header>
    <div class="top">
      <span class="image">
        <img src="../assets/img/Logo_GG.png" alt="Logo Gameglee" class="logo">
      </span>
      <div><p>GAMEGLEE</p></div>
    </div>
    <ul>
      <li><a href="index.php" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php" ><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <li><a href="promo.php"><span class="tabler--discount"></span>CODE PROMO</a></li>
      <li><a href="forum_komunitas.php"><span class="gg--community"></span>KOMUNITAS</a></li>
      <li><a href="logout.php"><span class="tabler--logout"></span>LOGOUT</a></li>
    </ul>
    </header>
   </div>
  

  <!-- Main Content -->
        <div class="topbar">
            <div class="search-box">
              <div class="dynamic-text">
                <input type="text"/>
                <span class="animated-text"></span>
              </div>
              <button class="close-button">X</button>
              <span class="iconamoon--search"></span>
            </div>
            <div class="menu-nav">
              <ul>
                <li><a href="kategori.php" class="active"><span class="mdi--category-plus"></span></a></li>
                <li><a href="feedback.php"><span class="mdi--feedbacks-outline"></span></a></li>
                <li><a href="index_admin.php"><span class="tdesign--cutomerservice"></span></a></li>
                <li><a href="notifikasi.php"><span class="ic--outline-notifications"></span></a></li>
                <li>
                  <a href="profil.php">
                  <span class="gg--profile"></span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="user-profile">
              <ul>
                <li>
                  <a href="profil.php"  data-bs-toggle="dropdown">
                  <span class="d-none d-md-block dropdown-toggle ps-2">
                    <i style="font-size: 20px" class="bi bi-person">
                    </i>&nbsp; Halo, <?= $_SESSION["admin"] ?>!
                  </span>
                  </a>
                </li>
              </ul>
            </div>
        </div>
        <main id="main" class="main-promo">
        <div class="pagetitle">
            <h1><i class="bi bi-grid"></i>&nbsp; KATEGORI</h1>

        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Daftar Kategori</h6>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                Tambah Kategori
                            </button>

                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th><p>Nama Kategori</p></th>
                                        <th><p>Jumlah Produk</p></th>
                                        <th><p>Aksi</p></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['categories'] as $id => $category): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($category) ?></td>
                                            <td><?= $categoryCounts[$id] ?? 0 ?></td>
                                            <td>
                                                <a href="kategori.php?delete_category=<?= $id ?>" class="btn btn-danger btn-sm">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section </main>
    </div>

    <!-- Modal for Adding Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addCategoryModalLabel">Tambah Kategori</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="kategori.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">Nama Kategori:</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_category" class="btn btn-primary">Tambah Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/index.js"></script>
</body>
</html>