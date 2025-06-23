<?php
session_start();
include('../db.php'); // Koneksi ke database

// Pastikan admin login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// Inisialisasi pesan
$successMessage = '';
$errorMessage = '';

// Proses pengiriman balasan admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_review = isset($_POST['id_review']) ? intval($_POST['id_review']) : 0;
    $response = isset($_POST['response']) ? trim($_POST['response']) : '';

    // Validasi input
    if ($id_review <= 0 || empty($response)) {
        $errorMessage = "ID review atau balasan tidak valid.";
    } else {
        // Simpan balasan admin ke database
        $sql = "UPDATE review_produk SET komentar_admin = ? WHERE id_review = ?";
        $stmt = mysqli_prepare($kon, $sql);
        if ($stmt) { // Cek apakah persiapan berhasil
            mysqli_stmt_bind_param($stmt, "si", $response, $id_review);

            if (mysqli_stmt_execute($stmt)) {
                $successMessage = "Balasan berhasil ditambahkan.";
            } else {
                $errorMessage = "Terjadi kesalahan saat menambahkan balasan: " . mysqli_error($kon);
            }

            mysqli_stmt_close($stmt);
        } else {
            $errorMessage = "Gagal mempersiapkan pernyataan: " . mysqli_error($kon);
        }
    }
}

// Ambil semua ulasan dari database
$sql = "SELECT rp.id_review, rp.id_produk, rp.rating, rp.komentar, rp.tanggal_review, rp.komentar_admin, 
        p.nama_produk, p.gambar, u.nama
        FROM review_produk rp
        JOIN produk p ON rp.id_produk = p.id_produk
        JOIN user u ON rp.id_user = u.id_user
        ORDER BY rp.tanggal_review DESC";
$result = mysqli_query($kon, $sql);
if ($result) {
    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    $errorMessage = "Gagal mengambil data ulasan: " . mysqli_error($kon);
    $reviews = []; // Pastikan $reviews terdefinisi sebagai array kosong
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Review</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <!-- Google Fonts -->
   <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <style>
        /* Gaya Umum */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Gaya Tabel */
        .table {
            background: #fff;
            border-radius: 4px;
            margin: 20px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: #e9ecef;
            color: #333;
            padding: 10px;
            text-align: left;
        }

        .table td {
            padding: 10px;
            vertical-align: middle;
            background: #fff;
            border-bottom: 1px solid #ddd;
        }

        .table tr:hover td {
            background: #f1f1f1;
        }

        /* Gaya Gambar Produk */
        .product-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-img {
            width: 60px; /* Ukuran gambar */
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-name {
            font-weight: normal;
            color: #333;
            font-size: 0.9rem; /* Ukuran font nama produk */
        }

        /* Gaya Tombol */
        .btn {
            border-radius: 4px;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        /* Gaya Modal */
        .modal-content {
            border-radius: 8px; /* Sudut lembut */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
        }

        .modal-header {
            background-color: #007bff; /* Warna latar belakang header */
            color: #fff; /* Warna teks header */
            border-top-left-radius: 8px; /* Sudut lembut */
            border-top-right-radius: 8px; /* Sudut lembut */
        }

        .modal-footer {
            border-bottom-left-radius: 8px; /* Sudut lembut */
            border-bottom-right-radius: 8px; /* Sudut lembut */
        }

        .modal.fade .modal-dialog {
            transition: transform 0.2s ease, opacity 0.2s ease; /* Transisi halus */
        }

        .modal.fade.show .modal-dialog {
            transform: translate(0, 0); /* Posisi normal saat modal muncul */
            opacity: 1; /* Transparansi penuh saat modal muncul */
        }

        .modal.fade .modal-dialog {
            transform: translate(0, -20px); /* Posisi awal saat modal muncul */
            opacity: 0; /* Transparansi awal */
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
      <li><a href="index.php" ><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="user.php"><span class="ph--user-list-bold"></span>DAFTAR USER</a></li>
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
      <li><a href="stok_produk.php"><span class="lsicon--management-stockout-outline"></span>STOK PRODUK TERSEDIA</a></li>
      <li><a href="review.php" class="active"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
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
                <li><a href="feedback.php"><span class="mdi--feedbacks-outline"></span></a></li>
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
            <h1><i class="bi bi-chat-dots"></i> Ulasan Produk</h1>
           
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Daftar Ulasan</h3>

                            <!-- Pesan sukses atau error -->
                            <?php if ($successMessage): ?>
                                <div class="alert alert-success"><?= $successMessage; ?></div>
                            <?php elseif ($errorMessage): ?>
                                <div class="alert alert-danger"><?= $errorMessage; ?></div>
                            <?php endif; ?>

                            <!-- Tabel Ulasan -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Produk</th>
                                            <th>Nama User</th>
                                            <th>Rating</th>
                                            <th>Ulasan</th>
                                            <th>Balasan Admin</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reviews as $index => $review): ?>
                                            <tr>
                                                <td><?= $index + 1; ?></td>
                                                <td>
                                                    <div class="product-container">
                                                        <img src="../uploads/<?= htmlspecialchars($review['gambar']); ?>" 
                                                             alt="<?= htmlspecialchars($review['nama_produk']); ?>" 
                                                             class="product-img">
                                                        <div class="product-name">
                                                            <?= htmlspecialchars($review['nama_produk']); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($review['nama']); ?></td>
                                                <td><?= htmlspecialchars($review['rating']); ?></td>
                                                <td><?= htmlspecialchars($review['komentar']); ?></td>
                                                <td>
                                                    <?php if (empty($review['komentar_admin'])): ?>
                                                        Belum ada balasan
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($review['komentar_admin']); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($review['tanggal_review'])); ?></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#responseModal<?= $review['id_review']; ?>">
                                                        Balas
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal Balasan -->
                                            <div class="modal fade" id="responseModal<?= $review['id_review']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Balas Ulasan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id_review" value="<?= $review['id_review']; ?>">
                                                                <div class="mb-3">
                                                                    <label for="response" class="form-label">Balasan Admin</label>
                                                                    <textarea name="response" id="response" class="form-control" rows="5" required><?= htmlspecialchars($review['komentar_admin']); ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Kirim</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.min.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>
        <script src="../assets/js/index.js"></script>
</body>
</html>