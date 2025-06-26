<?php
session_start();
include '../db.php';
$page = "komunitas";

// Cek apakah user adalah admin
if (!isset($_SESSION["admin"])) {
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

    // Query untuk insert data tanpa gambar
    $query_insert = "INSERT INTO komunitas (nama_komunitas, deskripsi, dibuat_oleh) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($kon, $query_insert);
    mysqli_stmt_bind_param($stmt, 'sss', $nama_forum, $deskripsi, $created_by);
    
    // Eksekusi query dan periksa hasilnya
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: forum_komunitas.php");
        exit;
    } else {
        $upload_error = "Gagal menyimpan data komunitas.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Dashboard</title>
  <link rel="stylesheet" href="../assets/css/styleDashboardA.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
    <style>
        /* Gaya untuk latar belakang modal */
   .modal {
    display: none; /* Disembunyikan secara default */
    position: fixed; /* Tetap di tempat saat scrolling */
    z-index: 1; /* Pastikan di atas elemen lainnya */
    left: 0;
    top: 0;
    width: 100%; /* Penuh layar */
    height: 100%; /* Penuh layar */
    overflow: auto; /* Jika konten terlalu besar */
    background-color: rgba(0, 0, 0, 0.4); /* Latar belakang semi-transparan */
}

/* Gaya untuk konten modal */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* Pusatkan secara vertikal dan horizontal */
    padding: 20px;
    border: none;
    width: 50%; /* Lebar modal */
    border-radius: 10px; /* Sudut membulat */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s; /* Animasi muncul */
}

/* Gaya untuk tombol tutup */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}

/* Gaya untuk tombol di dalam modal */
.modal-content .button-batal {
    background-color: #8d8d8d; /* Hijau */
    color: white;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 5px;
    cursor: pointer;
}

.modal-content .button-batal:hover {
    background-color: #c7c5c5; /* Hijau lebih gelap saat hover */
}

.modal-content .button-tambah {
  background-color: #0056b3; /* Hijau */
  color: white;
  border: none;
  padding: 10px 20px;
  margin: 5px;
  border-radius: 5px;
  cursor: pointer;
}

.modal-content .button-tambah:hover {
  background-color: #007bff; /* Hijau lebih gelap saat hover */
}

/* Gaya untuk form elemen */
.modal-content input[type="text"],
textarea {
    width: 100%; /* Lebar penuh */
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

.modal-content label {
    color: #ff8929;
    font-weight: bold;
    letter-spacing: 2px;
}

/* Animasi untuk munculnya modal */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
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
      <li><a href="order.php"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit-1"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <li><a href="promo.php"><span class="tabler--discount"></span>CODE PROMO</a></li>
      <li><a href="forum_komunitas.php" class="active"><span class="gg--community"></span>KOMUNITAS</a></li>
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
        <main id="main" class="main">
            <div class="container-com">
                <!-- Title -->
                <h1 class="text-center mb-5">COMMUNITY</h1>
                
                <!-- Recent and Top Community -->
                <div class="row mb-4 d-flex justify-content-end">
                    <div class="col-md-6">
                        <h4 class="section-title">Recent Communities</h4>
                        <?php while ($recent = mysqli_fetch_assoc($query_recent)): ?>
                            <a href="detail_komunitas.php?id=<?php echo $recent['id_komunitas']; ?>">
                                <div class="card mb-3 card-custom">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="fas fa-users community-icon"></i>
                                        <span class="community-card-title"><?php echo htmlspecialchars($recent['nama_komunitas']); ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                    <div class="col-md-6">
                        <h4 class="section-title">Top Communities</h4>
                        <?php while ($top = mysqli_fetch_assoc($query_top)): ?>
                            <a href="detail_komunitas.php?id=<?php echo $top['id_komunitas']; ?>">
                                <div class="card mb-3 card-custom">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-trophy community-icon"></i>
                                            <span class="community-card-title"><?php echo htmlspecialchars($top['nama_komunitas']); ?></span>
                                        </div>
                                        <span class="badge badge-warning"><?php echo $top['jumlah_komentar']; ?> Komentar</span>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Search and Add Community -->
                <div class="search-container d-flex justify-content-between align-items-center mb-4">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="text" class="form-control" placeholder="Search Community">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <!-- Button to redirect to tambah_komunitas.php -->
                    <button class="btn btn-warning" onclick="bukaModal()">Tambah Komunitas Baru</button>
                </div>


                <!-- List of Topics -->
                <div class="list-group">
                    <?php while ($topik = mysqli_fetch_assoc($query_topik)): ?>
                        <a href="topik.php?id=<?php echo $topik['id_topik']; ?>" class="list-group-item list-group-item-action">
                            <div class="list-group-content">
                                <h5 class="mb-1"><?php echo htmlspecialchars($topik['judul_topik']); ?></h5>
                                <p class="mb-1"><?php echo nl2br(htmlspecialchars($topik['deskripsi_topik'])); ?></p>
                                <small class="text-muted">By: <?php echo htmlspecialchars($topik['pembuat']); ?></small>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <!-- Modal for Adding Community -->
            <div id="modalTambahKomunitas" class="modal">
            <div class="modal-content">
                <span class="close" onclick="tutupModal()">&times;</span>
                <h3>Tambah Komunitas Baru</h3>
                <form action="forum_komunitas.php" method="POST" enctype="multipart/form-data">
                <label for="nama_komunitas">Nama Komunitas:</label>
                <input type="text" id="nama_komunitas" name="nama_komunitas" required>

                <label for="deskripsi">Deskripsi Komunitas:</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>

                <div class="modal-buttons">
                    <button type="submit" class="button-tambah">Tambah</button>
                    <button type="button" class="button-batal" onclick="tutupModal()">Batal</button>
                </div>
                </form>
            </div>
            </div>
        </main>
        <script>
        function bukaModal() {
            document.getElementById("modalTambahKomunitas").style.display = "block";
        }

        function tutupModal() {
            document.getElementById("modalTambahKomunitas").style.display = "none";
        } 
        </script>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
        <script src="../assets/js/index.js"></script>
</body>
</html>