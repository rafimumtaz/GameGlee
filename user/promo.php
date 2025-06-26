<?php
session_start();
require "../db.php";
$page = "produk";

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve `user_id` based on `nama` in the session
$nama = $_SESSION['user']; // Assuming user's `nama` is stored in session
$user_id = null;

// Fetch the user_id from the user table based on nama
$user_query = "SELECT id_user FROM user WHERE nama = ?";
$stmt_user = mysqli_prepare($kon, $user_query);
mysqli_stmt_bind_param($stmt_user, "s", $nama);
mysqli_stmt_execute($stmt_user);
mysqli_stmt_bind_result($stmt_user, $user_id);
mysqli_stmt_fetch($stmt_user);
mysqli_stmt_close($stmt_user);

if (!$user_id) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Promo</title>
  <link rel="stylesheet" href="../assets/css/stylepromo.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->                                                                                    
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
   <style>
        .promo-card {
            background: linear-gradient(135deg,rgb(255, 195, 43), rgb(255, 135, 43),rgb(255, 85, 0) );
            color: white;
            letter-spacing: 2px;
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 15px;
        }
        .promo-code {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .promo-value {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .promo-type {
            font-size: 14px;
            opacity: 0.8;
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
      <li><a href="promo.php" class="active"><span class="tabler--discount"></span>CODE PROMO</a></li>
      <br><br><br>
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
            <h1><i class="bi bi-gift"></i> Your Special Promo Codes</h1>
            
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Available Promo Codes</h5>
                            <div class="row">
                                <?php
                                try {
                                    // Check database connection
                                    if (!$kon) {
                                        throw new Exception("Database connection failed");
                                    }

                                    // Number of random promo codes to show
                                    $num_promos = 2;

                                    // Fetch promo codes without inserting into user_promo_codes or updating times_used
                                    $promo_query = "SELECT p.id, p.code, p.discount_type, p.discount_value 
                                                    FROM promo p
                                                    LEFT JOIN user_promo_codes upc ON p.id = upc.promo_id AND upc.user_id = ?
                                                    WHERE upc.id_user_promo_code IS NULL 
                                                      AND p.times_used < p.usage_limit  
                                                    LIMIT ?";
                                    
                                    $stmt = mysqli_prepare($kon, $promo_query);
                                    if ($stmt) {
                                        mysqli_stmt_bind_param($stmt, "ii", $user_id, $num_promos);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while ($promo_row = mysqli_fetch_assoc($result)) {
                                                ?>
                                                <div class="col-md-6">
                                                    <div class="promo-card">
                                                        <div class="promo-code">
                                                            <?php echo htmlspecialchars($promo_row['code']); ?>
                                                        </div>
                                                        <div class="promo-value">
                                                            <?php
                                                            if ($promo_row['discount_type'] == 'fixed') {
                                                                echo 'Rp. ' . number_format($promo_row['discount_value'], 0, ',', '.');
                                                            } else {
                                                                echo htmlspecialchars($promo_row['discount_value']) . '%';
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="promo-type">
                                                            <?php echo ($promo_row['discount_type'] == 'fixed' ? 'Fixed Discount' : 'Percentage Discount'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            echo "<div class='alert alert-info'>No promo codes available at the moment.</div>";
                                        }
                                        mysqli_stmt_close($stmt);
                                    } else {
                                        echo "<div class='alert alert-warning'>Error preparing database query.</div>";
                                    }
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

  <script src="../assets/js/index.js"></script>
</body>
</html>