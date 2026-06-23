<?php
session_start();
include "db.php";
 

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email']; 
 
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Password tidak sepadan!');</script>";
    } elseif (strlen($new_password) < 6) {
        echo "<script>alert('Password mestilah sekurang-kurangnya 6 aksara!');</script>";
    } else {
      
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $safeEmail = mysqli_real_escape_string($conn, $email);
 
        $sql = "UPDATE guest SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed, $safeEmail);
 
        if ($stmt->execute()) {
           
            unset($_SESSION['reset_email']);
            echo "<script>alert('Password berjaya ditukar! Sila login semula.'); window.location.href='index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Ralat semasa kemaskini password.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Pusat Sukan UTeM</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="header-container">
      <img src="UTeM Clear.png" alt="UTeM Logo" class="logo">
      <nav>
        <button type="button" onclick="openPopup('Location', 'Pusat Sukan UTeM.')">Location</button>

        <button type="button" onclick="openPopup('Help', 'No Tel Technician: +60-1140225591')">Help</button>
      </nav>
    </div>
  </header>

  <main>
    <div class="login-box">
      <h2>Reset Password</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="password" name="new_password" placeholder="Enter New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit" class="student-btn">Reset</button>
      </form>
      <div class="links">
        <a href="index.php">← Back to Login</a>
      </div>
    </div>
  </main>

  <footer>
    <div class="notice">
      <p><strong>Peringatan:</strong></p>
      <p>Pengguna perlulah bertanggungjawab atas barang tersebut.</p>
      <p>Pengguna perlu menghantar barang 3 minit sebelum masa tamat dalam keadaan baik.</p>
      <p>Penalti akan dikenakan atas individu yang tidak dapat mematuhi peraturan tersebut.</p>
    </div>
    <div class="social">
      <a href="https://www.utem.edu.my/en/">🌐</a>
      <a href="https://www.instagram.com/pusatsukanutem/">📸</a>
      <a href="https://www.facebook.com/PusatSukanUTeM/">📘</a>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>