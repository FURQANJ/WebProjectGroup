<?php
include "db.php";

$sql = "SELECT * FROM booking WHERE booking_status = 'Pending' ORDER BY booking_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Requests</title>

  <style>
    * {
      box-sizing: border-box;
    }

    body 
    {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #000;
      background: url('Main Background.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      min-height: 100vh;
    }

    header 
    {
      width: 250px;
      min-height: 100vh;
      background: #d3d3d3;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 50px;
      flex-shrink: 0;
    }

    .logo img 
    {
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
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
    }

    h2 {
      margin-bottom: 25px;
    }

    /* Synchronized Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }

    table th {
      background: #d9d9d9;
      padding: 15px;
      border: 1px solid #cfcfcf;
      text-align: left;
    }

    table td {
      padding: 15px;
      border: 1px solid #cfcfcf;
    }

    table tr:hover {
      background: #f5f5f5;
    }

    .back-btn {
        display: block;
        text-align: left;
        margin-bottom: 15px;
        transition: transform 0.2s ease;
        width: max-content;
    }

    .back-btn:hover {
        transform: scale(1.05);
    }

    .back-arrow-img {
        width: 35px;
        height: auto;
        vertical-align: middle;
    }

    .view-btn {
        display: inline-block;
        padding: 5px 10px;
        background: #294797;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
    }

    .view-btn:hover {
        background: #1f3675;
    }

    .empty {
        background: white;
        padding: 20px;
        border: 1px solid #cfcfcf;
        text-align: center;
        font-weight: bold;
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
    <div class="container">
        <a href="adminhome.php" class="back-btn">
            <img src="BackArrowButton.png" alt="Back" class="back-arrow-img">
        </a>
    </div>
    
    <h2>Booking Requests</h2>

    <?php if ($result->num_rows > 0) { ?>
      <table>
        <tr>
          <th>Booking ID</th>
          <th>Name</th>
          <th>Number</th>
          <th>Court</th>
          <th>Equipment</th>
          <th>Date</th>
          <th>Time</th>
          <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { 
          $details = explode("\t", $row['booking_details']);

          $name = $details[0] ?? '';
          $phone = $details[1] ?? '';
          $court = $details[4] ?? '';
          $date = $details[5] ?? '';
          $timeFrom = $details[6] ?? '';
          $timeTo = $details[7] ?? '';
          $equipment = $details[8] ?? '';
        ?>

        <tr>
          <td>B<?php echo str_pad($row['booking_id'], 4, "0", STR_PAD_LEFT); ?></td>
          <td><?php echo htmlspecialchars($name); ?></td>
          <td><?php echo htmlspecialchars($phone); ?></td>
          <td><?php echo htmlspecialchars($court); ?></td>
          <td><?php echo htmlspecialchars($equipment); ?></td>
          <td><?php echo htmlspecialchars($date); ?></td>
          <td><?php echo htmlspecialchars($timeFrom . " - " . $timeTo); ?></td>
          <td>
            <a class="view-btn" href="AdminBookingDetails.php?id=<?php echo $row['booking_id']; ?>">
              View
            </a>
          </td>
        </tr>

        <?php } ?>
      </table>
    <?php } else { ?>
      <div class="empty">No pending booking requests.</div>
    <?php } ?>
</main>

</body>
</html>