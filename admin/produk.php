<?php
session_start();
include('../db.php'); 
$page = "produk";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Pastikan kategori sudah dimuat di session
if (!isset($_SESSION['categories'])) {
    $_SESSION['categories'] = [];

    $sql = "SELECT id_kategori, nama_kategori FROM kategori";
    $result = mysqli_query($kon, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['categories'][$row['id_kategori']] = $row['nama_kategori'];
    }
}

// Ambil produk dari database setiap kali halaman dimuat
$sql = "SELECT p.*, k.nama_kategori, k.id_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id_kategori"; // Ambil produk dari database
$result = mysqli_query($kon, $sql);
if (!$result) {
    die("Query gagal: " . mysqli_error($kon));
}
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => $row['id_produk'],
        'name' => $row['nama_produk'],
        'category' => $row['nama_kategori'],
        'category_id' => $row['id_kategori'],
        'price' => $row['harga'],
        'stock' => $row['stok'],
        'image' => $row['gambar'],
        'description' => $row['deskripsi']
    ];
}

// Tambahkan debugging
if (empty($products)) {
    error_log("Tidak ada produk yang ditemukan di database");
}

// Simpan produk dalam session
$_SESSION['products'] = $products;

// Inisialisasi filteredProducts dengan semua produk
$filteredProducts = $products;

// Pencarian produk berdasarkan query
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = strtolower($_GET['query']);
    $filteredProducts = array_filter($filteredProducts, function($product) use ($query) {
        return strpos(strtolower($product['name']), $query) !== false;
    });
}

// Filter produk berdasarkan kategori dan harga
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_category'])) {
    $selectedCategory = $_POST['category'] ?? '';
    $selectedPriceOrder = $_POST['price'] ?? '';

    // Filter kategori
    if (!empty($selectedCategory)) {
        $filteredProducts = array_filter($filteredProducts, function($product) use ($selectedCategory) {
            return $product['category'] === $selectedCategory;
        });
    }

    // Urutkan produk berdasarkan harga
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

$cart = $_SESSION['cart'] ?? [];
$wishlist = $_SESSION['wishlist'] ?? [];

// Cek notifikasi sukses atau error
$deleteSuccess = isset($_GET['delete_success']); // Cek apakah ada notifikasi sukses
$deleteError = isset($_GET['delete_error']); // Cek apakah ada notifikasi error
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Product</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .wrapper {
            display: flex }

        .content {
            flex: 1;
            padding-left: 30px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            border-radius: 15px 15px 0 0;
            height: 200px;
            object-fit: cover;
        }   

        .btn-primary, .btn-success, .btn-warning, .btn-danger {
            width: 100%;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script>
    function confirmDelete(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'hapus_produk.php';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_id';
            input.value = productId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmUpdate() {
        return confirm('Apakah Anda yakin ingin mengupdate produk ini?');
    }
    </script>
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
      <li><a href="order.php" ><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <li><a href="promo.php"><span class="tabler--discount"></span>CODE PROMO</a></li>
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
                <li><a href="feedback.php"  class="active"><span class="mdi--feedbacks-outline"></span></a></li>
                <li><a href="index_admin.php"><span class="tdesign--cutomerservice"></span></a></li>
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
                  <span class="d-none d-md-block dropdown-toggle ps-2">
                    <i style="font-size: 20px" class="bi bi-person">
                    </i>&nbsp; Halo, <?= $_SESSION["admin"] ?>!
                  </span>
                  </a>
                </li>
              </ul>
            </div>
        </div>

    <main id="main" class="main-promo">
        <div class="pagetitle">
            <h1>&nbsp; PRODUK</h1>
       
        </div><!-- End Page Title -->

        <!-- Tombol Tambah Produk -->
        <div class="mb-3">
            <a href="tambah_produk.php" class="btn btn-success w-auto">Tambah Produk</a>
        </div>

        <!-- Tampilkan Produk -->
        <div class="row">
            <?php if (count($filteredProducts) > 0): ?>
                <?php foreach ($filteredProducts as $product): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" class="card-img-top" alt="Gambar Produk">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['category']); ?></p>
                                <p class="card-text"><strong>Harga: </strong>Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                <p class="card-text"><strong>Stok: </strong><?= htmlspecialchars($product['stock']); ?> pcs</p>

                                <!-- Detail Produk dan Edit Produk -->
                                <a href="detail_produk.php?product_id=<?= urlencode($product['id']); ?>" class="btn btn-info mb-2">Detail Produk</a>

                                <!-- Tombol Hapus Produk -->
                                <button type="button" onclick="confirmDelete(<?= htmlspecialchars($product['id']); ?>)" class="btn btn-danger">Hapus Produk</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada produk ditemukan untuk kategori atau pencarian yang dipilih.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/chart.js/chart. umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/tinymce/tinymce.min.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>

        <script src="../assets/js/index.js"></script>
</body>
</html>