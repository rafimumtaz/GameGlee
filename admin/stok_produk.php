<?php
session_start();
include('../db.php'); 
$page = "statistik";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Statistik produk
$sqlStats = "
    SELECT 
        COUNT(id_produk) AS total_produk, 
        SUM(CASE WHEN stok = 0 THEN 1 ELSE 0 END) AS unavailable_produk,
        (SELECT SUM(pd.jumlah) FROM pesanan_detail pd JOIN pesanan p ON pd.id_pesanan = p.id_pesanan WHERE p.status_pesanan != 'Dibatalkan') AS total_terjual
    FROM produk
";
$resultStats = mysqli_query($kon, $sqlStats);
if (!$resultStats) {
    die("Query gagal: " . mysqli_error($kon));
}
$stats = mysqli_fetch_assoc($resultStats);

// Data stok produk
$sqlProducts = "
    SELECT 
        p.id_produk, 
        p.nama_produk, 
        p.stok, 
        (SELECT SUM(pd.jumlah) FROM pesanan_detail pd JOIN pesanan p ON pd.id_pesanan = p.id_pesanan WHERE p.status_pesanan != 'Dibatalkan' AND pd.id_produk = p.id_produk) AS total_terjual, 
        (SELECT AVG(rating) FROM review_produk WHERE id_produk = p.id_produk) AS rata_rating 
    FROM produk p
";
$resultProducts = mysqli_query($kon, $sqlProducts);
if (!$resultProducts) {
    die("Query gagal: " . mysqli_error($kon));
}
$products = mysqli_fetch_all($resultProducts, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Stock</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
        body {
            background-color: #f5f5f5;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .text-success {
            font-weight: bold;
        }

        .text-danger {
            font-weight: bold;
        }
    </style>
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
      <li><a href="stok_produk.php" class="active"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
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
                <li><a href="kategori.php"><span class="mdi--category-plus"></span></a></li>
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
        <!-- Product Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light text-black">
                        <h4>Product Statistics</h4>
                        <a href="produk.php" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Go to Produk
                    </a>
                    </div>
                    <br>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6>Total Produk</h6>
                                <p class="h3"><?= $stats['total_produk'] ?></p>
                            </div>
                            <div class="col-md-4">
                                <h6>Unavailable Produk</h6>
                                <p class="h3 text-danger"><?= $stats['unavailable_produk'] ?></p>
                            </div>
                            <div class="col-md-4">
                                <h6>Total Terjual</h6>
                                <p class="h3"><?= $stats['total_terjual'] ?? 0 ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light text-black">
                        <h4>Stok Produk</h4>
                    </div>
                    <br>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Performance</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['nama_produk']) ?></td>
                                        <td>
                                            <strong>Total Terjual:</strong> <?= $product['total_terjual'] ?? 0 ?> <br>
                                            <strong>Rata-rata Rating:</strong> <?= number_format($product['rata_rating'] ?? 0, 1) ?>/5
                                        </td>
                                        <td><?= $product['stok'] ?></td>
                                        <td>
                                            <?php if ($product['stok'] > 0): ?>
                                                <span class="text-success">Available</span>
                                            <?php else: ?>
                                                <span class="text-danger">Unavailable</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($products)): ?>
                            <div class="alert alert-warning text-center">
                                Tidak ada produk ditemukan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
        <script src="../assets/js/index.js"></script>
</body>
</html>