<?php
ob_start(); 

$users = [
    "FurqanStudent" => ["password" => "bijak", "role" => "student"],
    "FurqanAdmin"   => ["password" => "bagus", "role" => "admin"]
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (isset($users[$userId]) && $users[$userId]['password'] === $password) {
        if ($users[$userId]['role'] === $role) {
            if ($role == "student") {
                header("Location: MainPage.html"); // Booking Space
                exit();
            } elseif ($role == "admin") {
                header("Location: adminhome.html"); // Approval
                exit();
            }
        } else {
            echo "<script>alert('Role tidak padan dengan akaun!');</script>";
        }
    } else {
        echo "<script>alert('User ID atau Password salah!');</script>";
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
  <!-- Header -->
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

  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-box">
      <button type="button" class="popup-close" onclick="closePopup()">X</button>
      <h2 id="popupTitle"></h2>
      <div id="popupContent"></div>
    </div>
  </div>

  <!-- Main Login Section -->
  <main>
    <div class="login-box">
      <h2>WELCOME TO PUSAT SUKAN UTEM</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="text" name="userId" placeholder="User ID" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="role" value="student" class="student-btn">Login For Student</button>
        <button type="submit" name="role" value="admin" class="admin-btn">Login For Admin</button>
      </form>
      <div class="links">
        <a href="#">Forgot Password</a>
        <a href="#">New Member Registration</a>
      </div>
    </div>
  </main>




  <!-- Footer -->
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
