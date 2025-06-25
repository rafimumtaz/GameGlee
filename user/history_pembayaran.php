<?php
session_start();
include('../db.php'); // Koneksi ke database
$page = "history_pembayaran";

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user']; 

// Tambahkan penanganan filter tanggal dengan validasi
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Validasi rentang tanggal
$dateQuery = "";
$bindTypes = "i";
$bindParams = [&$userId];

if ($start_date && $end_date) {
    // Pastikan start_date tidak lebih besar dari end_date
    if (strtotime($start_date) <= strtotime($end_date)) {
        $dateQuery = " AND p.tanggal_pesanan BETWEEN ? AND ?";
        $bindTypes = "iss";
        $bindParams[] = &$start_date;
        $bindParams[] = &$end_date;
    } else {
        // Jika tanggal tidak valid, reset filter
        $start_date = null;
        $end_date = null;
    }
}

// Perbaikan query dengan filter tanggal
$sql = "SELECT DISTINCT p.id_pesanan, p.total_harga, p.status_pesanan, p.tanggal_pesanan,
        pb.metode_pembayaran, pb.status_pembayaran, 
        pg.alamat_pengiriman, pg.nomor_resi, pg.nama_kurir, pg.tanggal_kirim, pg.perkiraan_tiba,
        pr.nama_produk, pr.harga AS harga_produk, pr.gambar,
        pd.jumlah AS jumlah_produk,
        r.id_pesanan AND r.id_review AS sudah_dinilai
        FROM pesanan_detail pd
        JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
        LEFT JOIN pembayaran pb ON pd.id_pesanan = pb.id_pesanan
        LEFT JOIN pengiriman_pesanan pg ON p.id_pesanan = pg.id_pesanan
        LEFT JOIN produk pr ON pd.id_produk = pr.id_produk
        LEFT JOIN review_produk r ON p.id_user = r.id_user AND pd.id_produk = r.id_produk AND p.id_pesanan = r.id_pesanan
        WHERE p.id_user = ? 
        $dateQuery
        GROUP BY p.id_pesanan
        ORDER BY p.tanggal_pesanan DESC";

// Persiapkan statement
$stmt = mysqli_prepare($kon, $sql);
if (!$stmt) {
    error_log("Prepare statement gagal: " . mysqli_error($kon));
    die("Terjadi kesalahan prepare statement: " . mysqli_error($kon));
}

// Bind parameter dinamis
$refs = [];
foreach ($bindParams as $key => $value) {
    $refs[$key] = &$bindParams[$key];
}

// Gunakan call_user_func_array untuk binding parameter
call_user_func_array(
    [$stmt, 'bind_param'], 
    array_merge([$bindTypes], $refs)
);

