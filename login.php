<?php
session_start();
require "db.php";
$error = "";

if (isset($_POST["login"])){
  // LOGIN USER
  $email = mysqli_real_escape_string($kon, isset($_POST["email"]) ? $_POST["email"] : "");
  $pwd  = mysqli_real_escape_string($kon, isset($_POST["pwd"]) ? $_POST["pwd"] : "");

  // CEK APAKAH MASIH KOSONG
  if (empty($email) or empty($pwd))
  {
    $msg = '
      <div class="alert alert-warning">
              &nbsp; MAAF, EMAIL / PASSWORD ANDA MASIH KOSONG. SILAHKAN ISI DENGAN BENAR!
      </div>
    ';
  } else {
    
    

    // PROSEDUR LOGIN BUAT ADMIN :
    $kue_admin = mysqli_query($kon, "SELECT * FROM user WHERE email = '" . $email . "' AND password = '" . $pwd . "'");
    $row_admin = mysqli_fetch_array($kue_admin);
    
    if (!($row_admin))
    {
      $msg = '
        <div class="alert alert-danger">
          &nbsp; MAAF, EMAIL / PASSWORD ANDA SALAH. SILAHKAN ULANGI LAGI !
        </div>
      ';
    } else {
        if ($row_admin["level"] == "admin") {
          // UPDATE ACTIVE FIELD
          $update_admin_active = "UPDATE user SET active = NOW() WHERE email = '$email'";
          mysqli_query($kon, $update_admin_active);

          // SET SESSION DAN REDIRECT
          $_SESSION["admin"] = $row_admin["nama"];
          header("Location: admin/index.php");
          exit;
      }
    }

      // PROSEDUR LOGIN UNTUK USER
      $kue_user = mysqli_query($kon, "SELECT * FROM user WHERE email = '$email' AND password = '$pwd'");
      $row_user = mysqli_fetch_array($kue_user);

      if (!$row_user) {
          $msg = '
              <div class="alert alert-danger">
                  <i class="bi bi-exclamation-circle-fill"></i>&nbsp; MAAF, EMAIL / PASSWORD ANDA SALAH. SILAHKAN ULANGI LAGI!
              </div>
          ';
      } else {
          if ($row_user["level"] == "user") {
              // UPDATE ACTIVE FIELD
              $update_user_active = "UPDATE user SET active = NOW() WHERE email = '$email'";
              mysqli_query($kon, $update_user_active);

              // SET SESSION DAN REDIRECT
              $_SESSION["user"] = $row_user["nama"];
              header("Location: user/index.php");
              exit;
          }
      }

  }

  $error = true;

}

if (isset($_POST["register"])){
  $nama_user = mysqli_real_escape_string($kon, isset($_POST["nama_user"]) ? $_POST["nama_user"] : "");
  $email = mysqli_real_escape_string($kon, isset($_POST["email"]) ? $_POST["email"] : "");
  $password = mysqli_real_escape_string($kon, isset($_POST["password"]) ? $_POST["password"] : "");
  $no_tlp = mysqli_real_escape_string($kon, isset($_POST["no_tlp"]) ? $_POST["no_tlp"] : "");
  $alamat = mysqli_real_escape_string($kon, isset($_POST["alamat"]) ? $_POST["alamat"] : "");
  $level = mysqli_real_escape_string($kon, isset($_POST["level"]) ? $_POST["level"] : "");

  if (empty($nama_user) or empty($email) or empty($password) or empty($no_tlp) or empty($alamat) or empty($level)){
    $msg = '
      <div class="alert alert-warning">
        &nbsp; MAAF, SEMUA FIELD HARUS DIISI. SILAHKAN ISI DENGAN BENAR !
      </div>
    ';
  } else {
    $query = mysqli_query($kon, "INSERT INTO user (nama, email, password, no_tlp, alamat, level) VALUES ('$nama_user', '$email', '$password', '$no_tlp', '$alamat', '$level')");
    if ($query){
      $msg = '
        <div class="alert alert-success">
          &nbsp; REGISTER BERHASIL. SILAHKAN LOGIN !
        </div>
      ';
      header("Location: login.php");
      exit(); // Pastikan script berhenti setelah redirect
    } else {
      $msg = '
        <div class="alert alert-danger">
          &nbsp; REGISTER GAGAL. SILAHKAN ULANGI LAGI !
        </div>
      ';
    }
  }

  $error = true;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>GameGlee Login Page</title>
	<link rel="stylesheet" type="text/css" href="assets/css/styles.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">

    <!-- Favicons -->
  <link href="assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
</head>
<body>
	<div class="container" id="main">
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
            <select name="level" class="form-control">
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
  </script>
</body>
</html>