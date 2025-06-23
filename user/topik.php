<?php
session_start();
include '../db.php'; // File koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);

// Gunakan ID user dari session
$id_user = $row_user["id_user"]; // Menggunakan ID user dari session

// Ambil topik berdasarkan ID
$id_topik = $_GET['id'] ?? 0; // Changed from 'id_topik' to 'id'
$query_topik = mysqli_query($kon, "SELECT t.*, u.nama AS pembuat 
                                   FROM topik t 
                                   JOIN user u ON t.dibuat_oleh = u.id_user 
                                   WHERE t.id_topik = '$id_topik'");

if (mysqli_num_rows($query_topik) == 0) {
    echo "Topik tidak ditemukan!";
    exit;
}

$topik = mysqli_fetch_assoc($query_topik);

// Proses kirim komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $id_user) {
    $isi_komentar = mysqli_real_escape_string($kon, $_POST['isi_komentar']);
    $query = "INSERT INTO komentar (id_topik, id_user, isi_komentar) VALUES ('$id_topik', '$id_user', '$isi_komentar')";
    mysqli_query($kon, $query);
    header("Location: topik.php?id=$id_topik");
    exit;
}

// Ambil semua komentar untuk topik ini
$query_komentar = mysqli_query($kon, "
    SELECT k.*, u.nama, u.level 
    FROM komentar k
    JOIN user u ON k.id_user = u.id_user
    WHERE k.id_topik = '$id_topik'
    ORDER BY k.created_at ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Community</title>
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
        <main id="main" class="main">
            <div class="container-topic">
                <!-- Tombol Kembali -->
                <a href="detail_komunitas.php?id=<?php echo $topik['id_komunitas']; ?>" class="btn-btn-light">← Kembali</a>

                <!-- Topik -->
                <div class="topik-box">
                    <h5><?= htmlspecialchars($topik['judul_topik']) ?></h5>
                    <p><?= nl2br(htmlspecialchars($topik['deskripsi_topik'])) ?></p>
                    <small>Dibuat oleh: <?= htmlspecialchars($topik['pembuat']) ?> pada <?= date('d M Y H:i', strtotime($topik['created_at'])) ?></small>
                </div>

                <!-- Daftar Komentar -->
                <h6>Komentar</h6>
                <?php while ($komentar = mysqli_fetch_assoc($query_komentar)): ?>
                    <div class="komentar-box">
                        <strong>
                            <?= htmlspecialchars($komentar['nama']) ?>
                            <?php if ($komentar['level'] == 'admin'): ?>
                                <span class="admin-label">admin</span>
                            <?php endif; ?>
                        </strong>
                        <p><?= nl2br(htmlspecialchars($komentar['isi_komentar'])) ?></p>
                        <small><?= date('d M Y H:i', strtotime($komentar['created_at'])) ?></small>
                    </div>
                <?php endwhile; ?>

                <!-- Form Komentar -->
                <?php if ($id_user): ?>
                    <form method="POST" class="mt-3">
                        <div class="form-group">
                            <textarea name="isi_komentar" rows="3" class="form-control comment-input" placeholder="Tulis komentar..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">Kirim</button>
                    </form>
                <?php else: ?>
                    <p><a href="../login.php" class="text-warning">Login</a> untuk memberikan komentar.</p>
                <?php endif; ?>
            </div>
        </main>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
        <script src="../assets/js/index.js"></script>
</body>
</html>