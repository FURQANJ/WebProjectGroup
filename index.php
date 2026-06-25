<?php
session_start();
ob_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $userId = mysqli_real_escape_string($conn, $_POST['userId']);
  $password = $_POST['password'];
  $role = $_POST['role'];

  if ($role == "admin") {
    $sql = "SELECT * FROM admin WHERE admin_id = '$userId' OR venue_and_equipment_update = '$userId' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
      $userRow = mysqli_fetch_assoc($result);

      if ($userRow['password'] === $password) {
        $_SESSION['admin_id'] = $userRow['admin_id'];
        $_SESSION['role'] = "admin";
        header("Location: adminhome.php");
        exit();
      } else {
        echo "<script>alert('Password Admin salah!');</script>";
      }
    } else {
      echo "<script>alert('Akaun Admin tidak dijumpai!');</script>";
    }
  } else if ($role == "student") {
    // cari ikut no matrik
    $sql = "SELECT * FROM guest WHERE matrik = '$userId' AND is_verified = 1 LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
      $userRow = mysqli_fetch_assoc($result);
      // check password
      $passwordMatch = false;

      // check dgn hashed password
      if (password_verify($password, $userRow['password'])) {
        $passwordMatch = true;
      }
      // kalau xjadi guna password biasa
      else if ($userRow['password'] === $password) {
        $passwordMatch = true;
      }

if ($passwordMatch) {
        $_SESSION['guest_id'] = $userRow['guest_id'];
        $_SESSION['guest_name'] = $userRow['guest_name']; // ADD THIS LINE
        $_SESSION['matrik'] = $userRow['matrik'];
        $_SESSION['role'] = "student";
        header("Location: MainPage.html");
        exit();
      } else {
        echo "<script>alert('Password Student salah!');</script>";
      }
    } else {
      $checkSql = "SELECT * FROM guest WHERE matrik = '$userId' LIMIT 1";
      $checkResult = mysqli_query($conn, $checkSql);
      if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Akaun belum disahkan! Sila semak emel OTP anda.');</script>";
      } else {
        echo "<script>alert('Akaun Student tidak dijumpai! Sila pastikan No. Matrik betul.');</script>";
      }
    }
  }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Pusat Sukan UTeM</title>
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

  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-box">
      <button type="button" class="popup-close" onclick="closePopup()">X</button>
      <h2 id="popupTitle"></h2>
      <div id="popupContent"></div>
    </div>
  </div>

  <main>
    <div class="login-box">
      <h2>WELCOME TO PUSAT SUKAN UTEM</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="text" name="userId" placeholder="No Matrik / Admin ID" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="role" value="student" class="student-btn">Login For Student</button>
        <button type="submit" name="role" value="admin" class="admin-btn">Login For Admin</button>
      </form>
      <div class="links">
        <a href="forgot_password.php" style="color: #002b7f">Forgot Password</a>
        <a href="new_member.php" style="color: #002b7f">Member Registration</a>
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