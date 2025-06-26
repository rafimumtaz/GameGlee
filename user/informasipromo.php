<?php
session_start();
include('../db.php');
$page = "info";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user_id'");
$row_user = mysqli_fetch_array($kue_user);

// Fetch all active informasipromo
$current_date = date('Y-m-d');
$sql = "SELECT * FROM informasipromo WHERE start_date <= ? AND end_date >= ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "ss", $current_date, $current_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$informasipromo = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Separate informasipromo by type
$all_informasipromo = $informasipromo;
$discount_informasipromo = array_filter($informasipromo, function($promo) {
    return $promo['promo_type'] === 'discount';
});
$bonus_informasipromo = array_filter($informasipromo, function($promo) {
    return $promo['promo_type'] === 'bonus';
});

$cartSuccess = isset($_GET['cart_success']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Promo</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->                                                                                    
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .text{
            color: darkblue;
        }
        h1{
            color: black !important ;
        }
        .promo-card {
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .promo-card:hover {
            transform: scale(1.05);
        }
        .promo-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .promo-type-btn {
            background-color: #fff;
            color: #4154f1;
            border: 2px solid #4154f1;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .promo-type-btn.active {
            background-color: #4154f1;
            color: #fff;
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
      <li><a href="index.php"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="most_favorite.php"><span class="ph--list-heart"></span>MOST FAVORITE ITEM</a></li>
      <li><a href="informasipromo.php"class="active"><span class="tabler--discount"></span>PROMO</a></li>
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
            <span>
              </i>Halo, <?= $_SESSION["user"] ?>!
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <main id="main" class="main-promo">
        <div class="pagetitle">
            <h1><i class="bi bi-megaphone"></i>&nbsp;Promo Spesial</h1>
        </div>
        <section class="section">
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <p></p>
                            </div>
                            <div class="text-center mb-4">
                                <h3 class="text">Jangan lewatkan penawaran terbaik kami!</h3>
                                <button class="promo-type-btn active" data-filter="all">Semua Promo</button>
                                <button class="promo-type-btn" data-filter="discount">Promo Diskon</button>
                                <button class="promo-type-btn" data-filter="bonus">Promo Bonus</button>
                                <?php if ($cartSuccess): ?>
                                    <div class="alert alert-success notification">Produk berhasil ditambahkan ke keranjang!</div>
                                <?php endif; ?>
                            </div>
                            <?php if (empty($informasipromo)): ?>
                                <div class="alert alert-info text-center">
                                    Saat ini tidak ada promo yang tersedia. Silakan cek kembali nanti!
                                </div>
                            <?php else: ?>
                                <div class="row" id="promoContainer">
                                    <?php foreach ($informasipromo as $promo): ?>
                                        <div class="col-md-3 promo-card" data-type="<?= htmlspecialchars($promo['promo_type']) ?>">
                                            <div class="card">
                                                <?php if (!empty($promo['photo_url'])): ?>
                                                    <img src="../admin/uploads/<?= htmlspecialchars($promo['photo_url']); ?>" class="card-img-top" alt="<?= htmlspecialchars($promo['title']) ?>">
                                                <?php else: ?>
                                                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">No Image</div>
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <?php if ($promo['promo_type'] === 'discount'): ?>
                                                        <p class="card-text"><strong><p class="card-title"><?= htmlspecialchars($promo['title']) ?> Disc <?= $promo['discount_percentage'] ?>%</p></strong></p>
                                                    <?php elseif ($promo['promo_type'] === 'bonus'): ?>
                                                        <p class="card-text"><strong><p class="card-title"><?= htmlspecialchars($promo['title']) ?> Gratis <?= htmlspecialchars($promo['bonus_item']) ?></p></strong></p>
                                                    <?php endif; ?>
                                                    <p class="card-text"><?= htmlspecialchars($promo['description']) ?></p>
                                                    <p class="card-text">
                                                        <small class="text-muted">Berlaku sampai: <?= htmlspecialchars($promo['end_date']) ?></small>
                                                    </p>
                                                    <div class="countdown mt-3">
                                                        <i class="bi bi-clock me-2"></i>
                                                        <span class="days-left">
                                                            <?php
                                                            $end = new DateTime($promo['end_date']);
                                                            $now = new DateTime();
                                                            $interval = $end->diff($now);
                                                            echo $interval->days . ' hari tersisa';
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex mt-3">
                                                        <form method="POST" action="add_to_cart.php" class="d-inline">
                                                            <input type="hidden" name="product_id" value="<?= $promo['bonus_item']; ?>">
                                                            <button type="submit" name="add_to_cart" class="btn btn-success">Add to Cart</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Promo type filtering
            const filterButtons = document.querySelectorAll('.promo-type-btn');
            const promoCards = document.querySelectorAll('.promo-card');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    promoCards.forEach(card => {
                        const cardType = card.getAttribute('data-type');
                        
                        if (filter === 'all' || cardType === filter) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
  <script src="../assets/js/index.js"></script>
</body>
</html>