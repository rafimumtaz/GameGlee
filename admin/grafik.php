<?php
session_start();
require "../db.php";
$page = "grafik_penjualan";
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Total Produk
$total_products_query = mysqli_query($kon, "SELECT COUNT(id_produk) as total_produk FROM produk");
$total_products = mysqli_fetch_assoc($total_products_query)['total_produk'];

// Produk Terjual
$total_sold_query = mysqli_query($kon, "SELECT SUM(jumlah) as total_terjual FROM pesanan WHERE status_pesanan != 'Dibatalkan'");
$total_sold = mysqli_fetch_assoc($total_sold_query)['total_terjual'];

// Grafik Data (X: Semua Produk, Y: Total Penjualan per Produk)
$chart_data_query = mysqli_query($kon, "
    SELECT produk.nama_produk, SUM(pesanan.jumlah) as jumlah_terjual 
    FROM produk 
    LEFT JOIN pesanan ON produk.id_produk = pesanan.id_produk AND pesanan.status_pesanan != 'Dibatalkan'
    GROUP BY produk.id_produk
    ORDER BY jumlah_terjual DESC
");

$chart_labels = [];
$chart_values = [];
while ($row = mysqli_fetch_assoc($chart_data_query)) {
    $chart_labels[] = $row['nama_produk'];
    $chart_values[] = $row['jumlah_terjual'];
}

// Produk Paling Laris
$top_products_query = mysqli_query($kon, "
    SELECT produk.nama_produk, produk.harga, SUM(pesanan.jumlah) as jumlah_terjual
    FROM produk
    JOIN pesanan ON produk.id_produk = pesanan.id_produk
    WHERE pesanan.status_pesanan != 'Dibatalkan'
    GROUP BY produk.id_produk
    ORDER BY jumlah_terjual DESC
    LIMIT 5
");
$top_products = mysqli_fetch_all($top_products_query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Graphic Report</title>
  <link rel="stylesheet" href="/GameGlee/Gamify/assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="/GameGlee/Gamify/assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
   <!-- Sidebar -->
   <div class="sidebar">
    <header>
    <div class="top">
      <span class="image">
        <img src="/GameGlee/Gamify/assets/img/Logo_GG.png" alt="Logo Gameglee" class="logo">
      </span>
      <div><p>GAMEGLEE</p></div>
    </div>
    <ul>
      <li><a href="index.php" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="user.php"><span class="ph--user-list-bold"></span>DAFTAR USER</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php" class="active"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
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
        <div class="container">
            <div class="pagetitle">
                <h1><i class="bi bi-bar-chart"></i>&nbsp; Grafik Total Penjualan</h1>
               
            </div>

            <!-- Total Produk & Produk Terjual -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title">Total Produk</h6>
                            <h3 class="text-primary"><?= $total_products ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title">Produk Terjual</h6>
                            <h3 class="text-success"><?= $total_sold ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Grafik Penjualan</h6>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produk Paling Laris -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Produk Paling Laris</h6>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><p>Nama Produk</p></th>
                                        <th><p>Harga</p></th>
                                        <th><p>Jumlah Terjual</p></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($top_products)) : ?>
                                        <?php foreach ($top_products as $index => $product) : ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($product['nama_produk']) ?></td>
                                                <td>Rp <?= number_format($product['harga'], 0, ',', '.') ?></td>
                                                <td><?= $product['jumlah_terjual'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="4">Tidak ada data produk.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chart_labels) ?>,
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: <?= json_encode($chart_values) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

        <script src="/GameGlee/Gamify/assets/js/index.js"></script>
</body>
</html>