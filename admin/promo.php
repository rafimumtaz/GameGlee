<?php
session_start();
require "../db.php";
$page = "promo";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to generate a promo code
function generatePromoCode($length = 5) {
    return strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length));
}

// Handle promo code generation
if (isset($_POST['generate_promo'])) {
    $promo_code = generatePromoCode();
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $usage_limit = intval($_POST['usage_limit']);

    // Validate input
    if ($discount_value <= 0 || $usage_limit <= 0) {
        echo "<script>alert('Invalid discount value or usage limit.');</script>";
    } else {
        $stmt = $kon->prepare("INSERT INTO promo (code, discount_type, discount_value, usage_limit) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $promo_code, $discount_type, $discount_value, $usage_limit);
        
        if ($stmt->execute()) {
            echo "<script>alert('Promo code successfully created: $promo_code');</script>";
        } else {
            echo "<script>alert('Failed to save promo code: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Code Promo</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
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
      <li><a href="review.php"><span class="uil--comment-alt-edit"></span>REVIEW BARANG YANG SUDAH DIBELI</a></li>
      <li><a href="promo.php" class="active"><span class="tabler--discount"></span>CODE PROMO</a></li>
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
            <h1><i class="bi bi-percent"></i>&nbsp; PROMO DISCOUNT</h1>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mt-5">Generate Promo Code</h6>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="discount_type" class="form-label-1">Discount Type</label>
                                    <select class="form-select" id="discount_type" name="discount_type" required>
                                        <option value="fixed"><p>Fixed Amount</p></option>
                                        <option value="percentage"><p>Percentage</p></option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="discount_value" class="form-label-1">Discount Value</label>
                                    <input type="number" class="form-control" id="discount_value" name="discount_value" required>
                                </div>
                                <div class="mb-3">
                                    <label for="usage_limit" class="form-label-1">Usage Limit</label>
                                    <input type="number" class="form-control" id="usage_limit" name="usage_limit" required>
                                </div>
                                <button type="submit" name="generate_promo" class="btn btn-primary">Generate Promo Code</button>
                            </form>

                            <h6 class="card-title mt-5">Available Promo Codes</h6>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Promo Code</th>
                                        <th>Discount Type</th>
                                        <th>Discount Value</th>
                                        <th>Usage Limit</th>
                                        <th>Times Used</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $promo_query = "SELECT * FROM promo";
                                    $promo_result = $kon->query($promo_query);
                                    while ($promo_row = $promo_result->fetch_assoc()) {
                                        $status = $promo_row['times_used'] >= $promo_row['usage_limit'] ? 'Expired' : 'Available';
                                        $discount_value = $promo_row['discount_type'] == 'fixed' ? 'Rp. ' . number_format($promo_row['discount_value'], 0, ',', '.') : $promo_row['discount_value'] . '%';

                                        echo "<tr>";
                                        echo "<td>{$promo_row['code']}</td>";
                                        echo "<td>" . ($promo_row['discount_type'] == 'fixed' ? 'Fixed Amount' : 'Percentage') . "</td>";
                                        echo "<td>$discount_value</td>";
                                        echo "<td>{$promo_row['usage_limit']}</td>";
                                        echo "<td>{$promo_row['times_used']}</td>";
                                        echo "<td>$status</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

        <script src="../assets/js/index.js"></script>
</body>
</html>