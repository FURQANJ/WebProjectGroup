<?php
session_start();
include "db.php";

$current_guest = $_SESSION['guest_id'] ?? null;
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

    table {
      width: 100%;
      background-color: #ffffff;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th,
    table td {
      border: 1px solid #bdc3c7;
      padding: 12px;
      text-align: center;
      font-size: 13px;
    }

    table th {
      background-color: #C0C0C0;
      color: #000;
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .status-approved {
      color: #2e7d32;
      font-weight: bold;
    }

    .status-rejected {
      color: #c62828;
      font-weight: bold;
    }

    .status-pending {
      color: #e67e22;
      font-weight: bold;
    }

    .rejection-reason {
      color: #c62828;
      font-size: 12px;
    }
  </style>
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

      <table>
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Phone Number</th>
            <th>Email</th>
            <th>Reason</th>
            <th>Court</th>
            <th>Date</th>
            <th>Time From</th>
            <th>Time To</th>
            <th>Equipment</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Rejection Reason</th>
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

                      $status = $row['booking_status'] ?? 'Pending';

                      if ($status === 'Approved') {
                          $status_class = 'status-approved';
                      } elseif ($status === 'Rejected') {
                          $status_class = 'status-rejected';
                      } else {
                          $status_class = 'status-pending';
                      }

                      if ($status === 'Rejected') {
                          $rejection_reason = htmlspecialchars($row['rejection_reason'] ?? '-');

                          if ($rejection_reason === '') {
                              $rejection_reason = '-';
                          }
                      } else {
                          $rejection_reason = '-';
                      }

                      echo "<tr>";
                      echo "<td>" . $name . "</td>";
                      echo "<td>" . $phone . "</td>";
                      echo "<td>" . $email . "</td>";
                      echo "<td>" . $reason . "</td>";
                      echo "<td>" . $court . "</td>";
                      echo "<td>" . $date . "</td>";
                      echo "<td>" . $timeFrom . "</td>";
                      echo "<td>" . $timeTo . "</td>";
                      echo "<td>" . $equipment . "</td>";
                      echo "<td>" . $quantity . "</td>";
                      echo "<td><span class='" . $status_class . "'>" . htmlspecialchars($status) . "</span></td>";
                      echo "<td><span class='rejection-reason'>" . $rejection_reason . "</span></td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='12'>No database booking records found for your account.</td></tr>";
              }

              $stmt->close();
          } else {
              echo "<tr><td colspan='12' style='color: #c62828;'>Please log in to view your reservation status.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </section>

</body>

</html>