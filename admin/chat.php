<?php 
session_start();
include '../db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil user_id dari query string
$user_id = $_GET['user_id'];

// Ambil nama user berdasarkan user_id
$user_query = "SELECT nama FROM user WHERE id_user='$user_id'";
$user_result = mysqli_query($kon, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$user_name = $user_data['nama'] ?? 'Tidak Diketahui';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = mysqli_real_escape_string($kon, $_POST['message']);
    $query = "INSERT INTO messages (sender, user_id, message) VALUES ('admin', '$user_id', '$message')";
    
    if (!mysqli_query($kon, $query)) {
        echo "Error: " . mysqli_error($kon);
    }
}

$query = "SELECT * FROM messages WHERE user_id='$user_id' ORDER BY timestamp";
$result = mysqli_query($kon, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Customer Service</title>
  <link rel="stylesheet" href="/GameGlee/Gamify/assets/css/styleChat.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
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
      <li><a href="index.php" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="user.php"><span class="ph--user-list-bold"></span>DAFTAR USER</a></li>
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
                <li><a href="chat.php" class="active"><span class="tdesign--cutomerservice"></span></a></li>
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
            <div class="container">
                <div class="icon-chat">
                    <span class="tdesign--cutomerservice-chat"></span>
                </div>
                <div class="top-chat">
                    <a href="index_admin.php" class="back-link"><span class="lets-icons--back"></span></a>
                    <h2><?php echo htmlspecialchars($user_name); ?> (User  ID: <?php echo htmlspecialchars($user_id); ?>)</h2>
                </div>
                
                <div class="chat-box">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="message-wrapper <?php echo $row['sender'] == 'admin' ? 'align-end' : 'align-start'; ?>">
                            <div class="message <?php echo $row['sender'] == 'admin' ? 'admin-message' : 'user-message'; ?>">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <form method="POST" class="message-form">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Ketik pesan...">
                        <button type="submit"><span class="akar-icons--send"></span></button>
                    </div>
                </form>
            </div>
        </main>
        <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
        <script src="/GameGlee/Gamify/assets/js/index.js"></script>
</body>
</html>