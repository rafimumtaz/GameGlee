<?php
session_start();
include '../db.php';
$page = "chat";

// Pastikan user telah login dan session user_id tersedia
if (!isset($_SESSION['user'])) {
    die("Harap login terlebih dahulu.");
}

$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);

$user_id = $row_user['id_user'];

// Ambil nama admin untuk ditampilkan di chat
$admin_query = "SELECT nama FROM user WHERE level='admin' LIMIT 1";
$admin_result = mysqli_query($kon, $admin_query);
$admin_data = mysqli_fetch_assoc($admin_result);
$admin_name = $admin_data['nama'] ?? 'Admin';

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = mysqli_real_escape_string($kon, $_POST['message']);
    $query = "INSERT INTO messages (sender, user_id, message) VALUES ('user', '$user_id', '$message')";
    if (!mysqli_query($kon, $query)) {
        echo "Error: " . mysqli_error($kon);
    }
}

// Ambil pesan antara user dan admin
$query = "SELECT * FROM messages WHERE user_id='$user_id' ORDER BY timestamp";
$result = mysqli_query($kon, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Dashboard</title>
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
      <li><a href="index.php"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="most_favorite.php"><span class="ph--list-heart"></span>MOST FAVORITE ITEM</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>PROMO</a></li>
      <li><a href="forum_komunitas.php"><span class="gg--community"></span>KOMUNITAS</a></li>
      <li><a href="feedback.php"><span class="mdi--feedback-outline"></span>FEEDBACK</a></li>
      <li><a href="promo.php" ><span class="tabler--discount"></span>CODE PROMO</a></li>
      <br><br><br>
      <li><a href="chat.php"class="active"><span class="tdesign--service"></span>CUSTOMER SERVICE</a></li>
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
  <div class="main-content">
  <main id="main-3" class="main-3">
            <div class="container-3">
                <div class="icon-chat">
                    <span class="tdesign--cutomerservice-chat"></span>
                </div>  
                <div class="top-chat-1">
                    <h2>Customer Service</h2>
                </div>
                
                <div class="chat-box-1">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="message-row <?php echo $row['sender'] == 'user' ? 'user-message-1' : 'other-message'; ?>">
                            <div class="message <?php echo $row['sender'] == 'user' ? 'bg-primary text-white' : 'bg-light'; ?>">
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
        <script src="../assets/js/index.js"></script>
</body>
</html>