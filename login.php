<?php
session_start();
require "db.php"; // Pastikan path ke db.php sudah benar
$error = "";
$msg = ""; // Variabel untuk menyimpan pesan notifikasi

// --- START: Logika untuk menampilkan pesan dari sesi (setelah redirect) ---
if (isset($_SESSION['notification_message'])) {
    $msg = $_SESSION['notification_message'];
    unset($_SESSION['notification_message']); // Hapus pesan dari sesi setelah ditampilkan
}
// --- END: Logika untuk menampilkan pesan dari sesi ---

if (isset($_POST["login"])){
    // LOGIN USER
    $email = mysqli_real_escape_string($kon, isset($_POST["email"]) ? $_POST["email"] : "");
    $pwd   = mysqli_real_escape_string($kon, isset($_POST["pwd"]) ? $_POST["pwd"] : "");

    // CEK APAKAH MASIH KOSONG
    if (empty($email) || empty($pwd)) // Menggunakan || (OR) untuk empty check
    {
        $msg = '
            <div class="alert alert-warning mt-3" role="alert">
                <i class="fas fa-exclamation-triangle"></i>&nbsp; MAAF, EMAIL / PASSWORD ANDA MASIH KOSONG. SILAHKAN ISI DENGAN BENAR!
            </div>
        ';
    } else {
        // PROSEDUR LOGIN BUAT ADMIN :
        // Perbaikan: Gunakan prepared statements untuk keamanan (opsional untuk versi ini, tapi sangat disarankan untuk produksi)
        $kue_admin = mysqli_query($kon, "SELECT * FROM user WHERE email = '" . $email . "' AND password = '" . $pwd . "'");
        $row_admin = mysqli_fetch_array($kue_admin);
        
        if ($row_admin && $row_admin["level"] == "admin") { // Cek jika ada baris DAN levelnya admin
            // UPDATE ACTIVE FIELD
            $update_admin_active = "UPDATE user SET active = NOW() WHERE email = '$email'";
            mysqli_query($kon, $update_admin_active);

            // SET SESSION DAN REDIRECT
            $_SESSION["admin"] = $row_admin["nama"];
            header("Location: admin/index.php");
            exit;
        }

        // PROSEDUR LOGIN UNTUK USER (akan dieksekusi jika bukan admin atau admin login gagal)
        $kue_user = mysqli_query($kon, "SELECT * FROM user WHERE email = '$email' AND password = '$pwd'");
        $row_user = mysqli_fetch_array($kue_user);

        if ($row_user && $row_user["level"] == "user") { // Cek jika ada baris DAN levelnya user
            // UPDATE ACTIVE FIELD
            $update_user_active = "UPDATE user SET active = NOW() WHERE email = '$email'";
            mysqli_query($kon, $update_user_active);

            // SET SESSION DAN REDIRECT
            $_SESSION["user"] = $row_user["nama"];
            header("Location: user/index.php");
            exit;
        } else {
            // Jika tidak ada yang cocok (bukan admin dan bukan user, atau kredensial salah)
            $msg = '
                <div class="alert alert-danger mt-3" role="alert">
                    <i class="fas fa-times-circle"></i>&nbsp; MAAF, EMAIL / PASSWORD ANDA SALAH. SILAHKAN ULANGI LAGI!
                </div>
            ';
        }
    }
    // Tidak perlu $error = true; di sini, $msg sudah cukup untuk indikasi
}

