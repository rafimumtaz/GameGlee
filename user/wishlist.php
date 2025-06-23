<?php
session_start();
include '../db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); 
    exit();
}
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];

$sql = "SELECT p.*, w.id_wishlist AS wishlist_id FROM wishlist w 
        JOIN produk p ON w.id_produk = p.id_produk 
        WHERE w.user_id = ?";

$stmt = $kon->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlistItems = $result->fetch_all(MYSQLI_ASSOC);

// Tambahkan fungsi untuk mengecek stok produk
function checkProductStock($kon, $productId) {
    $stockSql = "SELECT stok FROM produk WHERE id_produk = ?";
    $stockStmt = $kon->prepare($stockSql);
    $stockStmt->bind_param("i", $productId);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();
    $product = $stockResult->fetch_assoc();
    return $product['stok'] > 0;
}

// Proses penghapusan dari wishlist
if (isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    $checkSql = "SELECT * FROM wishlist WHERE user_id = ? AND id_produk = ?";
    $checkStmt = $kon->prepare($checkSql);
    $checkStmt->bind_param("ii", $user_id, $productId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $deleteSql = "DELETE FROM wishlist WHERE user_id = ? AND id_produk = ?";
        $deleteStmt = $kon->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $user_id, $productId);
        $deleteStmt->execute();
    }
    header("Location: wishlist.php");
    exit();
}

// Proses menambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    // Menambahkan ke keranjang
    $addToCartSql = "INSERT INTO keranjang (user_id, id_produk, jumlah) VALUES (?, ?, 1)";
    $addToCartStmt = $kon->prepare($addToCartSql);
    $addToCartStmt->bind_param("ii", $user_id, $productId);
    $addToCartStmt->execute();

    // Hapus dari wishlist
    $removeWishlistSql = "DELETE FROM wishlist WHERE user_id = ? AND id_produk = ?";
    $removeWishlistStmt = $kon->prepare($removeWishlistSql);
    $removeWishlistStmt->bind_param("ii", $user_id, $productId);
    $removeWishlistStmt->execute();

    header("Location: add_to_cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Wishlist</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
      <li><a href="history_pembayaran.php"><span class="ic--twotone-history"></span>HISTORY PEMBELIAN</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <li><a href="wishlist.php"><span class="ph--list-star"></span>WISHLIST</a></li>
      <br><br><br><br><br><br><br><br><br><br>
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
            <a href="profil.php" class="active">
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
  <main id="main" class="main-promo">
        <div class="pagetitle">
            <h1><i class="bi bi-heart"></i>&nbsp; Wishlist</h1>
          
          
        </div>

        <div class="container mt-5">
            <?php if (empty($wishlistItems)): ?>
                <p>Wishlist Anda kosong.</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($wishlistItems as $item): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <a href="detail_produk.php?product_id=<?= $item['id_produk']; ?>">
                                    <img src="../uploads/<?= $item['gambar']; ?>" class="card-img-top" alt="<?= $item['nama_produk']; ?>">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $item['nama_produk']; ?></h5>
                                    <p class="card-text"><strong>Harga: </strong>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></p>
                                    <p class="card-text"><strong>Stok: </strong><?= checkProductStock($kon, $item['id_produk']) ? 'Tersedia' : 'Tidak Tersedia'; ?></p>
                                    <div class="d-flex justify-content-between">
                                        <?php if (checkProductStock($kon, $item['id_produk'])): ?>
                                            <form action="" method="POST" style="display:inline;">
                                                <input type="hidden" name="product_id" value="<?= $item['id_produk']; ?>">
                                                <button type="submit" name="add_to_cart" class="btn btn-success btn-sm">Tambah ke Keranjang</button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="wishlist.php?remove=<?= $item['id_produk']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="produk.php" class="btn btn-secondary">Lihat Produk Lainnya</a>
            </div>
        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/index.js"></script>
</body>
</html>