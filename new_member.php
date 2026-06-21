<?php
session_start();
include("db.php"); // sambungan database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matrik = mysqli_real_escape_string($conn, $_POST['matrik']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "<script>alert('Password tidak sama!');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Generate OTP
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $sql = "INSERT INTO users (matrik, email, password, otp_code, otp_expiry) 
                VALUES ('$matrik', '$email', '$hashedPassword', '$otp', '$expiry')";

        if (mysqli_query($conn, $sql)) {
            $subject = "Your OTP Code";
            $message = "Here is your OTP code: $otp. It will expire in 10 minutes.";
            $headers = "From: no-reply@pusatsukanutem.com";

            if (mail($email, $subject, $message, $headers)) {
                echo "<script>alert('Registration successful! Check your email for OTP.');</script>";
                $_SESSION['email'] = $email;
                header("Location: otp_verify.php");
                exit();
            } else {
                echo "<script>alert('Failed to send OTP email.');</script>";
            }
        } else {
            echo "<script>alert('Error: Could not register user.');</script>";
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
        <button type="button" onclick="openCategoriesPopup()">Categories</button>
        <button type="button" onclick="openPopup('Help', 'Add your help content here.')">Help</button>
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
