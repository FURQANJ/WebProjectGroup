<?php
$status = $_GET['status'] ?? '';

if ($status === 'Approved') {
    $title = "Booking Approved";
    $message = "You have successfully approved this booking request.";
    $color = "#39d319";
} elseif ($status === 'Rejected') {
    $title = "Booking Rejected";
    $message = "You have successfully rejected this booking request.";
    $color = "#ff2a1a";
} else {
    $title = "Action Completed";
    $message = "The booking request has been updated.";
    $color = "#294797";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?></title>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #000;
      background: url('Main Background.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
    }

    header {
      width: 250px;
      min-height: 100vh;
      background: #d3d3d3;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 50px;
      flex-shrink: 0;
    }

    .logo img {
      width: 150px;
      height: auto;
      margin-bottom: 95px;
    }

    nav {
      width: 100%;
      margin-top: 20px;
    }

    nav ul {
      padding: 0;
      margin: 0;
      width: 100%;
      list-style: none;
    }

    nav ul li {
      width: 100%;
      margin-bottom: 5px;
    }

    nav ul li a {
      display: block;
      padding: 15px 25px;
      text-decoration: none;
      color: #000000;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    nav ul li a:hover {
      background-color: #c4c4c4;
      color: #000000;
      border-left: 4px solid #000000;
      padding-left: 30px;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .result-box {
      width: 520px;
      background: white;
      border-radius: 25px;
      border: 1px solid #ccc;
      text-align: center;
      padding: 45px 35px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: <?php echo $color; ?>;
      color: white;
      font-size: 42px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    h2 {
      margin: 0 0 15px;
      font-size: 24px;
    }

    p {
      font-size: 15px;
      margin-bottom: 30px;
    }

    .home-btn {
      display: inline-block;
      background: #294797;
      color: white;
      text-decoration: none;
      padding: 11px 25px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: bold;
    }

    .home-btn:hover {
      background: #1f3675;
    }
  </style>
</head>

<body>

<header>
  <div class="logo">
    <img src="UTeM Clear.png" alt="UTeM Logo">
  </div>
  <nav style="height: 70vh;">
    <ul style="display: flex; flex-direction: column; height: 100%; list-style: none; padding: 0; margin: 0;">
      <li><a href="AdminBookingLog.php">BOOKING LOG</a></li>
      <li><a href="AdminBookingRequest.php">BOOKING REQUESTS</a></li>
      <li><a href="AdminUpdate.php">COURT/EQUIPMENT UPDATE</a></li>
      <li style="margin-top: auto; padding-bottom: 20px;"><a href="index.php" style="color: #c62828;">LOGOUT</a></li>
    </ul>
  </nav>
</header>

<main>
  <div class="result-box">
    <div class="icon">
      <?php echo ($status === 'Rejected') ? '×' : '✓'; ?>
    </div>

    <h2><?php echo $title; ?></h2>
    <p><?php echo $message; ?></p>

    <a class="home-btn" href="AdminBookingRequest.php">
      Back to booking list
    </a>
  </div>
</main>

</body>
</html>