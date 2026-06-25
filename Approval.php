<?php
session_start();
include "db.php";

$current_guest = $_SESSION['guest_id'] ?? null;

// utk cancelling
if (isset($_POST['cancel_booking_id']) && $current_guest) {
    $cancel_id = intval($_POST['cancel_booking_id']);

    // pastikan booking yg pending or approved tu adalah user punya
    $check_stmt = $conn->prepare("SELECT booking_status FROM booking WHERE booking_id = ? AND guest_id = ?");
    $check_stmt->bind_param("is", $cancel_id, $current_guest);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result && $check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        if ($row['booking_status'] === 'Pending' || $row['booking_status'] === 'Approved') {
            $update_stmt = $conn->prepare("UPDATE booking SET booking_status = 'Cancelled', rejection_reason = 'Cancelled by user' WHERE booking_id = ?");
            $update_stmt->bind_param("i", $cancel_id);
            if ($update_stmt->execute()) {
                echo "<script>alert('Tempahan berjaya dibatalkan.'); window.location.href='Approval.php';</script>";
                exit();
            } else {
                echo "<script>alert('Gagal membatalkan tempahan. Sila cuba lagi.');</script>";
            }
            $update_stmt->close();
        } else {
            echo "<script>alert('Hanya tempahan berstatus Pending atau Approved boleh dibatalkan.');</script>";
        }
    } else {
        echo "<script>alert('Tempahan tidak sah.');</script>";
    }
    $check_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Pusat Sukan Court Booking - Approval</title>
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

    section {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .container {
      width: 95%;
      max-width: 1400px;
      margin: auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    h2 {
      text-align: left;
      margin-top: 0;
      margin-bottom: 25px;
      text-transform: uppercase;
    }

    .booking-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .booking-table th {
        background: #d9d9d9;
        padding: 15px;
        border: 1px solid #cfcfcf;
        text-align: center; 
        font-weight: bold;
        font-size: 13px;
        text-transform: uppercase;
    }

    .booking-table td {
        padding: 15px;
        border: 1px solid #cfcfcf;
        font-size: 13px;
        text-align: center; 
    }

    .booking-table tr:hover {
        background: #f5f5f5;
    }

    .status-approved {
      color: #2e7d32;
      font-weight: bold;
    }

    .status-rejected, .status-cancelled {
      color: #c62828;
      font-weight: bold;
    }

    .status-pending {
      color: #d68910; 
      font-weight: bold;
    }

    .rejection-reason {
      color: #c62828;
      font-size: 12px;
    }

    .cancel-btn {
      background-color: #c62828;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      font-size: 12px;
      transition: background-color 0.2s;
    }

    .cancel-btn:hover {
      background-color: #a02020;
    }
  </style>
  <script>
    function confirmCancellation(form) {
      if(confirm("Adakah anda pasti mahu membatalkan tempahan ini?")) {
        form.submit();
      }
    }
  </script>
</head>

<body>

  <header>
    <div class="logo">
      <img src="UTeM Clear.png" alt="Pusat Sukan Logo">
    </div>

    <nav>
      <ul>
        <li><a href="MainPage.html">BOOKING SPACE</a></li>
        <li><a href="Approval.php">APPROVAL/STATUS</a></li>
        <li style="margin-top: 400px;"><a href="index.php" style="color: #c62828;">LOGOUT</a></li>
      </ul>
    </nav>
  </header>

  <section>
    <div class="container">
      <h2>APPROVAL / STATUS</h2>

      <table class="booking-table">
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Court</th>
            <th>Equipment</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Notes/Reason</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php
          if ($current_guest) {
              $stmt = $conn->prepare("
                  SELECT booking_id, booking_status, booking_details, guest_id, rejection_reason
                  FROM booking
                  WHERE guest_id = ?
                  ORDER BY booking_id DESC
              ");

              $stmt->bind_param("s", $current_guest);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      $details_string = $row['booking_details'] ?? '';
                      $val = explode("\t", $details_string);

                      $booking_id = $row['booking_id'];
                      $name      = htmlspecialchars($val[0] ?? '-');
                      $phone     = htmlspecialchars($val[1] ?? '-');
                      $email     = htmlspecialchars($val[2] ?? '-');
                      $reason    = htmlspecialchars($val[3] ?? '-'); 
                      $court     = htmlspecialchars($val[4] ?? '-');
                      $date      = htmlspecialchars($val[5] ?? '-');
                      $timeFrom  = htmlspecialchars($val[6] ?? '-');
                      $timeTo    = htmlspecialchars($val[7] ?? '-');
                      $equipment = (!isset($val[8]) || trim($val[8]) === '') ? '-' : htmlspecialchars($val[8]);
                      $quantity  = (!isset($val[9]) || trim($val[9]) === '') ? '-' : htmlspecialchars($val[9]);

                      $timeDisplay = $timeFrom . " - " . $timeTo;
                      $equipmentDisplay = ($equipment !== '-' && $equipment !== 'N/A') ? $equipment . " (x" . $quantity . ")" : "-";

                      $status = $row['booking_status'] ?? 'Pending';

                      if ($status === 'Approved') {
                          $status_class = 'status-approved';
                      } elseif ($status === 'Rejected') {
                          $status_class = 'status-rejected';
                      } elseif ($status === 'Cancelled') {
                          $status_class = 'status-cancelled';
                      } else {
                          $status_class = 'status-pending';
                      }

                      $rejection_reason = '-';
                      if ($status === 'Rejected' || $status === 'Cancelled') {
                          $raw_reason = $row['rejection_reason'] ?? '';
                          if (trim($raw_reason) !== '') {
                              $rejection_reason = htmlspecialchars($raw_reason);
                          }
                      }

                      echo "<tr>";
                      echo "<td>B" . str_pad($booking_id, 4, '0', STR_PAD_LEFT) . "</td>";
                      echo "<td>" . $name . "</td>";
                      echo "<td>" . $phone . "</td>";
                      echo "<td>" . $email . "</td>";
                      echo "<td>" . $court . "</td>";
                      echo "<td>" . $equipmentDisplay . "</td>";
                      echo "<td>" . $date . "</td>";
                      echo "<td>" . $timeDisplay . "</td>";
                      echo "<td><span class='" . $status_class . "'>" . htmlspecialchars($status) . "</span></td>";
                      echo "<td><span class='rejection-reason'>" . $rejection_reason . "</span></td>";
                      
                      echo "<td>";
                      if ($status === 'Pending' || $status === 'Approved') {
                          echo "<form method='POST' style='margin:0;' onsubmit='event.preventDefault(); confirmCancellation(this);'>
                                  <input type='hidden' name='cancel_booking_id' value='" . $booking_id . "'>
                                  <button type='submit' class='cancel-btn'>Cancel</button>
                                </form>";
                      } else {
                          echo "-";
                      }
                      echo "</td>";
                      
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='11' style='text-align: center; font-style: italic;'>No database booking records found for your account.</td></tr>";
              }

              $stmt->close();
          } else {
              echo "<tr><td colspan='11' style='text-align: center; color: #c62828;'>Please log in to view your reservation status.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </section>

</body>

</html>