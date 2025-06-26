<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// Mengambil daftar user yang memiliki pesan dengan menghubungkan tabel messages dan user berdasarkan id_user
$query = "SELECT user.nama, messages.user_id, MAX(messages.timestamp) as last_message_time
          FROM messages
          JOIN user ON messages.user_id = user.id_user
          GROUP BY messages.user_id
          ORDER BY last_message_time DESC";
$result = mysqli_query($kon, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Customer Service</title>
  <link rel="stylesheet" href="../assets/css/styleChat.css">
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
      <li><a href="index.php" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <br><br><br>
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
        <main id="main-2" class="main-2">
        <div class="container-2">
                <div class="icon-chat">
                    <span class="tdesign--cutomerservice-chat"></span>
                </div>
                <div class="list-cht"><h2 >Daftar Chat User</h2></div>
                <ul class="list-group">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <li class="list-group-item">
                            <span><?php echo htmlspecialchars($row['nama']); ?></span>
                            <a class="btn btn-primary btn-sm" href="chat.php?user_id=<?php echo $row['user_id']; ?>">Buka Chat</a>
                        </li>
                    <?php } ?>
                </ul>
        </div>
    </main>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/js/index.js"></script>
</body>
</html>