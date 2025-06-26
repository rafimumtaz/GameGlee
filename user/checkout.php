<?php 
session_start();
include('../db.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!empty($_POST['selected_items'])) {
        $_SESSION['checkout_cart_ids'] = $_POST['selected_items'];
        header("Location: checkout.php");
        exit();
    } else {
        header("Location: add_to_cart.php?error=no_items_selected");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['ulang'])) {
    $is_redirected_from_cart = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'add_to_cart.php') !== false;

    if (!$is_redirected_from_cart) {
        unset($_SESSION['promo_applied']);
        unset($_SESSION['discount_details']);
        unset($_SESSION['promo_code_applied']);
    }
}


if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_session_name = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user_session_name'");
$row_user = mysqli_fetch_array($kue_user);

if (!$row_user) {
    die("User not found.");
}

$userId = $row_user['id_user'];

if (isset($_POST['apply_promo'])) {
    header('Content-Type: application/json');
    $promoCode = mysqli_real_escape_string($kon, $_POST['promo_code']);
    $subtotal_promo = 0;

    if (isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart'])) {
        foreach ($_SESSION['temp_cart'] as $item) {
            $subtotal_promo += $item['harga'] * $item['jumlah'];
        }
    } else {
        echo json_encode(['error' => 'Tidak ada item untuk diterapkan promo.']);
        exit();
    }

    try {
        if (empty($promoCode)) {
            unset($_SESSION['promo_applied'], $_SESSION['discount_details'], $_SESSION['promo_code_applied']);
            echo json_encode(['success' => true, 'reset' => true, 'message' => 'Promo dihapus.']);
            exit();
        }

        $promoQuery = "SELECT * FROM promo WHERE code = '$promoCode'";
        $promoResult = mysqli_query($kon, $promoQuery);

        if (!$promoResult || mysqli_num_rows($promoResult) <= 0) {
            throw new Exception("Kode promo tidak valid atau sudah kedaluwarsa.");
        }

        $promoData = mysqli_fetch_assoc($promoResult);
        if ($promoData['usage_limit'] != null && $promoData['usage_limit'] <= 0) {
            throw new Exception("Kode promo telah mencapai batas penggunaan.");
        }

        $discount_type = $promoData['discount_type'];
        $discount_value = $promoData['discount_value'];
        $calculatedDiscount = 0;

        if ($discount_type == 'percentage') {
            $calculatedDiscount = $subtotal_promo * ($discount_value / 100);
        } else {
            $calculatedDiscount = $discount_value;
        }

        $effectiveDiscount = min($calculatedDiscount, $subtotal_promo);

        $_SESSION['promo_applied'] = true;
        $_SESSION['promo_code_applied'] = $promoCode;
        $_SESSION['discount_details'] = [
            'code' => $promoCode,
            'type' => $discount_type,
            'value' => $discount_value,
            'calculated_discount' => $effectiveDiscount
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Promo berhasil diterapkan!',
            'discount' => $effectiveDiscount
        ]);

    } catch (Exception $e) {
        unset($_SESSION['promo_applied'], $_SESSION['discount_details'], $_SESSION['promo_code_applied']);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}


$checkoutItems = [];
$error = null;
$itemIds = [];
if (isset($_GET['ulang'])) {
    $ulangId = intval($_GET['ulang']);
    $sql = "SELECT pd.id_produk, pd.jumlah, pr.nama_produk, pr.harga, pr.gambar, pr.stok FROM pesanan_detail pd JOIN produk pr ON pd.id_produk = pr.id_produk WHERE pd.id_pesanan = $ulangId";
    $result = mysqli_query($kon, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) $checkoutItems[] = $row;
        $_SESSION['temp_cart'] = $checkoutItems;
    } else {
        $error = "Tidak dapat menemukan produk dari pesanan sebelumnya.";
    }
} elseif (isset($_SESSION['checkout_cart_ids']) && is_array($_SESSION['checkout_cart_ids'])) {
    $cartIds = array_map('intval', $_SESSION['checkout_cart_ids']);
    if (!empty($cartIds)){
        $cartIdsStr = implode(',', $cartIds);
        $sql = "SELECT k.id_keranjang, k.id_produk, k.jumlah, p.nama_produk, p.harga, p.gambar, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.id_keranjang IN ($cartIdsStr)";
        $result = mysqli_query($kon, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $checkoutItems[] = $row;
                $itemIds[] = $row['id_keranjang'];
            }
            $_SESSION['temp_cart'] = $checkoutItems;
        } else {
            $error = "Tidak dapat menemukan produk dari keranjang.";
        }
    } else {
         $error = "Tidak ada item yang dipilih dari keranjang.";
    }
} elseif (isset($_POST['buy_now'])) {
    $productId = intval($_POST['product_id']);
    $productQuery = "SELECT id_produk, nama_produk, harga, gambar, stok FROM produk WHERE id_produk = '$productId'";
    $result = mysqli_query($kon, $productQuery);
    if ($result && $product = mysqli_fetch_assoc($result)) {
        $product['jumlah'] = 1;
        $checkoutItems[] = $product;
        $_SESSION['temp_cart'] = $checkoutItems;
    } else {
        die("Produk tidak ditemukan.");
    }
} elseif (isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart'])) {
    $checkoutItems = $_SESSION['temp_cart'];
    if (isset($_SESSION['checkout_cart_ids'])) {
       $itemIds = $_SESSION['checkout_cart_ids'];
    }
} else {
    $error = "Tidak ada item yang diproses untuk checkout.";
}

