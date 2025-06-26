<?php
session_start();
include('../db.php');

$page = "keranjang";
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$userName = $_SESSION['user'];

$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];

// Add product to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $jumlah = 1;

    $query_check = "SELECT * FROM keranjang WHERE user_id = '$user_id' AND id_produk = '$product_id'";
    $result_check = mysqli_query($kon, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        $query_update = "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE user_id = '$user_id' AND id_produk = '$product_id'";
        mysqli_query($kon, $query_update);
    } else {
        $query_insert = "INSERT INTO keranjang (user_id, id_produk, jumlah) VALUES ('$user_id', '$product_id', '$jumlah')";
        mysqli_query($kon, $query_insert);
    }

    header("Location: produk.php?cart_success=1");
    exit();
}

// Update cart quantity via AJAX
if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['quantity'];

    $response = [];

    $stock_query = mysqli_query($kon, "SELECT p.stok FROM produk p JOIN keranjang k ON p.id_produk = k.id_produk WHERE k.id_keranjang = '$cart_id'");
    $product_data = mysqli_fetch_assoc($stock_query);
    $stok_produk = $product_data['stok'];

    if ($new_quantity > $stok_produk) {
        $response = ['error' => 'Stok tidak mencukupi. Sisa stok: ' . $stok_produk, 'current_quantity' => $stok_produk];
        $sql = "UPDATE keranjang SET jumlah = '$stok_produk' WHERE id_keranjang = '$cart_id'";
        mysqli_query($kon, $sql);
    } elseif ($new_quantity <= 0) {
        $sql = "DELETE FROM keranjang WHERE id_keranjang = '$cart_id'";
        mysqli_query($kon, $sql);
        $response = ['action' => 'removed'];
    } else {
        $sql = "UPDATE keranjang SET jumlah = '$new_quantity' WHERE id_keranjang = '$cart_id'";
        mysqli_query($kon, $sql);
        
        $query_total = "SELECT k.jumlah, p.harga FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.id_keranjang = '$cart_id'";
        $result_total = mysqli_query($kon, $query_total);
        $item = mysqli_fetch_assoc($result_total);
        $updated_total = $item ? $item['jumlah'] * $item['harga'] : 0;
        $response = ['item_total' => $updated_total];
    }
    
    echo json_encode($response);
    exit();
}


// Remove item from cart
if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];
    $sql = "DELETE FROM keranjang WHERE id_keranjang = '$cart_id'";
    mysqli_query($kon, $sql);
    echo json_encode(['action' => 'removed']);
    exit();
}

// Get the products in the cart
$cartItems = mysqli_query($kon, "SELECT k.*, p.nama_produk, p.harga, p.gambar, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.user_id = $user_id");

// Check if cart has items
$has_items = ($cartItems && mysqli_num_rows($cartItems) > 0);

if (isset($_POST['checkout']) && isset($_POST['selected_items'])) {
    $_SESSION['checkout_cart_ids'] = $_POST['selected_items'];
    header("Location: checkout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Cart</title>
  <link rel="stylesheet" href="../assets/css/styleProduk.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table td {
    padding: 10px;
    text-align: center;
    color: black;
    }
    th{
        color: black !important;
    }
    h4{
        color: black;
    }
    h1{
        color: black !important;
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
  <main id="main" class="main-promo">
        <div class="pagetitle">
            <h1><i class="bi bi-cart"></i> Keranjang Belanja</h1>
        
        </div>

        <div class="container mt-5">
            <?php if ($has_items): ?>
                <form method="POST" action="checkout.php">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Pilih</th>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $grandTotal = 0; ?>
                            <?php while ($item = mysqli_fetch_assoc($cartItems)): ?>
                                <tr>
                                    <td><input type="checkbox" name="selected_items[]" value="<?= $item['id_keranjang']; ?>"></td>
                                    <td><img src="../uploads/<?= htmlspecialchars($item['gambar']); ?>" alt="Gambar Produk" width="100"></td>
                                    <td><?= htmlspecialchars($item['nama_produk']); ?></td>
                                    <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <input type="number" name="quantity" value="<?= $item['jumlah']; ?>" 
                                               class="form-control quantity-input" style="width: 80px;"
                                               data-cart-id="<?= $item['id_keranjang']; ?>">
                                    </td>
                                    <td class="item-total">Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-item" data-cart-id="<?= $item['id_keranjang']; ?>">Hapus</button>
                                    </td>
                                </tr>
                                <?php $grandTotal += $item['harga'] * $item['jumlah']; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between">
                        <a href="produk.php" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="checkout" class="btn btn-success">Checkout</button>
                    </div>
                </form>
                <br>
                <div class="d-flex justify-content-end">
                    <h4>Total Pembayaran: <span id="grand-total">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></span></h4>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Keranjang Anda kosong.</div>
                <a href="produk.php" class="btn btn-secondary">Kembali ke Halaman Produk</a>
            <?php endif; ?>
        </div>
    </main>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // Event listener for quantity changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function () {
                const cartId = this.dataset.cartId;
                const newQuantity = this.value;

                // Send AJAX request to update quantity
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `update_quantity=1&cart_id=${cartId}&quantity=${newQuantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.action === 'removed') {
                        // If item is removed, reload the page
                        location.reload();
                    } else {
                        // Update total for the item
                        const itemRow = this.closest('tr');
                        itemRow.querySelector('.item-total').textContent = 
                            `Rp ${new Intl.NumberFormat('id-ID').format(data.item_total)}`;

                        // Recalculate grand total
                        let grandTotal = 0;
                        document.querySelectorAll('.item-total').forEach(total => {
                            grandTotal += parseInt(total.textContent.replace(/[^\d]/g, '')) || 0;
                        });
                        document.getElementById('grand-total').textContent = 
                            `Rp ${new Intl.NumberFormat('id-ID').format(grandTotal)}`;
                    }
                });
            });
        });

        // Event listener for remove item button
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function () {
                const cartId = this.dataset.cartId;

                // Send AJAX request to remove item
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `remove_item=1&cart_id=${cartId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.action === 'removed') {
                        // If item is removed, reload the page
                        location.reload();
                    }
                });
            });
        });
    </script>
    
    <script src="../assets/js/index.js"></script>
</body>
</html>