<?php
session_start();
require "../db.php"; 

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user"]; 
$result = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user_id'");

if (!$result) {
    die('Invalid query: ' . mysqli_error($kon));
}

$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Error: User not found.";
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GameGlee's Profile</title>
  <link rel="stylesheet" href="../assets/css/styleDashboard.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
   <!-- Favicons -->
   <link href="../assets/img/Logo_GG2.png" rel="icon" sizes="48x48">
   <style>
        /* Modal Styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
    padding-top: 60px;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccc;
}

.modal-header h5 {
    margin: 0;
}

.close {
    color: #aaa;
    font-size: 30px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.modal-body {
    padding: 10px 0;
}

.mb-3 {
    margin-bottom: 15px;
}

.form-label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #ccc;
}

/* Button Styles */
.btn {
    padding: 8px 15px;
    font-size: 16px;
    cursor: pointer;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
}

.btn-success {
    background-color: #28a745;
    color: white;
    border: none;
}

.btn:hover {
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
      <li><a href="history_pembayaran.php"><span class="ic--twotone-history"></span>HISTORY PEMBELIAN</a></li>
      <li><a href="wishlist.php"><span class="ph--list-star"></span>WISHLIST</a></li>
      <br><br><br><br><br><br><br><br><br><br>
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
            <a href="profil.php" class="active">
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
  <div class="main-content">
  <main id="main" class="main-profile">
        <div class="pagetitle">
            <h3>My Profile</h3>
            <p>admin</p>
        </div>
        
        <div class="card-profile">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']); 
                                ?>
                            </div>
                            <?php elseif (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']); 
                                ?>
                            </div>
                            <?php endif; ?>
                            <h2 class="text-center mb-4">Profil</h2>
                            <div class="row">
                                <div class="image">
                                    <img src="../uploads/<?php echo htmlspecialchars($user['foto']); ?>" alt="Profile Image" class="img-fluid rounded mb-4" />
                                </div>
                                <div class="col-md-8">
                                    <h3><?php echo htmlspecialchars($user['nama']); ?></h3>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                    <p><strong>No Telp:</strong> <?php echo htmlspecialchars($user['no_tlp']); ?></p>
                                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($user['alamat']); ?></p>
                                    <button class="btn btn-warning" onclick="openModal()">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal Edit Profil -->
    <div  id="editProfileModal" class="modal">
        <div class="modal-content">
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                        <span class="close" onclick="closeModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<?php echo $user['id_user']; ?>">
                        <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($user['foto']); ?>">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_tlp" class="form-label">No Telp</label>
                            <input type="text" class="form-control" id="no_tlp" name="no_tlp" value="<?php echo htmlspecialchars($user['no_tlp']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Ganti Foto Profil</label>
                            <input type="file" class="form-control" id="foto" name="foto">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
        </div>
    </div>
    <script>
           // Open modal
function openModal() {
    document.getElementById('editProfileModal').style.display = 'block';
}

// Close modal
function closeModal() {
    document.getElementById('editProfileModal').style.display = 'none';
}

// Close modal if clicked outside
window.onclick = function(event) {
    var modal = document.getElementById('editProfileModal');
    if (event.target == modal) {
        closeModal();
    }
}
    </script>

  <script src="../assets/js/index.js"></script>
</body>
</html>