if (isset($_POST["register"])){
    $nama_user = mysqli_real_escape_string($kon, isset($_POST["nama_user"]) ? $_POST["nama_user"] : "");
    $email = mysqli_real_escape_string($kon, isset($_POST["email"]) ? $_POST["email"] : "");
    $password = mysqli_real_escape_string($kon, isset($_POST["password"]) ? $_POST["password"] : "");
    $no_tlp = mysqli_real_escape_string($kon, isset($_POST["no_tlp"]) ? $_POST["no_tlp"] : "");
    $alamat = mysqli_real_escape_string($kon, isset($_POST["alamat"]) ? $_POST["alamat"] : "");
    $level = mysqli_real_escape_string($kon, isset($_POST["level"]) ? $_POST["level"] : "");

    if (empty($nama_user) || empty($email) || empty($password) || empty($no_tlp) || empty($alamat) || empty($level)){ // Menggunakan || (OR) untuk empty check
        $msg = '
            <div class="alert alert-warning mt-3" role="alert">
                <i class="fas fa-exclamation-triangle"></i>&nbsp; MAAF, SEMUA FIELD HARUS DIISI. SILAHKAN ISI DENGAN BENAR !
            </div>
        ';
    } else {
        // Cek apakah email sudah terdaftar (Penting untuk registrasi!)
        $check_email_query = mysqli_query($kon, "SELECT email FROM user WHERE email = '$email'");
        if (mysqli_num_rows($check_email_query) > 0) {
            $msg = '
                <div class="alert alert-danger mt-3" role="alert">
                    <i class="fas fa-exclamation-circle"></i>&nbsp; MAAF, EMAIL INI SUDAH TERDAFTAR. SILAHKAN GUNAKAN EMAIL LAIN!
                </div>
            ';
        } else {
            // Perbaikan: Gunakan prepared statements untuk keamanan (opsional untuk versi ini, tapi sangat disarankan untuk produksi)
            $query_insert = mysqli_query($kon, "INSERT INTO user (nama, email, password, no_tlp, alamat, level) VALUES ('$nama_user', '$email', '$password', '$no_tlp', '$alamat', '$level')");
            if ($query_insert){
                // --- START: Perbaikan untuk feedback register ---
                $_SESSION['notification_message'] = '
                    <div class="alert alert-success mt-3" role="alert">
                        <i class="fas fa-check-circle"></i> REGISTER BERHASIL. SILAHKAN LOGIN !
                    </div>
                ';
                header("Location: login.php"); // Redirect ke halaman login.php
                exit(); // Pastikan script berhenti setelah redirect
                // --- END: Perbaikan untuk feedback register ---
            } else {
                $msg = '
                    <div class="alert alert-danger mt-3" role="alert">
                        <i class="fas fa-times-circle"></i>&nbsp; REGISTER GAGAL. SILAHKAN ULANGI LAGI !
                    </div>
                ';
            }
        }
    }
    // Tidak perlu $error = true; di sini, $msg sudah cukup untuk indikasi
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>GameGlee Login Page</title>
    <!-- Link Bootstrap CSS untuk styling alert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link CSS kustom Anda -->
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <!-- Link Font Awesome untuk ikon -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <!-- Iconify (perbaikan: harus script, bukan stylesheet) -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- Favicons -->
    <link href="assets/img/Logo_GG2.png" rel="icon" sizes="48x48">

    <style>
        /* Anda mungkin perlu menambahkan CSS kustom tambahan di sini jika styles.css tidak sepenuhnya responsif atau jika ada konflik */
        /* Contoh: agar alert muncul di atas container utama */
        .alert {
            position: absolute;
            top: 20px; /* Sesuaikan posisi sesuai kebutuhan */
            left: 50%;
            transform: translateX(-50%);
            width: 80%; /* Sesuaikan lebar */
            max-width: 500px; /* Batasi lebar maksimum */
            z-index: 1000; /* Pastikan alert di atas elemen lain */
            text-align: center;
        }
        /* Style untuk social icons jika diperlukan */
        .social-container .social span[class*="iconify"] {
            font-size: 24px; /* Ukuran ikon */
            vertical-align: middle;
        }
        /* Memastikan select box Bootstrap ter-render dengan baik */
        .form-control {
            border-radius: 20px; /* Contoh: Menyesuaikan dengan gaya input lain */
        }
        /* Jika styles.css tidak mengatur tinggi min-height untuk container */
        .container {
            min-height: 100vh; /* Agar container mencakup seluruh tinggi viewport */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative; /* Penting untuk positioning alert absolute */
        }
        .container #main {
            /* Pastikan ada gaya untuk kontainer utama jika styles.css tidak mengaturnya */
            width: 100%;
            height: 100%;
            max-width: 900px; /* Contoh: atur lebar maksimum */
            background: #fff; /* Contoh: Latar belakang kontainer utama */
            border-radius: 10px;
            overflow: hidden; /* Penting jika ada elemen yang melampaui batas */
            position: relative; /* Untuk positioning elemen di dalamnya */
        }
    </style>
</head>
<body>
    <div class="container" id="main">
        <?php 
        // --- START: Tampilkan pesan notifikasi di sini ---
        if (!empty($msg)) {
            echo $msg;
        }
        // --- END: Tampilkan pesan notifikasi ---
        ?>

        <div class="sign-up">
            <form class="register" method="post">
                <h1>Register</h1>
                <div class="social-container">
                    <a href="#" class="social"><span class="grommet-icons--facebook-option"></span></a>
                    <a href="#" class="social"><span class="flat-color-icons--google"></span></a>
                </div>
                <p>or create your new account</p>
                <input type="text" name="nama_user" placeholder="Nama Lengkap" required="">
                <input type="email" name="email" placeholder="Email" required="">
                <input type="text" name="no_tlp" placeholder="Nomor Telepon" required="">
                <input type="text" name="alamat" placeholder="Alamat" required="">
                <input type="password" name="password" placeholder="Password" required="">
                <select name="level" class="form-control" required> <!-- Tambahkan 'required' -->
                    <option value="">Pilih Level Anda Sebagai User</option>
                    <option value="user">User</option>
                </select>         
                <button name="register" type="submit">Register</button>
            </form>
        </div>

        <div class="sign-in">
            <form class="login" method="post">
                <h1>Login</h1>
                <div class="social-container">
                    <a href="#" class="social"><span class="grommet-icons--facebook-option"></span></a>
                    <a href="#" class="social"><span class="flat-color-icons--google"></span></a>
                </div>
                <p>or use your account</p>
                <input type="email" name="email" placeholder="Email" required="">
                <input type="password" name="pwd" placeholder="Password" required="">
                <button name="login" type="submit">Login</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-left">
                    <img src="assets/img/Logo_GG.png" alt="Logo Gameglee" class="logo">
                    <h1>Wellcome Back!</h1>
                    <p>To keep connected with us please login with your personal account</p>
                    <button id="signIn">Login</button>
                </div>
                <div class="overlay-right">
                    <img src="assets/img/Logo_GG.png" alt="Logo Gameglee" class="logo">
                    <h1>Hi, Friend</h1>
                    <p>Please enter your personal details and start to feel the glee with us.</p>
                    <button id="signUp">Register</button>
                </div>
            </div>
        </div>
    </div>
    <div class="slider">
        <div class="list">
            <div class="item">
                <img src="assets/img/BG LOGIN.jpg" alt="bg 1">
            </div>
            <div class="item">
                <img src="assets/img/BG LOGIN 2.jpg" alt="bg 2">
            </div>
            <div class="item">
                <img src="assets/img/BG LOGIN 3.jpg" alt="bg 3">
            </div>
            <div class="item">
                <img src="assets/img/BG LOGIN 4.jpg" alt="bg 4">
            </div>
            <div class="item">
                <img src="assets/img/BG LOGIN 5.jpg" alt="bg 5">
            </div>
            <div class="item">
                <img src="assets/img/BG LOGIN 6.jpg" alt="bg 6">
            </div>
        </div>
    </div>
    <script src="assets/js/login.js" type="text/javascript"></script>
    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
