<?php
session_start();
include '../db.php';
$page = "komunitas";

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Community</title>
  <link rel="stylesheet" href="../assets/css/stylekomunitas.css">
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
                <div class="search-container d-flex mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search Community">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
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
        </main>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/js/index.js"></script>
</body>
</html>