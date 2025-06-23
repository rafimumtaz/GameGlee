<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Validasi product_id
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: produk.php");
    exit();
}

$productId = intval($_GET['product_id']);

// Query untuk mengambil detail produk dengan join kategori
$stmt = $kon->prepare("
    SELECT p.*, k.nama_kategori 
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
    WHERE p.id_produk = ?
");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah produk ditemukan
if ($result->num_rows === 0) {
    echo "<script>
        alert('Produk tidak ditemukan');
        window.location.href = 'produk.php';
    </script>";
    exit();
}

// Ambil data produk
$product = $result->fetch_assoc();

// Proses tambah ke wishlist
if (isset($_POST['add_to_wishlist'])) {
    // Pastikan pengguna sudah login
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $userId = $_SESSION['user'];
    $kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$userId'");
    $row_user = mysqli_fetch_array($kue_user);
    $productId = $product['id_produk']; // Gunakan ID produk dari hasil query

    // Cek apakah produk sudah ada di wishlist
    $checkSql = "SELECT * FROM wishlist WHERE user_id = ? AND id_produk = ?";
    $checkStmt = $kon->prepare($checkSql);
    $checkStmt->bind_param("ii", $row_user, $productId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows == 0) {
        // Tambahkan produk ke wishlist
        $insertSql = "INSERT INTO wishlist (user_id, id_produk) VALUES (?, ?)";
        $insertStmt = $kon->prepare($insertSql);
        $insertStmt->bind_param("ii", $row_user, $productId);
        $insertStmt->execute();
    }

    header("Location: wishlist.php?wishlist_success=1");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Produk</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .product-image {
            max-height: 500px;
            width: 100%;
            object-fit: cover;
        }
        .product-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        .btn-custom {
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: scale(1.05);
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
              <i class="bi bi-person"></i>Halo, <?= $_SESSION["user"] ?>!
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <main id="main" class="main-promo">
        <div class="container mt-5">
            <div class="row">
                <!-- Gambar Produk -->
                <div class="col-md-6">
                    <img 
                        src="../uploads/<?= htmlspecialchars($product['gambar']) ?>" 
                        alt="<?= htmlspecialchars($product['nama_produk']) ?>" 
                        class="img-fluid product-image rounded shadow"
                    >
                </div>
                
                <!-- Detail Produk -->
                <div class="col-md-6 product-details">
                    <h1 class="mb-3"><?= htmlspecialchars($product['nama_produk']) ?></h1>
                    
                    <!-- Kategori -->
                    <div class="mb-3">
                        <span class="badge bg-primary">
                            <?= htmlspecialchars($product['nama_kategori'] ?? 'Tidak Berkategori') ?>
                        </span>
                    </div>
                    
                    <!-- Harga -->
                    <h3 class="text-danger mb-3">
                        Rp. <?= number_format($product['harga'], 0, ',', '.') ?>
                    </h3>
                    
                    <!-- Deskripsi -->
                    <p class="mb-4"><?= htmlspecialchars($product['deskripsi']) ?></p>
                    
                    <!-- Informasi Tambahan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Stok:</strong> 
                            <span class="<?= $product['stok'] > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $product['stok'] > 0 ? $product['stok'] : 'Habis' ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="mt-4">
                        <?php if ($product['stok'] > 0): ?>
                            <form method="POST" action="add_to_cart.php">
                                <input type="hidden" action="add_to_cart.php" name="product_id" value="<?= $product['id_produk']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary btn-custom me-2">&nbsp;Add to Cart</button>
                            </form>
                            <br>
                            <form method="POST" action="checkout.php">
                                <input type="hidden" name="product_id" value="<?= $product['id_produk']; ?>">
                                <button type="submit" name="buy_now" class="btn btn-success">&nbsp;Buy Now</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $index; ?>">
                                <button type="submit" name="add_to_wishlist" class="btn btn-warning btn-wishlist">Add to Wishlist</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/index.js"></script>
</body>
</html>

<?php
// Tutup statement dan koneksi
$stmt->close();
$kon->close();
?>