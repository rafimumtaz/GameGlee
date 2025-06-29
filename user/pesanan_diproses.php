<?php
// Koneksi ke database
require_once '../db.php';
session_start();
$page = "pesanan_diproses";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Query untuk mengambil data pesanan dengan status 'Dibatalkan'
$query = "SELECT pd.*, p.*, pr.nama_produk, pr.harga, pr.gambar 
          FROM pesanan_detail pd
          JOIN produk pr ON pd.id_produk = pr.id_produk
          JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
          WHERE p.status_pesanan = 'Diproses'";

$result = $kon->query($query);
$pesanan = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Pesanan</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f5f5;
        }
        h4{
            color: black !important;
            font-family: "slackey", sans-serif !important;
        }
        h5{
          color: black !important;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            min-height: 150px;
        }
        .card-img-top {
            height: 150px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }
        .pesanan-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .pesanan-card img {
            max-width: 100px;
            border-radius: 8px;
        }
        .btn-orange {
            background-color: orange;
            color: white;
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
    <ul class="sidebar" style="color:#fff;">
      <li><a href="index.php"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="history_pembayaran.php"><span class="ic--twotone-history"></span>HISTORY PEMBELIAN</a></li>
      <li><a href="pesanan_diproses.php" class="active"><span class="hugeicons--package-process"></span>MENUNGGU DIKIRIM</a></li>
      <li><a href="pesanan_dikirim.php"><span class="carbon--delivery-parcel"></span>PESANAN DIKIRIM</a></li>
      <li><a href="pesanan_selesai.php"><span class="mdi--package-variant-closed-check"></span>PESANAN SELESAI</a></li>
      <li><a href="pesanan_dibatalkan.php"><span class="material-symbols--cancel-outline"></span>PESANAN DIBATALKAN</a></li>
      <li><a href="pengeluaran.php"><span class="tabler--report-money"></span>PENGELUARAN USER</a></li>
      <br><br><br><br>
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
          <li><a href="notifikasi.php"><span class="ic--outline-notifications"></span></a></li>
          <li><a href="add_to_cart.php" class="active"><span class="solar--cart-outline"></span></a></li>
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
              <i class="bi bi-person"></i>Halo, <?= $_SESSION["user"] ?>!
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <main id="main" class="main-promo">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Pesanan Diproses</h4>
        </div>
        <?php if (count($pesanan) > 0): ?>
            <?php foreach ($pesanan as $row): ?>
                <div class="pesanan-card d-flex align-items-center">
                    <div>
                        <img src="../uploads/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_produk']; ?>">
                    </div>
                    <div class="ms-3">
                        <h5><?php echo $row['nama_produk']; ?></h5>
                        <p class="mb-1">Jumlah: <?php echo $row['jumlah']; ?></p>
                        <p><?php echo date('d-m-Y', strtotime($row['tanggal_pesanan'])); ?></p>
                    </div>
                    <div class="ms-auto text-end">
                        <p>IDR <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        <p>Total: IDR <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></p>
                        <a href="checkout.php?id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-orange">Beli Lagi</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada pesanan yang diproses.</p>
        <?php endif; ?>
    </div>
</main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/chart.js/chart.umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>

    <script src="../assets/js/index.js"></script>
</body>
</html>