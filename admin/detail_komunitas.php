<?php
session_start();
include '../db.php';

// Cek apakah user login
if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Gunakan nama admin dari session
$user = $_SESSION["admin"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$nama_admin = $_SESSION["admin"];

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
                         VALUES ('$judul_topik', '$deskripsi', $id_komunitas, $id_user)";
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
  <title>GameGlee's Comunity</title>
  <link rel="stylesheet" href="/GameGlee/Gamify/assets/css/styleDashboardA.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="/GameGlee/Gamify/assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
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
    <ul>
      <li><a href="index.php" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <br><br><br>
      <li><a href="forum_komunitas.php" class="active"><span class="gg--community"></span>KOMUNITAS</a></li>
      <li><a href="logout.php"><span class="tabler--logout"></span>LOGOUT</a></li>
    </ul>
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
                <li><a href="chat.php?user_id=<?php echo $row['user_id']; ?>" class="active"><span class="tdesign--cutomerservice"></span></a></li>
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
                  <span class="d-none d-md-block dropdown-toggle ps-2"><i style="font-size: 20px" class="bi bi-person"></i>&nbsp; Halo, <?= $_SESSION["admin"] ?>!</span>
                  </a>
                </li>
              </ul>
            </div>
        </div>
    <main id="main" class="main">
        <div class="container-detailcom">
            <a href="forum_komunitas.php" class="btn-light">&larr; Kembali</a>
            <!-- Judul Komunitas dan Deskripsi -->
            <div class="jumbotron text-center bg-info text-white">
                <h1 class="display-4"><?php echo htmlspecialchars($komunitas['nama_komunitas']); ?></h1>
                <p class="lead"><?php echo nl2br(htmlspecialchars($komunitas['deskripsi'])); ?></p>
            </div>

            <!-- Daftar Topik -->
            
            <div class="d-flex justify-content-end mb-3">
                <h4 class="list-topik">Daftar Topik</h4>
                <button class="btn btn-warning" onclick="bukaModal()">Tambah Topik</button>
            </div>
            <div class="container-card">
                <?php while ($topik = mysqli_fetch_assoc($query_topik)): ?>
                    <div class="row">
                        <a href="topik.php?id=<?php echo $topik['id_topik']; ?>">
                            <div class="card-d card-custom">
                                <div class="card-body-d">
                                    <h5 class="card-title-d topic-card-title"><?php echo htmlspecialchars($topik['judul_topik']); ?></h5>
                                    <p class="card-text-d"><?php echo nl2br(htmlspecialchars($topik['deskripsi_topik'])); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="topic-card-author">
                                            <i class="fas fa-user mr-1"></i>
                                            <?php echo htmlspecialchars($topik['pembuat']); ?>
                                        </small>
                                        <small class="text-muted">
                                            <?php echo date('d M Y', strtotime($topik['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <div id="tambahTopikModal" class="modal">
            
            <div class="modal-content">
                <span class="close" onclick="tutupModal()">&times;</span>
                <h3>Tambah Topik Baru</h3>
                <form method="POST">
                    <div>
                        <label for="judul_topik">Judul Topik</label>
                        <input type="text" id="judul_topik" name="judul_topik" required>
                    </div>
                    <div>
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div>
                        <button type="button" onclick="tutupModal()" class="button-batal">Batal</button>
                        <button type="submit" class="button-tambah">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        function bukaModal() {
            document.getElementById('tambahTopikModal').style.display = 'block';
        }

        function tutupModal() {
            document.getElementById('tambahTopikModal').style.display = 'none';
        }

        // Menutup modal ketika klik di luar modal
        window.onclick = function(event) {
            var modal = document.getElementById('tambahTopikModal');
            if (event.target == modal) {
                tutupModal();
            }
        };
    </script>
    <script src="/GameGlee/Gamify/assets/js/index.js"></script>
</body>
</html>
        