<?php
session_start();
include '../db.php';
$page = "komunitas";

// Cek apakah user adalah admin
if (!isset($_SESSION["admin"])){
    header("Location: ../login.php");
    exit;
}

// Gunakan nama admin dari session
$user = $_SESSION["admin"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$nama_admin = $_SESSION["admin"];

// Ambil data recent komunitas
$query_recent = mysqli_query($kon, "SELECT * FROM komunitas ORDER BY created_at DESC LIMIT 5");

// Ambil data top komunitas (komunitas dengan lebih dari 10 balasan)
$query_top = mysqli_query($kon, "
    SELECT k.*, COUNT(t.id_topik) AS jumlah_topik, 
           (SELECT COUNT(*) 
            FROM topik tp 
            JOIN komentar km ON tp.id_topik = km.id_topik 
            WHERE tp.id_komunitas = k.id_komunitas) AS jumlah_komentar
    FROM komunitas k
    LEFT JOIN topik t ON k.id_komunitas = t.id_komunitas
    GROUP BY k.id_komunitas
    HAVING jumlah_komentar > 10
    ORDER BY jumlah_komentar DESC
    LIMIT 5
");

// Ambil daftar topik
$query_topik = mysqli_query($kon, "
    SELECT t.id_topik, t.judul_topik, t.deskripsi_topik, u.nama AS pembuat 
    FROM topik t 
    JOIN user u ON t.dibuat_oleh = u.id_user 
    ORDER BY t.id_topik DESC
");

// Tambah komunitas (proses langsung dalam file)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_forum = mysqli_real_escape_string($kon, $_POST['nama_komunitas']);
    $deskripsi = mysqli_real_escape_string($kon, $_POST['deskripsi']);
    $created_by = $row_user['id_user'];

    // File upload handling
    $upload_dir = '../uploads/komunitas/';
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Check if file was uploaded
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_name = uniqid() . '_' . basename($_FILES['gambar']['name']);
        $target_path = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_type, $allowed_types)) {
            // Max file size 5MB
            if ($_FILES['gambar']['size'] <= 5 * 1024 * 1024) {
                // Move uploaded file
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
                    // Insert into database with file path
                    $query_insert = "INSERT INTO komunitas (nama_komunitas, deskripsi, dibuat_oleh, gambar) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($kon, $query_insert);
                    mysqli_stmt_bind_param($stmt, 'ssss', $nama_forum, $deskripsi, $created_by, $file_name);
                    mysqli_stmt_execute($stmt);
                    
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        mysqli_stmt_close($stmt);
                        header("Location: forum_komunitas.php");
                        exit;
                    } else {
                        $upload_error = "Gagal menyimpan data komunitas.";
                    }
                } else {
                    $upload_error = "Gagal mengunggah gambar.";
                }
            } else {
                $upload_error = "Ukuran gambar terlalu besar. Maksimal 5MB.";
            }
        } else {
            $upload_error = "Jenis file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF.";
        }
    } else {
        $upload_error = "Silakan pilih gambar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Dashboard</title>
  <link rel="stylesheet" href="/GameGlee/Gamify/assets/css/styleDashboard.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <!-- Favicons -->
   <link href="/GameGlee/Gamify/assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
  <style>
    .tabler--discount {
  display: inline-block;
  width: 24px;
  height: 24px;
  color: #fff;
  --svg: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cg fill='none' stroke='%23000' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'%3E%3Cpath d='m9 15l6-6'/%3E%3Ccircle cx='9.5' cy='9.5' r='.5' fill='%23000'/%3E%3Ccircle cx='14.5' cy='14.5' r='.5' fill='%23000'/%3E%3Cpath d='M3 12a9 9 0 1 0 18 0a9 9 0 1 0-18 0'/%3E%3C/g%3E%3C/svg%3E");
  background-color: currentColor;
  -webkit-mask-image: var(--svg);
  mask-image: var(--svg);
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
  -webkit-mask-size: 100% 100%;
  mask-size: 100% 100%;
}
  </style>
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
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php" class="active"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
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
            <div class="container-plus">
                <h2>Tambah Komunitas</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_komunitas">Nama Komunitas</label>
                        <input type="text" class="form-control" id="nama_komunitas" name="nama_komunitas" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                    <a href="forum_komunitas.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="/GameGlee/Gamify/assets/js/index.js"></script>
    <!-- Sertakan Bootstrap JS dan jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        
</body>
</html>