// Eksekusi query
if (!mysqli_stmt_execute($stmt)) {
    error_log("Eksekusi query gagal: " . mysqli_stmt_error($stmt));
    die("Terjadi kesalahan eksekusi query: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    error_log("Pengambilan hasil query gagal: " . mysqli_stmt_error($stmt));
    die("Terjadi kesalahan pengambilan hasil: " . mysqli_stmt_error($stmt));
}

$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Tambahkan pengecekan jika tidak ada pesanan
$noOrdersMessage = empty($orders) ? "Tidak ada pesanan dalam rentang tanggal yang dipilih" : null;

// Bersihkan resources
mysqli_stmt_close($stmt);
mysqli_free_result($result);

$pembatalanQuery = "SELECT * FROM pembatalan_pesanan";
$resultPembatalan = mysqli_query($kon, $pembatalanQuery);
$alasanPembatalan = mysqli_fetch_all($resultPembatalan, MYSQLI_ASSOC);

$result_enum = mysqli_query($kon, "SHOW COLUMNS FROM pembatalan_pesanan LIKE 'alasan_pembatalan'");
$row_enum = mysqli_fetch_array($result_enum);

// Ambil daftar nilai ENUM
$enum_values = [];
if ($row_enum) {
    $enum_string = $row_enum['Type']; // e.g., enum('berubah_pikiran','harga_lebih_murah',...)
    preg_match_all("/'([^']+)'/", $enum_string, $matches);
    $enum_values = $matches[1]; // Array dari nilai ENUM
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's History Payment</title>
  <link rel="stylesheet" href="../assets/css/stylePayment.css">
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
    <ul class="sidebar" style="color:#fff;">
      <li><a href="index.php"><span class="humbleicons--dashboard"></span>DASHBOARD</a></li>
      <li><a href="history_pembayaran.php" class="active"><span class="ic--twotone-history"></span>HISTORY PEMBELIAN</a></li>
      <li><a href="pesanan_diproses.php"><span class="hugeicons--package-process"></span>MENUNGGU DIKIRIM</a></li>
      <li><a href="pesanan_dikirim.php"><span class="carbon--delivery-parcel"></span>PESANAN DIKIRIM</a></li>
      <li><a href="pesanan_selesai.php"><span class="mdi--package-variant-closed-check"></span>PESANAN SELESAI</a></li>
      <li><a href="pesanan_dibatalkan.php"><span class="material-symbols--cancel-outline"></span>PESANAN DIBATALKAN</a></li>
      <li><a href="pengeluaran.php"><span class="tabler--report-money"></span>PENGELUARAN USER</a></li>
      <br><br><br><br>
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
            <span class="d-none d-md-block dropdown-toggle">
              <i class="bi bi-person"></i>Halo, <?= $_SESSION["user"] ?>!
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="main-content">
<main id="main" class="main-list">
<link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <div class="pagetitle1">
        <h1>Histori Pembelian</h1>
    </div>
    
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Filter Pesanan</h2>
                        <form method="GET" action="" onsubmit="return validateDateRange()">
                            <div class="input-group mb-3">
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                    placeholder="Tanggal Mulai" 
                                    value="<?= htmlspecialchars($start_date ?? ''); ?>" 
                                    required>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                    placeholder="Tanggal Akhir" 
                                    value="<?= htmlspecialchars($end_date ?? ''); ?>" 
                                    required>
                                <button class="btn btn-primary" type="submit">Filter</button>
                                <?php if ($start_date && $end_date): ?>
                                    <a href="history_pembayaran.php" class="btn btn-secondary">Reset</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Daftar Pesanan</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Detail</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>Status Pesanan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($noOrdersMessage): ?>
                                        <div class="alert alert-info" role="alert">
                                            <?= $noOrdersMessage ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <button class="btn btn-link btn-detail" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapseDetail<?= $order['id_pesanan'] ?>" 
                                                    aria-expanded="false" 
                                                    aria-controls="collapseDetail<?= $order['id_pesanan'] ?>"
                                                    onclick="toggleArrow(this)">
                                                    <span class="ep--arrow-down-bold arrow-icon"></span> <!-- Ikon panah -->
                                                </button>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($order['tanggal_pesanan'])); ?></td>
                                            <td>
                                                <div class="product-container">
                                                    <img src="../uploads/<?= $order['gambar']; ?>" 
                                                         alt="<?= htmlspecialchars($order['nama_produk']); ?>" 
                                                         class="product-img">
                                                    <div class="product-name">
                                                        <?= htmlspecialchars($order['nama_produk']); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($order['jumlah_produk']); ?></td>
                                            <td>Rp <?= number_format($order['harga_produk'], 0, ',', '.'); ?></td>
                                            <td>Rp <?= number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                            <td><?= htmlspecialchars($order['status_pesanan']); ?></td>
                                            <td>
                                                <!-- Selesai Button -->
                                                <?php if ($order['status_pesanan'] !== 'Selesai' AND $order['status_pesanan'] !== 'Dibatalkan' AND $order['status_pesanan'] !== 'Diproses'): ?>
                                                    <button class="btn btn-warning btn-sm selesai-btn" 
                                                            data-id="<?= $order['id_pesanan']; ?>">Selesai</button>
                                                <?php endif; ?>
                                                
                                                <!-- Nilai Button -->
                                                <?php if ($order['status_pesanan'] === 'Selesai' && empty($order['sudah_dinilai'])): ?>
                                                    <a href="review.php?id=<?= $order['id_pesanan'] ?>" 
                                                       class="btn btn-primary btn-sm">Nilai</a>
                                                <?php endif; ?>

                                                <!-- Beli Lagi Button -->
                                                <?php if ($order['status_pesanan'] === 'Selesai' && !empty($order['sudah_dinilai'])): ?>
                                                    <form method="POST" action="checkout.php">
                                                        <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                                                        <button type="submit" name="buy_now" class="btn btn-success">&nbsp;Beli Lagi</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <!-- Lacak Button -->
                                                <?php if ($order['status_pesanan'] !== 'Selesai' AND $order['status_pesanan'] !== 'Dibatalkan' AND $order['status_pesanan'] !== 'Diproses'): ?>
                                                    <a href="lacak.php?id=<?= $order['id_pesanan'] ?>" 
                                                       class="btn btn-info btn-sm">Lacak</a>
                                                <?php endif; ?>

                                                <!-- Tombol Batalkan -->
                                                <?php if ($order['status_pesanan'] === 'Diproses'): ?>
                                                   <a href="form_pembatalan.php?id_pesanan=<?= urlencode($order['id_pesanan']); ?>"class="btn btn-danger btn-sm batalkan-btn" >Batalkan</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="collapseDetail<?= $order['id_pesanan'] ?>">
                                            <td colspan="9">
                                                <div class="p-3">
                                                    <h6>Informasi Pembayaran</h6>
                                                    <p>
                                                        <strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['metode_pembayaran']); ?><br>
                                                        <strong>Status Pembayaran:</strong> <?= htmlspecialchars($order['status_pembayaran']); ?>
                                                    </p>
                                                    <h6>Informasi Pengiriman</h6>
                                                    <p>
                                                        <strong>Alamat:</strong> <?= htmlspecialchars($order['alamat_pengiriman'] ?? 'Tidak tersedia'); ?><br>
                                                        <strong>Nomor Resi:</strong> <?= htmlspecialchars($order['nomor_resi'] ?? 'Tidak tersedia'); ?><br>
                                                        <strong>Ekspedisi:</strong> <?= htmlspecialchars($order['nama_kurir'] ?? 'Tidak tersedia'); ?><br>
                                                        <strong>Tanggal Kirim:</strong> <?= htmlspecialchars($order['tanggal_kirim'] ?? 'Tidak tersedia'); ?><br>
                                                        <strong>Perkiraan Tiba:</strong> <?= htmlspecialchars($order['perkiraan_tiba'] ?? 'Tidak tersedia'); ?><br>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
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


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailButtons = document.querySelectorAll('.btn-detail');

            detailButtons.forEach(button => {
                const targetId = button.getAttribute('data-bs-target');
                const targetCollapse = document.querySelector(targetId);

                // Tambahkan event listener untuk menunjukkan/menyembunyikan
                targetCollapse.addEventListener('show.bs.collapse', function () {
                    button.classList.add('rotate'); // Rotate saat buka
                });

                targetCollapse.addEventListener('hide.bs.collapse', function () {
                    button.classList.remove('rotate'); // Kembalikan rotasi saat tutup
                });

                // Pastikan satu tombol hanya mengontrol satu collapse
                button.addEventListener('click', function () {
                    const isCollapsed = targetCollapse.classList.contains('show');
                    detailButtons.forEach(btn => {
                        const collapse = document.querySelector(btn.getAttribute('data-bs-target'));
                        if (collapse !== targetCollapse && collapse.classList.contains('show')) {
                            collapse.classList.remove('show'); // Tutup semua collapse lain
                            btn.classList.remove('rotate');
                        }
                    });
                    if (isCollapsed) {
                        targetCollapse.classList.remove('show'); // Tutup target jika sedang terbuka
                        button.classList.remove('rotate');
                    } else {
                        targetCollapse.classList.add('show'); // Buka target jika tertutup
                        button.classList.add('rotate');
                    }
                });
            });
        });

    </script>
    
    <script>
    function validateDateRange() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);
        
        if (startDate > endDate) {
            alert('Tanggal mulai harus sebelum atau sama dengan tanggal akhir');
            return false;
        }
        return true;
    }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selesaiButtons = document.querySelectorAll('.selesai-btn');

            selesaiButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const pesananId = this.getAttribute('data-id');

                    if (confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?')) {
                        fetch('update_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_pesanan=${pesananId}&status=selesai`,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Pesanan berhasil diselesaikan!');
                                location.reload(); // Reload to reflect changes
                            } else {
                                alert('Gagal menyelesaikan pesanan. Silakan coba lagi.');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tangkap klik button Batalkan
            document.querySelectorAll('.batalkan-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const pesananId = this.getAttribute('data-id');
                    document.getElementById('id_pesanan').value = pesananId;
                    const batalkanModal = new bootstrap.Modal(document.getElementById('batalkanModal'));
                    batalkanModal.show();
                });
            });
        
            // Form Submit untuk pembatalan
            document.getElementById('formPembatalan').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
            
                fetch('proses_pembatalan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pesanan berhasil dibatalkan!');
                        location.reload();
                    } else {
                        alert('Gagal membatalkan pesanan. Silakan coba lagi.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
            });
        });
    </script>
    <script>
    function toggleArrow(button) {
        var icon = button.querySelector('.arrow-icon');
        var collapseTarget = document.querySelector(button.getAttribute('data-bs-target'));
        
        if (collapseTarget.classList.contains('show')) {
            // Collapse sedang terbuka, ubah ikon ke bawah
            icon.style.transform = 'rotate(0deg)';
        } else {
            // Collapse sedang tertutup, ubah ikon ke atas
            icon.style.transform = 'rotate(180deg)';
        }
    }
    document.querySelector('.toggle-arrow').addEventListener('click', function() {
    var collapseDetail = document.querySelector('.collapse-detail');
    
    // Toggle visibilitas kolom collapse
    if (collapseDetail.style.display === 'none' || collapseDetail.style.display === '') {
        collapseDetail.style.display = 'block'; // Menampilkan kolom
    } else {
        collapseDetail.style.display = 'none'; // Menyembunyikan kolom
    }
});

</script>
  <script src="../assets/js/index.js"></script>
</body>
</html>