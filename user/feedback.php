<?php
session_start();
require "../db.php";

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user'];

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle new feedback submission
if (isset($_POST['submit_feedback'])) {
    $feedback = mysqli_real_escape_string($kon, $_POST['feedback']);
    $insert_query = "INSERT INTO feedback (user_id, content) VALUES ('$user_id', '$feedback')";
    if (mysqli_query($kon, $insert_query)) {
        echo '<script>alert("Feedback berhasil diajukan!"); window.location="feedback.php";</script>';
    } else {
        echo '<script>alert("Gagal menyimpan feedback: ' . mysqli_error($kon) . '");</script>';
    }
}

// Fetch feedback
$fetch_query = "SELECT f.id, f.content, f.created_at, u.nama 
                FROM feedback f 
                JOIN user u ON f.user_id = u.nama
                ORDER BY f.created_at DESC";

$feedbacks = mysqli_query($kon, $fetch_query);

if (!$feedbacks) {
    echo '<script>alert("Query gagal: ' . mysqli_error($kon) . '");</script>';
}

if (mysqli_num_rows($feedbacks) == 0) {
    echo '<script>alert("Tidak ada feedback ditemukan.");</script>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Feedback</title>
  <link rel="stylesheet" href="../assets/css/stylefeedback.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill.quill.bubble.css" rel="stylesheet">
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
      <li><a href="feedback.php" class="active"><span class="mdi--feedback-outline"></span>FEEDBACK</a></li>
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
            <h1><i class="bi bi-chat-dots"></i>&nbsp; Feedback</h1>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ajukan Feedback</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <textarea name="feedback" class="form-control" placeholder="Ajukan feedback Anda..." required></textarea>
                                </div>
                                <button type="submit" name="submit_feedback" class="btn btn-success"><i class="bi bi-send"></i>&nbsp; Ajukan Feedback</button>
                            </form>
                            <br>
                            <h5 class="card-title">Daftar Feedback</h5>
                            <div class="accordion" id="feedbackAccordion">
                                <?php if (mysqli_num_rows($feedbacks) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($feedbacks)): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading<?php echo $row['id']; ?>">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $row['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $row['id']; ?>">
                                                    <?php echo $row['nama']; ?> memberikan feedback pada <?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?>
                                                </button>
                                            </h2>
                                            <div id="collapse<?php echo $row['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $row['id']; ?>" data-bs-parent="#feedbackAccordion">
                                                <div class="accordion-body">
                                                    <p><?php echo $row['content']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning" role="alert">
                                        Tidak ada feedback ditemukan.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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