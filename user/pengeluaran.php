<?php
session_start();
require "../db.php";
$page = "history";

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

// Filter bulan dan tahun
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Ambil data user yang sedang login
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];

// Query untuk menampilkan data pengeluaran berdasarkan bulan dan tahun jika dipilih
$query = "SELECT 
            produk.nama_produk, 
            pesanan.tanggal_pesanan, 
            produk.harga, 
            pesanan_detail.jumlah AS kuantitas, 
            pesanan.total_harga
          FROM pesanan_detail
          JOIN pesanan ON pesanan_detail.id_pesanan = pesanan.id_pesanan
          JOIN produk ON pesanan_detail.id_produk = produk.id_produk
          WHERE pesanan.id_user = ?";
$params = [$user_id];

if (!empty($month)) {
    $query .= " AND MONTH(pesanan.tanggal_pesanan) = ?";
    $params[] = $month;
}
if (!empty($year)) {
    $query .= " AND YEAR(pesanan.tanggal_pesanan) = ?";
    $params[] = $year;
}

$query .= " ORDER BY pesanan.tanggal_pesanan DESC";
$stmt = $kon->prepare($query);
$stmt->bind_param(str_repeat("i", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

$totalPengeluaran = 0;

// Array nama bulan
$nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Outcome</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
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
      <li><a href="pesanan_diproses.php"><span class="hugeicons--package-process"></span>MENUNGGU DIKIRIM</a></li>
      <li><a href="pesanan_dikirim.php"><span class="carbon--delivery-parcel"></span>PESANAN DIKIRIM</a></li>
      <li><a href="pesanan_selesai.php"><span class="mdi--package-variant-closed-check"></span>PESANAN SELESAI</a></li>
      <li><a href="pesanan_dibatalkan.php"><span class="material-symbols--cancel-outline"></span>PESANAN DIBATALKAN</a></li>
      <li><a href="pengeluaran.php" class="active"><span class="tabler--report-money"></span>PENGELUARAN USER</a></li>
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
            <h1><i class="bi bi-clock-history"></i>&nbsp; Riwayat Pengeluaran</h1>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Filter Bulan dan Tahun -->
                            <form method="GET" class="mb-3">
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Bulan</label>
                                        <select name="month" class="form-control">
                                            <option value="">Semua Bulan</option>
                                            <?php 
                                            foreach ($nama_bulan as $index => $nama) {
                                                $monthVal = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                                $selected = ($monthVal == $month) ? 'selected' : '';
                                                echo "<option value='$monthVal' $selected>$nama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Tahun</label>
                                        <select name="year" class="form-control">
                                            <option value="">Semua Tahun</option>
                                            <?php for ($y = 2020; $y <= date('Y'); $y++) {
                                                $selected = ($y == $year) ? 'selected' : '';
                                                echo "<option value='$y' $selected>$y</option>";
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 align-self-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Tabel Data Pengeluaran -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Tanggal Pembelian</th>
                                        <th>Harga Satuan</th>
                                        <th>Kuantitas</th>
                                        <th>Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $no = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            $totalPengeluaran += $row['total_harga'];
                                            echo "<tr>
                                                    <td>{$no}</td>
                                                    <td>{$row['nama_produk']}</td>
                                                    <td>{$row['tanggal_pesanan']}</td>
                                                    <td>Rp " . number_format($row['harga'], 0, '', '.') . "</td>
                                                    <td>{$row['kuantitas']}</td>
                                                    <td>Rp " . number_format($row['total_harga'], 0, '', '.') . "</td>
                                                </tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>Tidak ada data pengeluaran</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Pengeluaran</th>
                                        <th>Rp <?= number_format($totalPengeluaran, 0, '', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="../assets/js/index.js"></script>
</body>
</html>