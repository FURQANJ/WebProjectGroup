<?php
include "db.php";

if (!isset($_GET['id'])) {
    header("Location: adminBookingRequests.php");
    exit();
}

$booking_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM booking WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Booking not found.";
    exit();
}

$booking = $result->fetch_assoc();
$details = explode("\t", $booking['booking_details']);

$name = $details[0] ?? '';
$phone = $details[1] ?? '';
$email = $details[2] ?? '';
$reason = $details[3] ?? '';
$court = $details[4] ?? '';
$date = $details[5] ?? '';
$timeFrom = $details[6] ?? '';
$timeTo = $details[7] ?? '';
$equipment = $details[8] ?? '';
$quantity = $details[9] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Details</title>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('Main Background.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      min-height: 100vh;
    }

    header {
      width: 250px;
      background: #d3d3d3;
      padding-top: 50px;
      text-align: center;
    }

    .logo img {
      width: 150px;
      margin-bottom: 80px;
    }

    nav a {
      display: block;
      padding: 15px 25px;
      text-decoration: none;
      color: black;
      font-weight: bold;
      font-size: 14px;
      text-align: left;
    }

    nav a:hover {
      background: #c4c4c4;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .card {
      width: 650px;
      background: white;
      border-radius: 25px;
      overflow: hidden;
      border: 1px solid #ccc;
    }

    .card-title {
      background: #d9d9d9;
      padding: 14px 28px;
      font-size: 20px;
      font-weight: bold;
    }

    .card-body {
      padding: 25px 28px;
    }

    p {
      margin: 0 0 13px;
      font-size: 14px;
    }

    label {
      font-size: 13px;
      font-weight: bold;
    }

    textarea {
      width: 100%;
      height: 110px;
      border: 1px solid #ddd;
      border-radius: 7px;
      padding: 10px;
      resize: none;
      margin-top: 7px;
      font-family: Arial, sans-serif;
    }

    .button-area {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 25px;
    }

    button {
      border: none;
      border-radius: 20px;
      padding: 10px 25px;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    .approve {
      background: #39d319;
    }

    .reject {
      background: #ff2a1a;
    }

    .back {
      background: #294797;
      color: white;
      padding: 8px 15px;
      text-decoration: none;
      border-radius: 5px;
      font-size: 13px;
      display: inline-block;
      margin-bottom: 15px;
    }

    .reject-box {
      margin-top: 20px;
    }

    input {
      width: 100%;
      height: 35px;
      border: 1px solid #ddd;
      border-radius: 7px;
      padding: 8px 12px;
      margin-top: 7px;
    }
  </style>
</head>

<body>

<header>
  <div class="logo">
    <img src="UTeM Clear.png" alt="UTeM Logo">
  </div>

  <nav>
    <a href="adminBookingRequests.php">BOOKING REQUESTS</a>
    <a href="index.php" style="color:#c62828;">LOGOUT</a>
  </nav>
</header>

<main>
  <div class="card">
    <div class="card-title">Booking Details</div>

    <div class="card-body">
      <a class="back" href="adminBookingRequests.php">Back</a>

      <p>B<?php echo str_pad($booking['booking_id'], 4, "0", STR_PAD_LEFT); ?></p>
      <p><?php echo htmlspecialchars($name); ?></p>
      <p><?php echo htmlspecialchars($phone); ?></p>
      <p><?php echo htmlspecialchars($email); ?></p>
      <p><?php echo htmlspecialchars($court); ?></p>
      <p><?php echo htmlspecialchars($equipment); ?> x <?php echo htmlspecialchars($quantity); ?></p>
      <p><?php echo htmlspecialchars($date . " (" . $timeFrom . " - " . $timeTo . ")"); ?></p>

      <label>Reason</label>
      <textarea readonly><?php echo htmlspecialchars($reason); ?></textarea>

      <div class="button-area">
        <form action="updateBookingStatus.php" method="POST">
          <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
          <input type="hidden" name="status" value="Approved">
          <button class="approve" type="submit">APPROVE</button>
        </form>
      </div>

      <div class="reject-box">
        <form action="updateBookingStatus.php" method="POST">
          <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
          <input type="hidden" name="status" value="Rejected">

          <label>Rejection Reason</label>
          <input type="text" name="rejection_reason" placeholder="Type reason here" required>

          <div class="button-area">
            <button class="reject" type="submit">REJECT</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

</body>
</html>