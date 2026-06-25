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
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: #000;
      background: url('Main Background.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      min-height: 100vh;
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
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
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

    .form-centered {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }

    .container {
      width: 100%;
      max-width: 700px;
      background: rgba(255, 255, 255, 0.95);
      padding: 35px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      text-align: left;
    }

    h2 {
      margin: 0 0 25px 0;
      font-size: 24px;
      font-weight: bold;
      color: #000;
      text-transform: uppercase;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
    }

    .meta-text {
      font-size: 14px;
      margin-bottom: 12px;
      color: #000;
      text-align: left;
    }

    .meta-text strong {
      display: inline-block;
      width: 140px;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    label {
      display: block;
      font-size: 13px;
      font-weight: bold;
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    textarea,
    input[type="text"] {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 7px;
      box-sizing: border-box;
      font-size: 13px;
      background: #fff;
      color: #000;
    }

    textarea {
      height: 100px;
      resize: none;
      background-color: #f9f9f9;
    }

    .reason-display {
      width: 100%;
      padding: 15px;
      background-color: #f4f4f4;
      border: 1px solid #e0e0e0;
      border-radius: 7px;
      font-size: 14px;
      color: #333;
      line-height: 1.5;
      min-height: 80px;
      word-wrap: break-word;
    }

    .action-container {
      margin-top: 30px;
      border-top: 1px solid #ddd;
      padding-top: 20px;
    }

    .button-area {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      align-items: center;
    }

    form {
      margin: 0;
      display: inline-block;
    }

    .submit-btn {
      color: #ffffff;
      border: none;
      cursor: pointer;
      padding: 12px 30px;
      font-size: 14px;
      font-weight: bold;
      border-radius: 6px;
      transition: background-color 0.2s ease, transform 0.1s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 140px;
      height: 45px;
    }

    .submit-btn:active {
      transform: scale(0.98);
    }

    .submit-btn.approve {
      background-color: #39d319;
    }

    .submit-btn.approve:hover {
      background-color: #2da814;
    }

    .submit-btn.reject {
      background-color: #ff2a1a;
    }

    .submit-btn.reject:hover {
      background-color: #cc2215;
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
    <a href="AdminBookingRequest.php" class="back-btn">
      <img src="BackArrowButton.png" alt="Back" class="back-arrow-img">
    </a>

    <div class="form-centered">
      <div class="container">
        <h2>Booking Details</h2>

        <div class="meta-text"><strong>Booking ID:</strong> B<?php echo str_pad($booking['booking_id'], 4, "0", STR_PAD_LEFT); ?></div>
        <div class="meta-text"><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></div>
        <div class="meta-text"><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></div>
        <div class="meta-text"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
        <div class="meta-text"><strong>Court/Venue:</strong> <?php echo htmlspecialchars($court); ?></div>
        <div class="meta-text"><strong>Equipment:</strong> <?php echo htmlspecialchars($equipment); ?> x <?php echo htmlspecialchars($quantity); ?></div>
        <div class="meta-text" style="margin-bottom: 25px;"><strong>Date & Time:</strong> <?php echo htmlspecialchars($date . " (" . $timeFrom . " - " . $timeTo . ")"); ?></div>

        <div class="form-group">
          <label>Reason for Booking</label>
          <div class="reason-display">
            <?php echo nl2br(htmlspecialchars($reason)); ?>
          </div>
        </div>

        <div class="action-container">
          <div class="form-group" style="margin-bottom: 20px;">
            <label for="rejection_reason" style="color: #ad2218c0;">Rejection Reason (OPTIONAL)</label>
            <input type="text" id="rejection_reason" name="rejection_reason" form="rejectForm" placeholder="Type reason here...">
          </div>

          <div class="button-area">
            <form id="approveForm" action="UpdateBookingStatus.php" method="POST">
              <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
              <input type="hidden" name="status" value="Approved">
              <button class="submit-btn approve" type="submit">APPROVE</button>
            </form>

            <form id="rejectForm" action="UpdateBookingStatus.php" method="POST" onsubmit="return validateRejection()">
              <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
              <input type="hidden" name="status" value="Rejected">
              <button class="submit-btn reject" type="submit">REJECT</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </main>


</body>

</html>