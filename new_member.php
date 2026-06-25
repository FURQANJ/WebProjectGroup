<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matrik = mysqli_real_escape_string($conn, $_POST['matrik']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "<script>alert('Password tidak sama!');</script>";
    } else {
        // Semak jika matrik/emel dah berdaftar & verified
        $checkSql = "SELECT * FROM guest WHERE (matrik='$matrik' OR email='$email') AND is_verified=1 LIMIT 1";
        $checkResult = mysqli_query($conn, $checkSql);
        
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            echo "<script>alert('No. Matrik atau emel sudah didaftarkan!');</script>";
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Buang rekod lama yang belum verify
            mysqli_query($conn, "DELETE FROM guest WHERE email='$email' AND is_verified=0");

            // Masukkan rekod pendaftaran baru
            $sql = "INSERT INTO guest (matrik, email, password, otp_code, otp_expiry, is_verified)
                    VALUES ('$matrik', '$email', '$hashedPassword', '$otp', '$expiry', 0)";

            if (mysqli_query($conn, $sql)) {
                
                // Menggunakan fungsi mail() asal untuk hantar ke Papercut
                $subject = "Your OTP Code - Pusat Sukan UTeM";
                $message = "Salam, $matrik!\n\nKod OTP anda ialah: $otp\nKod ini akan tamat dalam masa 10 minit.\n\nTerima kasih,\nPusat Sukan UTeM";
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
      <h2>New Member Registration</h2>
      <form method="POST" action="">
        <input type="text" name="matrik" placeholder="No Matrik" required>
        <input type="email" name="email" placeholder="Gmail" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
        <button type="submit" class="student-btn">Register</button>
        <div class="links">
          <a href="index.php" class="nav-button">⬅ Back to Login</a>
        </div>
      </form>
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