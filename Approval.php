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
      flex-shrink: 0; /* Mengelakkan sidebar mengecil */
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

    /* GAYA CSS DARIPADA KAWAN ANDA */
    .container {
      width: 95%;
      max-width: 1400px;
      margin: auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    table {
      width: 100%;
      background-color: #ffffff;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th, table td {
      border: 1px solid #bdc3c7;
      padding: 12px;
      text-align: center;
    }

    table th {
      background-color: #C0C0C0;
      color: #000;
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
  
  <!-- Menu Sidebar Anda -->
  <header>
    <div class="logo">
      <img src="UTeM Clear.png" alt="Pusat Sukan Logo">
    </div>
    <nav>
      <ul>
        <li><a href="MainPage.html">BOOKING SPACE</a></li>
        <li><a href="Approval.php">APPROVAL/STATUS</a></li>
      </ul>
    </nav>
  </header>

  <!-- Bahagian Kandungan Utama (Gabungan kawan anda) -->
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
            <th>Status</th> <!-- Ditambah jika perlu status padanan kod kawan anda -->
          </tr>
        </thead>
        <tbody>
          <?php
          // Membuka dan membaca fail details.txt secara selamat
          if (file_exists("details.txt")) {
              $fp = fopen("details.txt", "r") or die("Couldn’t open the file");

              while (!feof($fp)) {
                  $data = fgets($fp, 1024);
                  $values = chop($data);
                  
                  // Elak baris kosong di akhir fail daripada create row kosong
                  if (empty($values)) continue; 
                  
                  $val = explode("\t", $values); 

                  // Mengendalikan kes jika data equipment/quantity kosong supaya tak error
                  $equipment = (!isset($val[8]) || trim($val[8]) === '') ? '-' : htmlspecialchars($val[8]);
                  $quantity  = (!isset($val[9]) || trim($val[9]) === '') ? '-' : htmlspecialchars($val[9]);

                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($val[0] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[1] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[2] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[3] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[4] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[5] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[6] ?? '-') . "</td>";
                  echo "<td>" . htmlspecialchars($val[7] ?? '-') . "</td>";
                  echo "<td>" . $equipment . "</td>"; // Menggunakan sempang jika null
                  echo "<td>" . $quantity . "</td>";  // Menggunakan sempang jika null
                  echo "<td><strong style='color: #e67e22;'>Pending</strong></td>"; 
                  echo "</tr>";
              }
              fclose($fp);
          } else {
              echo "<tr><td colspan='11'>No booking records found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </section>

</body>
</html>