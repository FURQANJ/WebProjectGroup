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
    body 
    {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('Main Background.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      min-height: 100vh;
    }

    header 
    {
      width: 250px;
      background: #d3d3d3;
      padding-top: 50px;
      text-align: center;
    }

    .logo img 
    {
      width: 150px;
      margin-bottom: 80px;
    }

    nav a 
    {
      display: block;
      padding: 15px 25px;
      text-decoration: none;
      color: black;
      font-weight: bold;
      font-size: 14px;
      text-align: left;
    }

    nav a:hover 
    {
      background: #c4c4c4;
    }

    main {
      flex: 1;
      padding: 60px 40px;
    }

    h2 {
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 12px;
      font-size: 13px;
      text-align: left;
    }

    th {
      background: #f0f0f0;
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

  </style>
</head>

<body>

<header>
  <div class="logo">
    <img src="UTeM Clear.png" alt="UTeM Logo">
  </div>
</header>

<main>

    <div class="container">
            <a href="adminhome.html" class="back-btn">
                <img src="BackArrowButton.png" alt="Back" class="back-arrow-img">
            </a>
  <h2>BOOKING REQUESTS</h2>

  <?php if ($result->num_rows > 0) { ?>
    <table>
      <tr>
        <th>Booking ID</th>
        <th>Name / Organization</th>
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
          <a class="view-btn" href="adminBookingDetails.php?id=<?php echo $row['booking_id']; ?>">
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