<?php
session_start();
include("db.php");
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
 
    $sql = "SELECT * FROM guest WHERE email='$email' AND is_verified=1 LIMIT 1";
    $result = mysqli_query($conn, $sql);
 
    if ($result && mysqli_num_rows($result) > 0) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
 
        $update = "UPDATE guest SET otp_code='$otp', otp_expiry='$expiry' WHERE email='$email'";
        mysqli_query($conn, $update);
 
        // Menggunakan fungsi mail() asal untuk hantar ke Papercut
        $subject = "Password Reset OTP - Pusat Sukan UTeM";
        $message = "Salam!\n\nKod OTP untuk set semula kata laluan anda ialah: $otp\nKod ini akan tamat dalam masa 10 minit.\n\nTerima kasih,\nPusat Sukan UTeM";
        $headers = "From: no-reply@pusatsukanutem.com";
 
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['otp_email'] = $email;
            $_SESSION['otp_purpose'] = 'forgot_password';
            echo "<script>alert('OTP telah dihantar! Sila semak emel anda.'); window.location.href='otp.php';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal menghantar emel OTP. Pastikan php.ini XAMPP dah di-config ke Papercut.');</script>";
        }
    } else {
        echo "<script>alert('Email tidak dijumpai atau akaun belum disahkan!');</script>";
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
      
        <button type="button" onclick="openPopup('Help', 'No Tel Technician: +60-1140225591')">Help</button>
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
      <p><strong>Best Viewed By Users:</strong></p>
      <p>Users must be responsible for equipments that are borrowed.</p>
      <p>Users must return equipments in good condition before booking timer ends.</p>
      <p>Users must follow these rules to avoid account penalties and disciplinary action.</p>
    </div>
    <div class="social">
      <p><strong>Our Socials:</strong></p>
      <a href="https://www.utem.edu.my/en/"><img src="Universiti_Teknikal_Malaysia_Melaka_logo.png" alt="UTeM"></a>
      <a href="https://www.instagram.com/pusatsukanutem/"><img src="Instagram_icon.png" alt="Instagram"></a>
      <a href="https://www.facebook.com/PusatSukanUTeM/"><img src="Facebook_Logo.png" alt="Facebook"></a>
    </div>
  </footer>
</body>
</html>