// Perhitungan harga awal
$subtotal = 0;
foreach ($checkoutItems as $item) {
    $subtotal += $item['harga'] * $item['jumlah'];
}
$biaya_kirim = ceil($subtotal * 0.10);
$grandTotal = $subtotal + $biaya_kirim;


if (isset($_POST['confirm_order'])) {
    if (empty($checkoutItems)) {
        $error = "Sesi checkout berakhir. Silakan ulangi.";
    } else {
        $name = mysqli_real_escape_string($kon, $_POST['name']);
        $address = mysqli_real_escape_string($kon, $_POST['address']);
        $phone = mysqli_real_escape_string($kon, $_POST['phone']);
        $postal_code = mysqli_real_escape_string($kon, $_POST['postal_code']);
        $payment_method = mysqli_real_escape_string($kon, $_POST['payment_method']);

        $final_biaya_kirim = ceil($subtotal * 0.10);
        $kurir_list = array("JNE", "J&T", "SiCepat", "Pos Indonesia");
        $kurir_terpilih = $kurir_list[array_rand($kurir_list)];

        mysqli_begin_transaction($kon);
        try {
            $final_subtotal = 0;
            foreach ($checkoutItems as $item) {
                $final_subtotal += $item['harga'] * $item['jumlah'];
            }

            $final_discount = 0;
            if (isset($_SESSION['promo_applied']) && $_SESSION['promo_applied'] && isset($_SESSION['discount_details'])) {
                $final_discount = $_SESSION['discount_details']['calculated_discount'];
            }

            $final_grand_total = ($final_subtotal + $final_biaya_kirim) - $final_discount;

            $insertOrder = "INSERT INTO pesanan (id_user, total_harga, status_pesanan, tanggal_pesanan) VALUES ('$userId', '$final_grand_total', 'Diproses', NOW())";
            if (!mysqli_query($kon, $insertOrder)) throw new Exception('Error inserting order: ' . mysqli_error($kon));
            $orderId = mysqli_insert_id($kon);

            foreach ($checkoutItems as $item) {
                if ($item['stok'] < $item['jumlah']) throw new Exception("Stok produk '{$item['nama_produk']}' tidak mencukupi.");
                
                $insertDetail = "INSERT INTO pesanan_detail (id_pesanan, id_produk, jumlah, subtotal) VALUES ('$orderId', '{$item['id_produk']}', '{$item['jumlah']}', '".($item['harga'] * $item['jumlah'])."')";
                if (!mysqli_query($kon, $insertDetail)) throw new Exception('Error inserting order detail: ' . mysqli_error($kon));

                $updateStock = "UPDATE produk SET stok = stok - {$item['jumlah']} WHERE id_produk = '{$item['id_produk']}'";
                if (!mysqli_query($kon, $updateStock)) throw new Exception('Error updating stock: ' . mysqli_error($kon));
            }

            $insertPayment = "INSERT INTO pembayaran (id_pesanan, metode_pembayaran, status_pembayaran, tanggal_pembayaran) VALUES ('$orderId', '$payment_method', 'Dibayar', NOW())";
            if (!mysqli_query($kon, $insertPayment)) throw new Exception('Error inserting payment: ' . mysqli_error($kon));

            $nomor_resi = 'RSI' . date('YmdHis') . rand(100, 999);
            $insertShipping = "INSERT INTO pengiriman_pesanan (id_pesanan, id_user, nomor_resi, nama_kurir, alamat_pengiriman, tanggal_kirim, perkiraan_tiba, status_pengiriman, biaya_kirim) VALUES ('$orderId', '$userId', '$nomor_resi', '$kurir_terpilih', '$address', NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), 'dalam_pengiriman', '$final_biaya_kirim')";
            if (!mysqli_query($kon, $insertShipping)) throw new Exception('Error inserting shipping: ' . mysqli_error($kon));

            if (!empty($itemIds)) {
                $cartIdsToDeleteStr = implode(',', array_map('intval', $itemIds));
                $deleteCart = "DELETE FROM keranjang WHERE id_keranjang IN ($cartIdsToDeleteStr)";
                if (!mysqli_query($kon, $deleteCart)) throw new Exception('Error deleting cart items: ' . mysqli_error($kon));
            }

            if (isset($_SESSION['promo_code_applied'])) {
                $usedPromoCode = $_SESSION['promo_code_applied'];
                $updatePromoQuery = "UPDATE promo SET usage_limit = usage_limit - 1, times_used = times_used + 1 WHERE code = '$usedPromoCode' AND usage_limit > 0";
                mysqli_query($kon, $updatePromoQuery);
            }

            mysqli_commit($kon);
            unset($_SESSION['temp_cart'], $_SESSION['checkout_cart_ids'], $_SESSION['promo_applied'], $_SESSION['discount_details'], $_SESSION['promo_code_applied']);

            header("Location: history_pembayaran.php?order_success=true");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($kon);
            $error = "Transaksi Gagal: " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Checkout</title>
    <!-- Favicons -->
    <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Template Main CSS File -->
    <link href="../assets/css/styleProduk.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS untuk checkout page -->
    <style>
        
        .product-item img { width: 70px; height: 70px; object-fit: cover; border-radius: 0.375rem; }
        #summary-card { position: sticky; top: 80px; }
        .summary-card .list-group-item { border: none; padding-left: 0; padding-right: 0; }
        .payment-method .form-check-label { width: 100%; border: 1px solid #dee2e6; padding: 1rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s ease-in-out; }
        .payment-method .form-check-input:checked + .form-check-label { border-color: #f87117; background-color:rgb(255, 230, 176); box-shadow: 0 0 0 2px rgba(232, 153, 44, 0.25); }
        .payment-method .form-check-input { display: none; }
        #promo-feedback { font-size: 0.875em; }
        .btn-primary{
            background-color: #f87117 !important;
            border: hidden;
        }
        a.back{
        text-decoration: none;
        color: black;
        background-color:rgba(0, 0, 0, 0.1);
        padding: 10px 10px;
        border: 1px solid transparent;
        border-radius: 10px;
        margin-top: 20px;
        margin-right: 10px;
    }
    label.form-check-label{
        color: black !important;
    }
    h5.card-title{
        color: black !important;
    }
        main{
            margin-top: 100px !important;
            margin-left: 50px !important;
        }
        h1{
            font-family: "slackey", sans-serif;
            font-size: 24px !important;
        }
        .sidebar {
            margin-top: 15px;
            position: relative;
            top: 0;
            left: 0;
            width: 260px;
            height: auto;
            background: transparent !important;
            background-color: transparent !important;
            padding: 10px 14px;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            letter-spacing: 2px;
        }
        span.text-primary{
            color:rgb(255, 120, 57) !important;
        }
        .btn-outline-primary{
            border: 1px solid #f89317;
            color: #f89317 !important;
        }
        .btn-outline-primary:hover{
            border: 1px solid transparent;
            color: #ffffff !important;
            background-color: #f89317 !important;
        }
        div.fw-bold{
            font-size: 14px !important;
        }
        h5{
            font-size: 16px !important;
            font-weight: bold !important;
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
          <li><a href="add_to_cart.php" class="active"><span class="solar--cart-outline"></span></a></li>
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
    <main id="main" class="main">
        
        <div class="pagetitle">
            <h1><i class="bi bi-cart"></i>&nbsp; PEMBAYARAN</h1>
            
        </div>
        <a href="add_to_cart.php" class="back">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>

        <section class="section">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (empty($checkoutItems) && empty($error)): ?>
                 <div class="alert alert-warning">Tidak ada item untuk di-checkout. <a href="produk.php">Kembali belanja</a>.</div>
            <?php else: ?>
                <form method="POST" action="checkout.php">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card mb-4">
                                <div class="card-body pt-4">
                                    <h5 class="card-title p-0 m-0 mb-3"><i class="bi bi-person-circle me-2"></i>Data Penerima</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label for="name" class="form-label">Nama Penerima</label><input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($row_user['nama']) ?>" required></div>
                                        <div class="col-md-6 mb-3"><label for="phone" class="form-label">Nomor Telepon</label><input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($row_user['no_tlp']) ?>" required></div>
                                        <div class="col-12 mb-3"><label for="address" class="form-label">Alamat Lengkap</label><textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($row_user['alamat']) ?></textarea></div>
                                        <div class="col-md-6 mb-3"><label for="postal_code" class="form-label">Kode Pos</label><input type="text" class="form-control" id="postal_code" name="postal_code" value="<?= htmlspecialchars($row_user['kode_pos'] ?? '') ?>" required></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-body pt-4">
                                    <h5 class="card-title p-0 m-0 mb-3"><i class="bi bi-credit-card-fill me-2"></i>Metode Pembayaran</h5>
                                    <div class="payment-method">
                                        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD"><label class="form-check-label d-flex justify-content-between align-items-center" for="cod"><span><i class="bi bi-truck me-2"></i>Cash on Delivery (COD)</span></label></div>
                                        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="payment_method" id="transferBank" value="Transfer Bank" checked><label class="form-check-label d-flex justify-content-between align-items-center" for="transferBank"><span><i class="bi bi-bank me-2"></i>Transfer Bank</span></label></div>
                                        <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" id="kartuKredit" value="Kartu Kredit"><label class="form-check-label d-flex justify-content-between align-items-center" for="kartuKredit"><span><i class="bi bi-credit-card-2-front-fill me-2"></i>Kartu Kredit</span></label></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card" id="summary-card">
                                <div class="card-body pt-4">
                                    <h5 class="card-title p-0 m-0 mb-3"><i class="bi bi-journal-text me-2"></i>Ringkasan Pesanan</h5>
                                    <div class="mb-3">
                                        <?php foreach ($checkoutItems as $item): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    <img src="../uploads/<?= htmlspecialchars($item['gambar']); ?>" class="me-2" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($item['nama_produk']); ?></div>
                                                        <small class="text-muted"><?= $item['jumlah']; ?> x <?= formatCurrency($item['harga']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="fw-bold"><?= formatCurrency($item['harga'] * $item['jumlah']); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="promo-code" placeholder="Masukkan kode promo">
                                            <button class="btn btn-outline-primary" type="button" id="apply-promo-btn">Pakai</button>
                                        </div>
                                        <div id="promo-feedback" class="mt-1"></div>
                                    </div>
                                    <hr>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between"><span>Subtotal</span><span id="summary-subtotal"><?= formatCurrency($subtotal); ?></span></li>
                                        <li class="list-group-item d-flex justify-content-between"><span>Ongkos Kirim</span><span id="summary-ongkir"><?= formatCurrency($biaya_kirim); ?></span></li>
                                        <li class="list-group-item d-flex justify-content-between text-success" id="summary-diskon-row" style="display: none;"><span>Diskon</span><span id="summary-diskon">- Rp 0</span></li>
                                        <li class="list-group-item d-flex justify-content-between fs-5 fw-bold border-top pt-3"><span>Total</span><span class="text-primary" id="summary-grand-total"><?= formatCurrency($grandTotal); ?></span></li>
                                    </ul>
                                    <div class="d-grid mt-4">
                                        <button href="pesanan_diproses.php" type="submit" name="confirm_order" class="btn btn-primary btn-lg">Konfirmasi Pesanan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>          
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const subtotal = <?= (float)$subtotal; ?>;
            const ongkir = <?= (float)$biaya_kirim; ?>;
        
            const promoInput = $('#promo-code');
            const promoFeedback = $('#promo-feedback');
            const diskonRow = $('#summary-diskon-row');
            const diskonEl = $('#summary-diskon');
            const grandTotalEl = $('#summary-grand-total');
        
            function formatCurrency(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
            }
        
            function updateSummary(diskonAmount = 0) {
                const diskon = parseFloat(diskonAmount) || 0;
                if (diskon > 0) {
                    diskonEl.text('- ' + formatCurrency(diskon));
                    diskonRow.css('display', 'flex');
                } else {
                    diskonRow.css('display', 'none');
                }
                const grandTotal = (subtotal + ongkir) - diskon;
                grandTotalEl.text(formatCurrency(grandTotal > 0 ? grandTotal : 0));
            }
        
            $('#apply-promo-btn').on('click', function() {
                const kodePromo = promoInput.val().trim();
                promoFeedback.text('').removeClass('text-success text-danger');
            
                $.ajax({
                    type: 'POST',
                    url: 'checkout.php',
                    data: {
                        apply_promo: true,
                        promo_code: kodePromo
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            promoFeedback.text(data.message).addClass('text-success');
                            if (data.reset) {
                                updateSummary(0);
                            } else {
                                updateSummary(data.discount);
                            }
                        } else {
                            promoFeedback.text(data.error).addClass('text-danger');
                            updateSummary(0);
                        }
                    },
                    error: function(xhr, status, error) {
                        promoFeedback.text('Terjadi kesalahan. Cek konsol browser.').addClass('text-danger');
                        console.error("AJAX Error:", status, error, xhr.responseText);
                    }
                });
            });
        });

        <?php
            function formatCurrency($number) {
                return 'Rp ' . number_format($number, 0, ',', '.');
            }
        ?>
    </script>
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
        <script src="../assets/js/index.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>