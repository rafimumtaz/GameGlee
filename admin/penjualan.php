<?php
session_start();
$page = "penjualan";
if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

$host = 'localhost';
$dbname = 'gamify';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Filter data berdasarkan periode
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$conditions = "p.status_pesanan != 'Dibatalkan'";
$date_filter = '';

if ($filter === 'harian') {
    $date_filter = "AND DATE(p.tanggal_pesanan) = CURDATE()";
} elseif ($filter === 'bulanan') {
    $date_filter = "AND MONTH(p.tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(p.tanggal_pesanan) = YEAR(CURDATE())";
} elseif ($filter === 'tahunan') {
    $date_filter = "AND YEAR(p.tanggal_pesanan) = YEAR(CURDATE())";
}
$conditions .= " $date_filter";

// Hitung total pendapatan
$total_query = "SELECT SUM(p.total_harga) AS total_pendapatan 
                FROM pesanan_detail pd
                JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
                WHERE $conditions";
$total_stmt = $pdo->query($total_query);
$total_pendapatan = $total_stmt->fetch(PDO::FETCH_ASSOC)['total_pendapatan'] ?? 0;

// Query data pesanan
$query = "SELECT pr.nama_produk, p.tanggal_pesanan, pd.jumlah, pr.harga, p.total_harga 
          FROM pesanan_detail pd
          JOIN produk pr ON pd.id_produk = pr.id_produk
          JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
          WHERE $conditions
          ORDER BY p.tanggal_pesanan DESC";
$stmt = $pdo->query($query);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Report Revenue</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
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
      <li><a href="penjualan.php" class="active"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
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
        <div class="row mb-4">
            <div class="col">
                <h1><i class="bi bi-graph-up"></i>&nbsp; Data Penjualan</h1>
            </div>
        </div>

        <!-- Total Pendapatan dan Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Total Pendapatan</h6>
                        <h3 class="text-success">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <form method="GET" class="card">
                    <div class="card-body">
                        <h6 class="card-title">Filter Data</h6>
                        <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Semua</option>
                            <option value="harian" <?= $filter == 'harian' ? 'selected' : '' ?>>Harian</option>
                            <option value="bulanan" <?= $filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="tahunan" <?= $filter == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data Penjualan -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Daftar Penjualan</h6>
                <a href="grafik.php" class="btn btn-primary">Grafik Penjualan</a>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><p>Nama Produk</p></th>
                            <th><p>Tanggal</p></th>
                            <th><p>Jumlah</p></th>
                            <th><p>Harga Satuan</p></th>
                            <th><p>Total</p></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pesanan)) : ?>
                            <?php foreach ($pesanan as $index => $order) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($order['nama_produk']) ?></td>
                                    <td><?= $order['tanggal_pesanan'] ?></td>
                                    <td><?= $order['jumlah'] ?></td>
                                    <td>Rp <?= number_format($order['harga'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6">Tidak ada data penjualan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


        <script src="../assets/js/index.js"></script>
</body>
</html>