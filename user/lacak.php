<?php
session_start();
include('../db.php'); // Koneksi ke database

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user'];

// Ambil ID pesanan dari parameter URL
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validasi ID pesanan
if ($orderId <= 0) {
    die("ID pesanan tidak valid.");
}

// Ambil data pengiriman dari database
$sql = "SELECT pg.nomor_resi, pg.nama_kurir, pg.alamat_pengiriman, 
        pg.tanggal_kirim, pg.perkiraan_tiba, pg.tanggal_tiba, 
        pg.status_pengiriman, pg.biaya_kirim
        FROM pengiriman_pesanan pg
        JOIN pesanan p ON pg.id_pesanan = p.id_pesanan
        WHERE pg.id_pesanan = ? AND pg.id_user = ?";
$stmt = mysqli_prepare($kon, $sql);

if (!$stmt) {
    error_log("Prepare statement gagal: " . mysqli_error($kon));
    die("Terjadi kesalahan sistem.");
}

mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$shippingData = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Pastikan $shippingData tidak null
if (!$shippingData) {
    die("Data pengiriman tidak ditemukan.");
}

// Tentukan progress berdasarkan status pengiriman
$statusMap = [
    'dalam_pengiriman' => 2,
    'sudah_sampai' => 3,
    'terlambat' => 3,
];
$currentStep = isset($statusMap[$shippingData['status_pengiriman']]) ? $statusMap[$shippingData['status_pengiriman']] : 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Lacak</title>
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
        /* Timeline Styling */
        .timeline {
            position: relative;
            margin: 20px 0;
            padding: 0;
            list-style: none;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20px;
            bottom: 0;
            width: 4px;
            background: #ddd;
        }

        .timeline-step {
            position: relative;
            margin: 0 0 20px 50px;
            padding: 0 0 0 20px;
        }

        .timeline-step.active .timeline-icon {
            background: #007bff;
            color: #fff;
        }

        .timeline-step .timeline-icon {
            position: absolute;
            top: 0;
            left: -30px;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            background: #ddd;
            color: #fff;
        }

        .timeline-step .timeline-content {
            font-size: 14px;
            color: #555;
        }

        .timeline-step .timeline-content strong {
            font-size: 16px;
            color: #333;
        }

        h2 {
            font-size: 32px; /* Ukuran font untuk h2 yang lebih besar */
            margin-bottom: 20px; /* Jarak bawah untuk h2 */
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
      <li><a href="history_pembayaran.php" class="active"><span class="ic--twotone-history"></span>HISTORY PEMBELIAN</a></li>
      <li><a href="pesanan_diproses.php"><span class="hugeicons--package-process"></span>MENUNGGU DIKIRIM</a></li>
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
        <div class="pagetitle">
            <h1><i class="bi bi-truck"></i> Lacak Pesanan</h1>
        
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Timeline Pengiriman</h2>
                            <ul class="timeline">
                                <li class="timeline-step <?= $currentStep >= 1 ? 'active' : ''; ?>">
                                    <div class="timeline-icon"><i class="bi bi-box"></i></div>
                                    <div class="timeline-content">
                                        <strong>Dipesan</strong>
                                        <p><?= isset($shippingData['tanggal_kirim']) ? date('d/m/Y H:i', strtotime($shippingData['tanggal_kirim'])) : 'Tidak tersedia'; ?></p>
                                    </div>
                                </li>
                                <li class="timeline-step <?= $currentStep >= 2 ? 'active' : ''; ?>">
                                    <div class="timeline-icon"><i class="bi bi-truck"></i></div>
                                    <div class="timeline-content">
                                        <strong>Dalam Pengiriman</strong>
                                        <p><?= $currentStep >= 2 ? 'Sedang dalam perjalanan' : 'Belum dalam pengiriman'; ?></p>
                                    </div>
                                </li>
                                <li class="timeline-step <?= $currentStep >= 3 ? 'active' : ''; ?>">
                                    <div class="timeline-icon"><i class="bi bi-house-door"></i></div>
                                    <div class="timeline-content">
                                        <strong><?= isset($shippingData['status_pengiriman']) && $shippingData['status_pengiriman'] === 'terlambat' ? 'Terlambat' : 'Tiba di Tujuan'; ?></strong>
                                        <p><?= isset($shippingData['tanggal_tiba']) ? ($shippingData['tanggal_tiba'] ? date('d/m/Y H:i', strtotime($shippingData['tanggal_tiba'])) : 'Belum tiba') : 'Tidak tersedia'; ?></p>
                                    </div>
                                </li>
                            </ul>
                            <h2 class="card-title mt-4">Detail Pengiriman</h2>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nomor Resi</th>
                                    <td><?= isset($shippingData['nomor_resi']) ? htmlspecialchars($shippingData['nomor_resi']) : 'Tidak tersedia'; ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Kurir</th>
                                    <td><?= isset($shippingData['nama_kurir']) ? htmlspecialchars($shippingData['nama_kurir']) : 'Tidak tersedia'; ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat Pengiriman</th>
                                    <td><?= isset($shippingData['alamat_pengiriman']) ? htmlspecialchars($shippingData['alamat_pengiriman']) : 'Tidak tersedia'; ?></td>
                                </tr>
                                <tr>
                                    <th>Estimasi Waktu Tiba</th>
                                    <td><?= isset($shippingData['perkiraan_tiba']) ? ($shippingData['perkiraan_tiba'] ? date('d/m/Y H:i', strtotime($shippingData['perkiraan_tiba'])) : 'Tidak tersedia') : 'Tidak tersedia'; ?></td>
                                </tr>
                                <tr>
                                    <th>Biaya Kirim</th>
                                    <td><?= isset($shippingData['biaya_kirim']) ? 'Rp ' . number_format($shippingData['biaya_kirim'], 0, ',', '.') : 'Tidak tersedia'; ?></td>
                                </tr>
                            </table>
                            <a href="history_pembayaran.php" class="btn btn-secondary mt-3">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.min.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <script src="../assets/js/index.js"></script>
</body>
</html>