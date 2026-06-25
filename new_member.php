<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matrik = mysqli_real_escape_string($conn, $_POST['matrik']);
    $guest_name = mysqli_real_escape_string($conn, $_POST['guest_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "<script>alert('Password tidak sama!');</script>";
    } else {

        // check klau matrik/emel dah berdaftar & verified
        $checkSql = "SELECT * FROM guest WHERE (matrik='$matrik' OR email='$email') AND is_verified=1 LIMIT 1";
        $checkResult = mysqli_query($conn, $checkSql);
        
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            echo "<script>alert('No. Matrik atau emel sudah didaftarkan!');</script>";
        } else {
            // generate otp
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Buang rekod lama yang belum verify
            mysqli_query($conn, "DELETE FROM guest WHERE email='$email' AND is_verified=0");

            // masuk rekod pendaftaran baru dgn nama
            $sql = "INSERT INTO guest (matrik, guest_name, email, password, otp_code, otp_expiry, is_verified)
                    VALUES ('$matrik', '$guest_name', '$email', '$hashedPassword', '$otp', '$expiry', 0)";

            if (mysqli_query($conn, $sql)) {
                
                // guna mail() asal untuk hantar gi  Papercut
                $subject = "Your OTP Code - Pusat Sukan UTeM";
                $message = "Salam, $guest_name!\n\nKod OTP anda ialah: $otp\nKod ini akan tamat dalam masa 10 minit.\n\nTerima kasih,\nPusat Sukan UTeM";
                $headers = "From: no-reply@pusatsukanutem.com";

                if (mail($email, $subject, $message, $headers)) {
                    $_SESSION['otp_email'] = $email;
                    $_SESSION['otp_purpose'] = 'register';
                    echo "<script>alert('Pendaftaran berjaya! Sila semak emel untuk kod OTP.'); window.location.href='otp.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Gagal menghantar emel fungsi mail(). Pastikan php.ini XAMPP dah di-config ke Papercut.');</script>";
                }

            } else {
                echo "<script>alert('Ralat database: " . mysqli_real_escape_string($conn, mysqli_error($conn)) . "');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pusat Sukan UTeM - New Member</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .warning-text {
      display: block;
      width: 80%;
      margin: -5px auto 10px auto;
      text-align: left;
      font-size: 11px;
      color: #c62828;
      font-weight: bold;
    }
  </style>
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

  <main class="content-page">
    <div class="login-box">
      <h2>New Member Registration</h2>
      <form method="POST" action="">
        <input type="text" name="matrik" placeholder="No Matrik" required>
        
        <input type="text" name="guest_name" placeholder="Full Name (As per Student Card)" required>
        <span class="warning-text">* Please enter your real full name. This cannot be changed later and will be used for all official bookings.</span>

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
        <button type="submit" class="student-btn">Register</button>
        <div class="links">
          <a href="index.php" class="nav-button" style="color: #ffffff;">⬅ Back to Login</a>
        </div>
      </form>
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