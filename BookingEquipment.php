<?php
session_start();
include "db.php";

$current_guest = $_SESSION['guest_id'] ?? null;
$guest_name = $_SESSION['guest_name'] ?? 'Unknown User';

// ambik data court
$available_courts = [];
$court_query = mysqli_query($conn, "SELECT venue_details FROM venue WHERE venue_status = 'AVAILABLE'");
if ($court_query) {
  while ($row = mysqli_fetch_assoc($court_query)) {
    $available_courts[] = $row['venue_details'];
  }
}

// ambik data equipment yang lebih 0
$available_eq = [];
$eq_query = mysqli_query($conn, "SELECT equipment_details FROM equipment WHERE equipment_status = 'AVAILABLE' AND equipment_quantity > 0");
if ($eq_query) {
  while ($row = mysqli_fetch_assoc($eq_query)) {
    $available_eq[] = $row['equipment_details'];
  }
}

// ambik semua booking aktif utk hantar ke JS
$active_bookings = [];
$check_query = mysqli_query($conn, "SELECT booking_details FROM booking WHERE booking_status IN ('Pending', 'Approved')");
if ($check_query) {
  while ($row = mysqli_fetch_assoc($check_query)) {
    $details = explode("\t", $row['booking_details']);
    if (count($details) >= 8) {
      $active_bookings[] = [
        'court' => $details[4],
        'date' => $details[5],
        'from' => $details[6],
        'to' => $details[7]
      ];
    }
  }
}
$active_bookings_json = json_encode($active_bookings);

