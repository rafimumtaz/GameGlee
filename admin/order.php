<?php
session_start();
$page = "order";
if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

$host = 'localhost';
$dbname = 'gamify';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Filter status jika ada
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Query untuk menampilkan daftar pesanan dengan join tabel user dan produk
$query = "SELECT p.id_pesanan, u.nama AS customer_name, pr.nama_produk, 
                 p.tanggal_pesanan, p.total_harga, p.status_pesanan
          FROM pesanan_detail pd
            JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
          JOIN user u ON p.id_user = u.id_user
          JOIN produk pr ON pd.id_produk = pr.id_produk";

if (!empty($status_filter)) {
    $query .= " WHERE p.status_pesanan = :status";
}
$query .= " ORDER BY p.tanggal_pesanan DESC";

$stmt = $pdo->prepare($query);

if (!empty($status_filter)) {
    $stmt->bindParam(':status', $status_filter);
}
$stmt->execute();
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Order</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
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
      <li><a href="informasipromo.php"><span class="tabler--discount"></span>INFORMASI PROMO</a></li>
      <li><a href="penjualan.php"><span class="icon-park-outline--sales-report"></span>TOTAL PENJUALAN</a></li>
      <li><a href="order.php" class="active"><span class="lsicon--work-order-abnormal-outline"></span>ORDER MASUK</a></li>
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
    <div class="container">
        <div class="row">
            <div class="col">
                <h1><i class="bi bi-grid"></i>&nbsp; Daftar Pesanan</h1>
            </div>
        </div>

        <!-- Filter Status -->
        <form method="GET" class="mb-3">
            <label for="status" class="form-label">Filter Status:</label>
            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="Diproses" <?= $status_filter == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="Dikirim" <?= $status_filter == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                <option value="Dibatalkan" <?= $status_filter == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
        </form>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><p>Nama Pembeli</p></th>
                            <th><p>Produk</p></th>
                            <th><p>Tanggal</p></th>
                            <th><p>Total</p></th>
                            <th><p>Status</p></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($pesanan)) {
                            foreach ($pesanan as $index => $order) {
                                if($order['status_pesanan'] !== "Selesai"){
                                ?>
                                
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['nama_produk']) ?></td>
                                    <td><?= $order['tanggal_pesanan'] ?></td>
                                    <td>Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <select class="form-select status-dropdown" 
                                                data-id="<?= $order['id_pesanan'] ?>">
                                            <option value="Diproses" <?= $order['status_pesanan'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                            <option value="Dikirim" <?= $order['status_pesanan'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                            <option value="Dibatalkan" <?= $order['status_pesanan'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                                }
                            } 
                        }else{
                            echo "<tr><td colspan='6'>Tidak ada data pesanan.</td></tr>";
                        }
                        ?>
                        
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusDropdowns = document.querySelectorAll('.status-dropdown');
    
    statusDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function () {
            const id = this.getAttribute('data-id');
            const newStatus = this.value;

            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_pesanan=${id}&status_pesanan=${newStatus}`
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    location.reload();
                } else {
                    alert('Gagal memperbarui status.');
                }
            });
        });
    });
});
</script>
        <script src="../assets/js/index.js"></script>
</body>
</html>