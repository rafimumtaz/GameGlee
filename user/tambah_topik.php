<?php
session_start();
include '../db.php';

// Cek apakah user sudah login
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

// Gunakan ID user dari session
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$id_user = $row_user["id_user"];

// Cek apakah ID komunitas dikirim di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Komunitas tidak ditemukan!";
    exit;
}
$id_komunitas = $_GET['id'];

// Ambil data komunitas berdasarkan ID
$query = mysqli_query($kon, "SELECT * FROM komunitas WHERE id_komunitas = '$id_komunitas'");
if (mysqli_num_rows($query) == 0) {
    echo "Komunitas tidak ditemukan!";
    exit;
}
$komunitas = mysqli_fetch_assoc($query);

// Tambah topik baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul_topik = mysqli_real_escape_string($kon, $_POST['judul_topik']);
    $deskripsi = mysqli_real_escape_string($kon, $_POST['deskripsi']);
    $id_user = $row_user['id_user'];

    if (!empty($judul_topik) && !empty($deskripsi)) {
        $query_insert = "INSERT INTO topik (judul_topik, deskripsi_topik, id_komunitas, dibuat_oleh) 
                         VALUES ('$judul_topik', '$deskripsi', '$id_komunitas', '$id_user' )";
        mysqli_query($kon, $query_insert);
        header("Location: detail_komunitas.php?id=$id_komunitas");
        exit;
    }
}

// Ambil daftar topik dari komunitas ini dengan nama pembuat
$query_topik = mysqli_query($kon, "
    SELECT t.*, u.nama AS pembuat 
    FROM topik t
    JOIN user u ON t.dibuat_oleh = u.id_user 
    WHERE t.id_komunitas = '$id_komunitas'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Dashboard</title>
  <link rel="stylesheet" href="../assets/css/styleDashboard.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
      <li><a href="index.php"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="most_favorite.php"><span class="ph--list-heart"></span>MOST FAVORITE ITEM</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>PROMO</a></li>
      <li><a href="forum_komunitas.php" class="active"><span class="gg--community"></span>KOMUNITAS</a></li>
      <li><a href="feedback.php"><span class="mdi--feedback-outline"></span>FEEDBACK</a></li>
      <li><a href="promo.php"><span class="tabler--discount"></span>CODE PROMO</a></li>
      <br><br><br><br>
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
            <li><a href="add_to_cart.php"><span class="solar--cart-outline"></span></a></li>
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

<!-- Modal -->
<div id="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background: #fff; margin: 10% auto; padding: 20px; width: 50%; position: relative;">
        <h2>Tambah Topik</h2>
        <form action="proses_tambah_topik.php" method="post">
            <label for="judul">Judul Topik:</label><br>
            <input type="text" id="judul" name="judul" required><br>
            <label for="deskripsi">Deskripsi:</label><br>
            <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea><br><br>
            <button type="submit">Tambah Topik</button>
            <button type="button" onclick="tutupModal()">Batal</button>
        </form>
    </div>
</div>

<script>
function bukaModal() {
    document.getElementById('modal').style.display = 'block';
}

function tutupModal() {
    document.getElementById('modal').style.display = 'none';
}
</script>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
        <script src="../assets/js/index.js"></script>
</body>
</html>