if (isset($_POST['submit'])) {
  if (!$current_guest) {
    echo "<script>alert('Sila log masuk terlebih dahulu sebelum membuat tempahan!'); window.location.href='index.php';</script>";
    exit();
  }

  $new_court = trim($_POST['court']);
  $new_date = trim($_POST['date']);
  $new_from = trim($_POST['timeFrom']);
  $new_to = trim($_POST['timeTo']);

  // Server-side fallback check
  $conflict_found = false;
  foreach ($active_bookings as $booking) {
    if ($booking['court'] === $new_court && $booking['date'] === $new_date) {
      if ($new_from < $booking['to'] && $new_to > $booking['from']) {
        $conflict_found = true;
        break;
      }
    }
  }

  if ($conflict_found) {
    echo "<script>alert('Maaf, slot masa ini telah ditempah oleh pengguna lain. Sila pilih masa atau court yang lain.');</script>";
  } else {
    // klau xde conflict, sambung je
    $data_fields = [
      $_POST['fullName'],
      $_POST['phoneNumber'],
      $_POST['email'],
      $_POST['reason'],
      $new_court,
      $new_date,
      $new_from,
      $new_to,
      $_POST['equipment'],
      $_POST['quantity']
    ];

    $sanitized_fields = array_map(function ($field) use ($conn) {
      return mysqli_real_escape_string($conn, trim($field));
    }, $data_fields);

    $booking_details_payload = implode("\t", $sanitized_fields);
    $default_status = "Pending";

    $stmt = $conn->prepare("INSERT INTO booking (booking_status, booking_details, guest_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $default_status, $booking_details_payload, $current_guest);

    if ($stmt->execute()) {
      echo "<script>alert('Tempahan berjaya dihantar!'); window.location.href='Approval.php';</script>";
    } else {
      echo "<script>alert('Gagal menyimpan tempahan.');</script>";
    }
    $stmt->close();
  }
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
      color: #000;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    nav ul li a:hover {
      background-color: #c4c4c4;
      border-left: 4px solid #000;
      padding-left: 30px;
    }

    main {
      flex: 1;
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
    }

    .form-centered {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
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
      transition: opacity 0.2s;
    }

    button:hover {
      background: #1f3675;
    }

    button:disabled {
      background: #ccc;
      cursor: not-allowed;
      opacity: 0.6;
    }

    .error-msg {
      color: #d32f2f;
      font-size: 11px;
      display: none;
      margin-top: 4px;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo"><img src="UTeM Clear.png" alt="UTeM Logo"></div>
    <nav style="height: 70vh;">
      <ul style="display: flex; flex-direction: column; height: 100%; list-style: none; padding: 0; margin: 0;">
        <li><a href="MainPage.html">BOOKING SPACE</a></li>
        <li><a href="Approval.php">APPROVAL/STATUS</a></li>
        <li style="margin-top: auto; padding-bottom: 20px;"><a href="index.php" style="color: #c62828;">LOGOUT</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <a href="MainPage.html" class="back-btn"><img src="BackArrowButton.png" alt="Back" class="back-arrow-img"></a>
    <div class="form-centered">
      <div id="formSection">
        <div class="form-title">Booking Space & Equipment</div>

        <form action="" method="post" id="bookingForm">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($guest_name); ?>" readonly style="background-color: #e9ecef; cursor: not-allowed; color: #495057;" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>No. H/P</label>
              <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="01xxxxxxx" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" id="email" name="email" placeholder="student@utem.edu.my" required>
            </div>
          </div>

          <div class="form-group">
            <label>Reason for Booking</label>
            <textarea id="reason" name="reason" placeholder="State your purpose here..." required></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Court</label>
              <select id="court" name="court" required>
                <option value="" disabled selected>Choose Court</option>
                <?php foreach ($available_courts as $court): ?>
                  <option value="<?php echo htmlspecialchars($court); ?>"><?php echo htmlspecialchars($court); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Date</label>
              <input type="date" id="dateChoose" name="date" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Time Slot ( From )</label>
              <input type="time" id="apptFrom" name="timeFrom" required>
            </div>
            <div class="form-group">
              <label>Time Slot ( To )</label>
              <input type="time" id="apptTo" name="timeTo" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group" style="flex: 2;">
              <label>Equipment</label>
              <select id="equipment" name="equipment" required>
                <option value="" disabled selected>Choose Equipment</option>
                <?php foreach ($available_eq as $eq): ?>
                  <option value="<?php echo htmlspecialchars($eq); ?>"><?php echo htmlspecialchars($eq); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group" style="flex: 1;">
              <label>Quantity</label>
              <input type="number" id="QtyInput" name="quantity" min="1" max="5" placeholder="0" required>
            </div>
          </div>

          <span id="liveErrorMsg" class="error-msg">Please resolve errors to enable submission.</span>
          <div class="button-row">
            <button type="submit" name="submit" id="submitBtn" disabled>Submit Booking</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script>
    const activeBookings = <?php echo $active_bookings_json; ?>;

    document.addEventListener("DOMContentLoaded", function() {
      const form = document.getElementById("bookingForm");
      const submitBtn = document.getElementById("submitBtn");
      const errorMsg = document.getElementById("liveErrorMsg");

      const dateInput = document.getElementById("dateChoose");
      const timeFrom = document.getElementById("apptFrom");
      const timeTo = document.getElementById("apptTo");
      const courtSelect = document.getElementById("court");
      const eqSelect = document.getElementById("equipment");
      const qtyInput = document.getElementById("QtyInput");

      // kira date
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const todayStr = `${year}-${month}-${day}`;

      dateInput.setAttribute("min", todayStr);

      // auto correct klau user type tarikh yg dah lepas
      dateInput.addEventListener("change", function() {
        if (this.value && this.value < todayStr) {
          this.value = todayStr;
        }
      });

      // kaitkan court dgn equipment
      const equipmentMap = {
        "Tennis Court (1)": "Tennis Ball",
        "Tennis Court (2)": "Tennis Ball",
        "Badminton Court (1)": "Badminton Shuttlecock",
        "Badminton Court (2)": "Badminton Shuttlecock",
        "Basketball Court (1)": "BasketBall Ball",
        "Basketball Court (2)": "BasketBall Ball",
        "Futsal Court (1)": "Futsal Ball",
        "Futsal Court (2)": "Futsal Ball",
        "Football Field": "FootBall Ball",
        "Rugby Field": "Rugby Ball"
      };

      // filter equipment ikut name court
      courtSelect.addEventListener("change", function() {
        const requiredEq = equipmentMap[this.value];
        let foundMatch = false;

        Array.from(eqSelect.options).forEach(opt => {
          if (opt.value === "" || opt.value === requiredEq) {
            opt.style.display = "block";
            opt.disabled = false;
            if (opt.value === requiredEq) foundMatch = true;
          } else {
            opt.style.display = "none";
            opt.disabled = true;
          }
        });

        eqSelect.value = foundMatch ? requiredEq : "";
        validateLive();
      });

      // logic time constraint + auto block
      function updateTimeConstraints() {
        if (dateInput.value === todayStr) {
          const currentTime = new Date();
          const currentStr = String(currentTime.getHours()).padStart(2, '0') + ':' + String(currentTime.getMinutes()).padStart(2, '0');

          timeFrom.setAttribute("min", currentStr);

          // auto correct to current time kalau user try nak bypass minimum
          if (timeFrom.value && timeFrom.value < currentStr) {
            timeFrom.value = currentStr;
          }
        } else {
          timeFrom.removeAttribute("min");
        }

        if (timeFrom.value) {
          timeTo.setAttribute("min", timeFrom.value);

          // block 'To' time klau set sebelum 'From' time
          if (timeTo.value && timeTo.value <= timeFrom.value) {
            timeTo.value = "";
          }
        }
        validateLive();
      }

      dateInput.addEventListener("change", updateTimeConstraints);
      timeFrom.addEventListener("change", updateTimeConstraints);
      timeTo.addEventListener("change", updateTimeConstraints);

      // validation utk UI button toggle and real-time check
      function validateLive() {
        let isValid = true;
        let errorTxt = "";

        // nak check required inputs
        Array.from(form.elements).forEach(input => {
          if (input.required && !input.value.trim()) isValid = false;
        });

        const selCourt = courtSelect.value;
        const selDate = dateInput.value;
        const selFrom = timeFrom.value;
        const selTo = timeTo.value;

        // nak check time overlap dgn duration
        if (selFrom && selTo) {
          if (selTo <= selFrom) {
            isValid = false;
            errorTxt = "End time must be after Start time.";
          } else {
            // convert time ke minutes
            const startParts = selFrom.split(':');
            const endParts = selTo.split(':');
            const startTotalMins = (parseInt(startParts[0]) * 60) + parseInt(startParts[1]);
            const endTotalMins = (parseInt(endParts[0]) * 60) + parseInt(endParts[1]);

            if ((endTotalMins - startTotalMins) > 180) { // 180 minutes = 3 hours
              isValid = false;
              errorTxt = "Booking cannot exceed 3 hours.";
            }
          }
        }

        // check quantity
        if (qtyInput.value) {
          const q = parseInt(qtyInput.value);
          if (q < 1 || q > 5) {
            isValid = false;
            errorTxt = "Quantity must be between 1 and 5.";
          }
        }

        // check ngan banding dgn booking yg dah ada
        if (selCourt && selDate && selFrom && selTo) {
          for (let i = 0; i < activeBookings.length; i++) {
            let b = activeBookings[i];
            if (b.court === selCourt && b.date === selDate) {
              if (selFrom < b.to && selTo > b.from) {
                isValid = false;
                errorTxt = `Slot already taken! Overlaps with existing booking from ${b.from} to ${b.to}.`;
                break;
              }
            }
          }
        }

        submitBtn.disabled = !isValid;
        errorMsg.style.display = (errorTxt && !isValid) ? "block" : "none";
        errorMsg.innerText = errorTxt;
      }

      // smbung live listener dkt semua input
      Array.from(form.elements).forEach(input => {
        input.addEventListener("input", validateLive);
        input.addEventListener("change", validateLive);
      });
    });
  </script>
</body>

</html>