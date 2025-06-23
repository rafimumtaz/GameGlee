<?php
session_start();
require "../db.php";
$page = "dashboard";

if (!isset($_SESSION["user"]))
{
  header("Location: ../login.php");
}

// GET ID FROM USER
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
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
      <li><a href="index.php" class="active"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="most_favorite.php"><span class="ph--list-heart"></span>MOST FAVORITE ITEM</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>PROMO</a></li>
      <li><a href="forum_komunitas.php"><span class="gg--community"></span>KOMUNITAS</a></li>
      <li><a href="feedback.php"><span class="mdi--feedback-outline"></span>FEEDBACK</a></li>
      <li><a href="promo.php" ><span class="tabler--discount"></span>CODE PROMO</a></li>
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
              <i class="bi bi-person"></i>Halo, <?//= $_SESSION["user"] ?>!
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="main-content">
    <!-- Featured Game Section -->
    <div class="featured-game">
      <div class="carousel">
        <!-- Slide 1 -->
        <div class="slide">
          <img src="../assets/img/SLIDE 1.png" alt="BG 1" class="game-image">
          <div class="game-info">
            <h5 class="game-subtitle">NEW</h5>
            <h2 class="game-title">Xbox Series X - 1TB</h2>
            <p class="game-description">Reveal the Xbox Series X - 1TBand Elevate Your Coolness</p>
            <button class="game-button">Catch Up!</button>
          </div>
        </div>
        <!-- Slide 2 -->
        <div class="slide">
          <img src="../assets/img/SLIDE 2.png" alt="BG 2" class="game-image">
          <div class="game-info">
            <h5 class="game-subtitle">NEW</h5>
            <h2 class="game-title">Xbox Series X - 2TB Galaxy Black Special Edition</h2>
            <p class="game-description">Reveal the Xbox Series X - 2TB and Elevate Your Coolness</p>
            <button class="game-button">Catch Up!</button>
          </div>
        </div>
        <!-- Slide 3 -->
        <div class="slide">
          <img src="../assets/img/SLIDE 3.png" alt="BG 3" class="game-image">
          <div class="game-info">
            <h5 class="game-subtitle">NEW</h5>
            <h2 class="game-title">Xbox Series X - 1TB Digital Edition (White)</h2>
            <p class="game-description">Reveal the Xbox Series X - 1TB Digital Edition and Elevate Your Coolness</p>
            <button class="game-button">Catch Up!</button>
          </div>
        </div>
        <!-- Dots -->
        <div class="carousel-dots">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>
      <!-- Controls -->
      <button class="carousel-control prev" onclick="changeSlide(-1)"><span class="ic--round-arrow-back-ios"></span></button>
      <button class="carousel-control next" onclick="changeSlide(1)"><span class="ic--round-arrow-forward-ios"></span></button>
    </div>

    <div class="text-box">
      <p class="text-learn">All Product</p>
      <ul class="desc">
        <li><a href="produk.php" >Learn More</span></a></li>
      </ul>
    </div>
    
    <!-- Special Offers Section -->
    <div class="special-offers">
      <div class="game-card">
        <div class="card-info">
          <a href="detail_produk.php"> <button class="game-button">Available Now!</button></a>
        </div>
        <img src="../assets/img/NINTENDO/GAME/Kirbys Return to Dream Land Deluxe.jpg" alt="Kirbys Return to Dream Land Deluxe">
      </div>
      <div class="game-card">
        <div class="card-info">
          <button class="game-button">Available Now!</button>
        </div>
        <a href="detail_produk.php"><img src="../assets/img/PLAYSTATION/GAME/God of War Ragnarök.png" alt="God of War Ragnarök"></a>
      </div>
      <div class="game-card">
        <div class="card-info">
          <button class="game-button">Available Now!</button>
        </div>
        <a href="detail_produk.php"><img src="../assets/img/PLAYSTATION/GAME/Gran Turismo 7 (English Chinese Korean Thai Ver.).png" alt="Gran Turismo 7 (English Chinese Korean Thai Ver.)"></a>
      </div>
      <div class="game-card">
        <div class="card-info">
          <button class="game-button">Available Now!</button>
        </div>
        <a href="detail_produk.php"><img src="../assets/img/XBOX/GAME/Call of Duty  Black Ops 6 - Vault Edition Upgrade.jpeg" alt="Call of Duty  Black Ops 6 - Vault Edition Upgrade"></a>
      </div>
    </div>
  </div>
  <script src="../assets/js/index.js"></script>
</body>
</html>