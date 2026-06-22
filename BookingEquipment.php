<?php
session_start();
include "db.php";


$current_guest = $_SESSION['guest_id'] ?? null;

if (isset($_POST['submit'])) {
    if (!$current_guest) {
        echo "<script>alert('Sila log masuk terlebih dahulu sebelum membuat tempahan!'); window.location.href='index.php';</script>";
        exit();
    }


    $data_fields = [
        $_POST['fullName'],
        $_POST['phoneNumber'],
        $_POST['email'],
        $_POST['reason'],
        $_POST['court'],
        $_POST['date'],
        $_POST['timeFrom'],
        $_POST['timeTo'],
        $_POST['equipment'],
        $_POST['quantity']
    ];

    $sanitized_fields = array_map(function($field) use ($conn) {
        return mysqli_real_escape_string($conn, trim($field));
    }, $data_fields);
    
    $booking_details_payload = implode("\t", $sanitized_fields);
    $default_status = "Pending";

    $stmt = $conn->prepare("INSERT INTO booking (booking_status, booking_details, guest_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $default_status, $booking_details_payload, $current_guest);
    
    if ($stmt->execute()) {
        echo "<script>alert('Tempahan berjaya dihantar!'); window.location.href='Approval.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan tempahan ke dalam pangkalan data.');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Booking Space - UTeM</title>

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

    #formSection {
      width: 650px;
      min-height: 550px;
      background: #fff;
      border: 1px solid #cfcfcf;
      border-radius: 30px;
      overflow: hidden;
      box-shadow: 0 0 4px rgba(0, 0, 0, 0.15);
    }

    .form-title {
      background: #d9d9d9;
      padding: 12px 28px;
      font-size: 18px;
      font-weight: bold;
    }

    form {
      padding: 30px 28px 25px;
    }

    .form-group {
      margin-bottom: 18px;
      text-align: left;
    }

    .form-row {
      display: flex;
      gap: 25px;
      margin-bottom: 0;
    }

    .form-row .form-group {
      flex: 1;
    }

    label {
      display: block;
      margin-bottom: 7px;
      font-size: 13px;
      font-weight: 600;
    }

    input,
    textarea,
    select {
      width: 100%;
      border: 1px solid #ddd;
      border-radius: 7px;
      padding: 8px 12px;
      font-size: 13px;
      font-family: Arial, sans-serif;
    }

    input {
      height: 35px;
    }

    textarea {
      height: 90px;
      resize: none;
    }

    select {
      height: 35px;
      background: #fff;
    }

    .section-divider {
      border-top: 1px solid #eee;
      margin: 25px 0 20px 0;
    }

    .button-row {
      margin-top: 25px;
      display: flex;
      justify-content: flex-end;
    }

    button {
      border: none;
      border-radius: 6px;
      background: #294797;
      color: #fff;
      padding: 10px 25px;
      font-size: 13px;
      cursor: pointer;
      font-weight: bold;
    }

    button:hover {
      background: #1f3675;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">
      <img src="UTeM Clear.png" alt="UTeM Logo">
    </div>

    <nav>
      <ul>
        <li><a href="MainPage.html">BOOKING SPACE</a></li>
        <li><a href="Approval.php">APPROVAL/STATUS</a></li>
        <li style="margin-top: 400px;"><a href="index.php" style="color: #c62828;">LOGOUT</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div id="formSection">
      <div class="form-title">Booking Space</div>

      <form action="" method="post" onsubmit="return validateForm();">

        <div class="form-group">
          <label for="fullName">Full Name</label>
          <input type="text" id="fullName" name="fullName" placeholder="Your full name">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phoneNumber">No. H/P</label>
            <input
              type="tel"
              id="phoneNumber"
              name="phoneNumber"
              placeholder="01xxxxxxx"
              inputmode="numeric"
              pattern="[0-9]*"
              oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="student@utem.edu.my">
          </div>
        </div>

        <div class="form-group">
          <label for="reason">Reason for Booking</label>
          <textarea id="reason" name="reason" placeholder="State your purpose here..."></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="court">Court</label>
            <select id="court" name="court">
              <option value="" disabled selected>Choose Court</option>
              <option value="Tennis (1)">Tennis Court (1)</option>
              <option value="Tennis (2)">Tennis Court (2)</option>
              <option value="Badminton (1)">Badminton Court (1)</option>
              <option value="Badminton (2)">Badminton Court (2)</option>
              <option value="Basketball (1)">Basketball Court (1)</option>
              <option value="Basketball (2)">Basketball Court (2)</option>
              <option value="Futsal (1)">Futsal Court (1)</option>
              <option value="Futsal (2)">Futsal Court (2)</option>
              <option value="Football">Football Field</option>
              <option value="Rugby">Rugby Field</option>
            </select>
          </div>

          <div class="form-group">
            <label for="dateChoose">Date</label>
            <input type="date" id="dateChoose" name="date">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="apptFrom">Time Slot ( From )</label>
            <input type="time" id="apptFrom" name="timeFrom">
          </div>

          <div class="form-group">
            <label for="apptTo">Time Slot ( To )</label>
            <input type="time" id="apptTo" name="timeTo">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="flex: 2;">
            <label for="equipment">Equipment</label>
            <select id="equipment" name="equipment">
              <option value="" disabled selected>Choose Equipment</option>
              <option value="Tennis Ball">Tennis Ball</option>
              <option value="Badminton Shuttlecock">Badminton Shuttlecock</option>
              <option value="BasketBall Ball">Basketball Ball</option>
              <option value="Futsal Ball">Futsal Ball</option>
              <option value="FootBall Ball">Football Ball</option>
              <option value="Rugby Ball">Rugby Ball</option>
            </select>
          </div>

          <div class="form-group" style="flex: 1;">
            <label for="QtyInput">Quantity</label>
            <input type="number" id="QtyInput" name="quantity" min="1" max="5" placeholder="0">
          </div>
        </div>

        <div class="button-row">
          <button type="submit" name="submit">Submit Booking</button>
        </div>

      </form>
    </div>
  </main>

  <script>
    function validateForm() {
      const court = document.getElementById("court").value;
      const dateChoose = document.getElementById("dateChoose").value;
      const apptFrom = document.getElementById("apptFrom").value;
      const apptTo = document.getElementById("apptTo").value;
      const equipment = document.getElementById("equipment").value;
      const quantity = document.getElementById("QtyInput").value;

      const fullName = document.getElementById("fullName").value.trim();
      const phoneNumber = document.getElementById("phoneNumber").value.trim();
      const email = document.getElementById("email").value.trim();
      const reason = document.getElementById("reason").value.trim();

      if (!court || !dateChoose || !apptFrom || !apptTo || !equipment || !quantity || !fullName || !phoneNumber || !email || !reason) {
        alert("Sila isi semua maklumat tempahan dan peribadi sebelum menghantar.");
        return false;
      }

      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const selectedDate = new Date(dateChoose);
      selectedDate.setHours(0, 0, 0, 0);

      if (selectedDate < today) {
        alert("Anda tidak boleh memilih tarikh yang telah berlalu.");
        return false;
      }

      const startTime = new Date(`${dateChoose}T${apptFrom}`);
      const endTime = new Date(`${dateChoose}T${apptTo}`);

      if (endTime <= startTime) {
        alert("Masa tamat mestilah selepas masa mula.");
        return false;
      }

      const differenceInHours = (endTime - startTime) / (1000 * 60 * 60);
      if (differenceInHours > 3) {
        alert("Masa tempahan tidak boleh melebihi 3 jam.");
        return false;
      }

      if (quantity < 1 || quantity > 5) {
        alert("Kuantiti peralatan mestilah antara 1 hingga 5 buah.");
        return false;
      }
    }
  </script>
</body>
</html>