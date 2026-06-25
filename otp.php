<?php
session_start();
include "db.php";
 
if (!isset($_SESSION['otp_email'])) {
    header("Location: index.php");
    exit();
}
 
$email = $_SESSION['otp_email'];
$purpose = $_SESSION['otp_purpose'] ?? 'register';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);
 
    $safeEmail = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT otp_code, otp_expiry FROM guest WHERE email='$safeEmail' LIMIT 1";
    $result = mysqli_query($conn, $sql);
 
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $db_otp = $row['otp_code'];
        $expiry = $row['otp_expiry'];
 
        if (strtotime($expiry) < time()) {
            echo "<script>alert('OTP telah tamat tempoh! Sila daftar semula.'); window.location.href='new_member.php';</script>";
        } elseif ($entered_otp == $db_otp) {
            if ($purpose === 'register') {
                // Berjaya, tukar status is_verified ke 1
                mysqli_query($conn, "UPDATE guest SET is_verified=1, otp_code=NULL, otp_expiry=NULL WHERE email='$safeEmail'");
                unset($_SESSION['otp_email'], $_SESSION['otp_purpose']);
                echo "<script>alert('OTP berjaya disahkan! Akaun anda telah didaftarkan. Sila login.'); window.location.href='index.php';</script>";
                exit();
            } elseif ($purpose === 'forgot_password') {
                mysqli_query($conn, "UPDATE guest SET otp_code=NULL, otp_expiry=NULL WHERE email='$safeEmail'");
                $_SESSION['reset_email'] = $email;
                unset($_SESSION['otp_email'], $_SESSION['otp_purpose']);
                header("Location: reset_password.php");
                exit();
            }
        } else {
            echo "<script>alert('OTP tidak betul! Sila cuba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Akaun tidak dijumpai!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OTP Verification - Pusat Sukan UTeM</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="header-container">
      <img src="UTeM Clear.png" alt="UTeM Logo" class="logo">
      <nav>
        <button type="button" onclick="window.open('https://www.google.com/maps?q=Stadium+UTeM,+76100+Durian+Tunggal,+Melaka', '_blank')">Location</button>
        <button type="button" onclick="openPopup('Contact Us', 'No Tel Technician: +60-1140225591')">Contact Us</button>
      </nav>
    </div>
  </header>
  <main>
    <div class="login-box">
      <h2>OTP Verification</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="text" name="otp" placeholder="Enter 6-Digit OTP" required>
        <button type="submit" class="student-btn">Confirm OTP</button>
      </form>
      <div class="links">
        <a href="new_member.php">← Back to Registration</a>
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

  <script src="script.js"></script>
</body>
</html>