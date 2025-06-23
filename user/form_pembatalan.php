<?php
session_start();
if (!isset($_GET['id_pesanan'])) {
    die("ID Pesanan tidak ditemukan.");
}
$id_pesanan = $_GET['id_pesanan'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Canceling Form</title>
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
    <ul>
      <li><a href="index.php"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="history_pembayaran.php"><span class="ic--twotone-history"></span>HISTORY PEMBELIAN</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <li><a href="wishlist.php"><span class="ph--list-star"></span>WISHLIST</a></li>
      <br><br><br><br><br><br><br><br><br><br>
      <li><a href="chat.php"><span class="tdesign--service"></span>CUSTOMER SERVICE</a></li>
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
            <h1><i class="bi bi-x-circle"></i> Pembatalan Pesanan</h1>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Ajukan Pembatalan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <form action="proses_pembatalan.php" method="POST">
                                <!-- ID Pesanan (Hidden) -->
                                <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($id_pesanan); ?>">

                                <!-- Alasan Pembatalan -->
                                <div class="mb-3">
                                    <label for="alasan" class="form-label">Alasan Pembatalan</label>
                                    <select name="alasan_pembatalan" id="alasan" class="form-select" required>
                                        <option value="">-- Pilih Alasan --</option>
                                        <option value="berubah_pikiran">Berubah Pikiran</option>
                                        <option value="harga_lebih_murah">Harga Lebih Murah</option>
                                        <option value="barang_salah">Barang Salah</option>
                                        <option value="pengiriman_lama">Pengiriman Lama</option>
                                        <option value="masalah_pembayaran">Masalah Pembayaran</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <!-- Deskripsi Pembatalan -->
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Pembatalan</label>
                                    <textarea name="deskripsi_pembatalan" id="deskripsi" class="form-control" rows="4" placeholder="Jelaskan alasan pembatalan..."></textarea>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-send"></i> Kirim Pembatalan
                                    </button>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
    <script src="../assets/js/index.js"></script>
</body>
</html>