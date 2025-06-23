<?php
session_start();
include('../db.php'); 
$page = "produk";

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user_id'");
$row_user = mysqli_fetch_array($kue_user);

// Ambil kategori dari database
$sql = "SELECT id_kategori, nama_kategori FROM kategori";
$result = mysqli_query($kon, $sql);
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[$row['id_kategori']] = $row['nama_kategori'];
}

// Ambil produk dari database setiap kali halaman dimuat
$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p 
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_produk NOT IN (SELECT bonus_item FROM informasipromo)";
$result = mysqli_query($kon, $sql);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => $row['id_produk'],
        'name' => $row['nama_produk'],
        'category' => $row['nama_kategori'], 
        'price' => $row['harga'],
        'stock' => $row['stok'],
        'image' => $row['gambar'],
        'description' => $row['deskripsi'] 
    ];
}

// Pastikan kategori sudah dimuat di session
if (!isset($_SESSION['categories'])) {
    $_SESSION['categories'] = [];
}

$_SESSION['products'] = $products; 

$filteredProducts = $products ?? []; 
// Filter kategori dan harga seperti semula
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_category'])) {
    $selectedCategory = $_POST['category'] ?? '';
    $selectedPriceOrder = $_POST['price'] ?? '';

    if (!empty($selectedCategory)) {
        $filteredProducts = array_filter($filteredProducts, function($product) use ($selectedCategory) {
            return $product['category'] === $selectedCategory;
        });
    }

    if ($selectedPriceOrder === 'asc') {
        usort($filteredProducts, function($a, $b) {
            return $a['price'] - $b['price'];
        });
    } elseif ($selectedPriceOrder === 'desc') {
        usort($filteredProducts, function($a, $b) {
            return $b['price'] - $a['price'];
        });
    }
}

// Wishlist logic tetap
if (isset($_POST['add_to_wishlist'])) {
    $productIndex = $_POST['product_id'];
    $productId = $products[$productIndex]['id'];

    $checkSql = "SELECT * FROM wishlist WHERE user_id = ? AND id_produk = ?";
    $checkStmt = $kon->prepare($checkSql);
    $checkStmt->bind_param("ii", $row_user, $productId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        $insertSql = "INSERT INTO wishlist (user_id, id_produk) VALUES (?, ?)";
        $insertStmt = $kon->prepare($insertSql);
        $insertStmt->bind_param("ii", $row_user, $productId);
        $insertStmt->execute();
    }
    header("Location: produk.php?wishlist_success=1");
    exit();
}

// Wishlist saat ini
$wishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];

// Cek notifikasi sukses
$wishlistSuccess = isset($_GET['wishlist_success']);
$cartSuccess = isset($_GET['cart_success']);

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
    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
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
    <div class="pagetitle">
      <h1><i class="bi bi-grid"></i>&nbsp;Produk</h1>
    
    </div>

    <div class="container mt-5">
        <!-- Notifikasi Sukses Wishlist -->
        <?php if ($wishlistSuccess): ?>
            <div class="alert alert-success notification">Produk berhasil ditambahkan ke wishlist!</div>
        <?php endif; ?>
        <!-- Notifikasi Sukses Keranjang -->
        <?php if ($cartSuccess): ?>
            <div class="alert alert-success notification">Produk berhasil ditambahkan ke keranjang!</div>
        <?php endif; ?>

        <!-- Filter Kategori -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $id => $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>"
                                <?= (isset($_POST['category']) && $_POST['category'] == $category) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="filter_category" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Tampilkan Produk -->
        <div class="row">
            <?php if (count($filteredProducts) > 0): ?>
                <?php foreach ($filteredProducts as $index => $product): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <a href="detail_produk.php?product_id=<?= urlencode($product['id']); ?>">
                                <img src="../uploads/<?= $product['image']; ?>" class="card-img-top" alt="Gambar Produk">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?= $product['name']; ?></h5>
                                <p class="card-text"><?= $product['category']; ?></p>
                                <p class="card-text"><strong>Harga: </strong>Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                <?php if ($product['stock'] > 0): ?>
                                    <!-- Form untuk menambah ke keranjang -->
                                    <form method="POST" action="add_to_cart.php" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-success">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="col-md-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada produk ditemukan untuk kategori yang dipilih.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
  </main>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Bootstrap JS -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/index.js"></script>
</body>
</html>