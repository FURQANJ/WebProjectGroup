<?php
session_start();
include("db.php"); // sambungan database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check email dalam DB
    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Simpan OTP dalam DB
        $update = "UPDATE users SET otp_code='$otp', otp_expiry='$expiry' WHERE email='$email'";
        mysqli_query($conn, $update);

        // Hantar OTP ke email
        $subject = "Password Reset OTP";
        $message = "Your OTP code is: $otp. It will expire in 10 minutes.";
        $headers = "From: no-reply@pusatsukanutem.com";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['reset_email'] = $email;
            header("Location: otp_verify.php");
            exit();
        } else {
            echo "<script>alert('Failed to send OTP email.');</script>";
        }
    } else {
        echo "<script>alert('Email tidak dijumpai dalam sistem!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pusat Sukan UTeM - Forgot Password</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="header-container">
      <img src="UTeM Clear.png" alt="UTeM Logo" class="logo">
      <nav>
        <button type="button" onclick="openPopup('Location', 'Pusat Sukan UTeM.')">Location</button>
        <button type="button" onclick="openCategoriesPopup()">Categories</button>
        <button type="button" onclick="openPopup('Help', 'Add your help content here.')">Help</button>
      </nav>
    </div>
  </header>

  <main class="content-page">
    <div class="login-box">
      <h2>Forgot Password</h2>
      <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your registered Gmail" required>
        <button type="submit" class="student-btn">Send OTP</button>
      </form>
        <div class="links">
      <a href="index.php" class="nav-button">⬅ Back to Login</a>
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
</body>
</html>
