<?php
session_start();
require "../db.php";
$page = "dashboard";

if (!isset($_SESSION["admin"]))
{
  header("Location: ../login.php");
}

// MENGHITUNG SEMUA DATA
$sql_user = mysqli_query($kon, "SELECT * FROM user");
$row_user = mysqli_num_rows($sql_user);

$sql_pesanan = mysqli_query($kon, "SELECT * FROM pesanan");
$row_pesanan = mysqli_num_rows($sql_pesanan);

$sql_kategori = mysqli_query($kon, "SELECT * FROM kategori");
$row_kategori = mysqli_num_rows($sql_kategori);

$sql_produk = mysqli_query($kon, "SELECT * FROM produk");
$row_produk = mysqli_num_rows($sql_produk);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Dashboard</title>
  <link rel="stylesheet" href="../assets/css/styleDashboard.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
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
      <li><a href="index.php" class="active" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
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
        <div class="dashboard">
          <a href="order.php" class="card-1">
            <div class="icon">
            <span class="lsicon--work-order-abnormal-outline-list"></span>
            </div>
            <div class="content">
              <h3>Order Masuk</h3>
              <p><p><?= number_format($row_pesanan, 0, "", ".") ?></p></p>
            </div>
          </a>
          <a href="stok_produk.php" class="card-1">
            <div class="icon">
            <span class="lsicon--management-stockout-filled"></span>
            </div>
            <div class="content">
              <h3>Stok Produk</h3>
              <p><?= number_format($row_user, 0, "", ".") ?></p>
            </div>
          </a>
          <a href="kategori.php" class="card-1">
            <div class="icon">
            <span class="mdi--category-list">
            </div>
            <div class="content">
              <h3>Daftar Kategori</h3>
              <p><?= number_format($row_kategori, 0, "", ".") ?></p>
            </div>
          </a>
          <a href="produk.php" class="card-1">
            <div class="icon">
            <span class="ix--product"></span>
            </div>
            <div class="content">
              <h3>Daftar Produk</h3>
              <p><?= number_format($row_produk, 0, "", ".") ?></p>
            </div>
          </a>
        </div>
        <script src="../assets/js/index.js"></script>
</body>
</html>