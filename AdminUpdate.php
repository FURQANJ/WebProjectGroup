<?php
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Equipment/Court</title>
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
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            text-align: left;
        }

        h2 {
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: bold;
            color: #000;
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

        .search-container {
            text-align: left;
            margin-bottom: 25px;
        }

        .search-container input {
            width: 300px;
            height: 35px;
            border: 1px solid #ccc;
            border-radius: 7px;
            padding: 8px 12px;
            font-size: 13px;
            background: white;
        }

        /* Synchronized Table Styling */
        table {
            width: 100%;
            background-color: #ffffff;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: #d9d9d9;
            padding: 15px;
            border: 1px solid #cfcfcf;
            text-align: left;
            color: #000;
            font-weight: bold;
        }

        table td {
            padding: 15px;
            border: 1px solid #cfcfcf;
            text-align: left;
        }

        table tr:hover {
            background: #f5f5f5;
        }

        .item-link {
            color: #294797;
            text-decoration: none;
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
            <a href="adminhome.html" class="back-btn">
                <img src="BackArrowButton.png" alt="Back" class="back-arrow-img">
            </a>

            <h2>Update Equipment/Court</h2>

            <div class="search-container">
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Name..">
            </div>

            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>Equipment/Court ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Availability Status</th>
                        <th>Last Updated Date</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT e.equipment_id AS id, e.equipment_details AS details, 'EQUIPMENT' AS type, e.equipment_quantity AS quantity, e.equipment_status AS status, m.timestamp AS last_date, a.admin_name AS updated_by
                            FROM equipment e
                            LEFT JOIN maintenance m ON m.equipment_id = e.equipment_id AND m.maintenance_id = (SELECT MAX(maintenance_id) FROM maintenance WHERE equipment_id = e.equipment_id)
                            LEFT JOIN admin a ON m.admin_id = a.admin_id
                            
                            UNION
                            
                            SELECT v.venue_id AS id, v.venue_details AS details, 'COURT' AS type, 1 AS quantity, v.venue_status AS status, m.timestamp AS last_date, a.admin_name AS updated_by
                            FROM venue v
                            LEFT JOIN maintenance m ON m.venue_id = v.venue_id AND m.maintenance_id = (SELECT MAX(maintenance_id) FROM maintenance WHERE venue_id = v.venue_id)
                            LEFT JOIN admin a ON m.admin_id = a.admin_id";

                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $display_admin = !empty($row['updated_by']) ? htmlspecialchars($row['updated_by']) : '-';
                            $display_qty = ($row['type'] === 'COURT') ? '-' : htmlspecialchars($row['quantity']);
                            $display_date = !empty($row['last_date']) ? date("d/m/Y", strtotime($row['last_date'])) : '-';

                            echo "<tr>";
                            echo "<td><a class='item-link' href='AdminEditInventory.php?id=" . urlencode($row['id']) . "&type=" . urlencode($row['type']) . "'>" . htmlspecialchars($row['id']) . "</a></td>";
                            echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                            echo "<td>" . $display_qty . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . $display_date . "</td>"; 
                            echo "<td>" . $display_admin . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No records found in database.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function filterTable() {
            let input = document.getElementById("searchInput").value.toUpperCase();
            let table = document.getElementById("inventoryTable");
            let tr = table.getElementsByTagName("tr");
            for (let i = 1; i < tr.length; i++) {
                let tdDetails = tr[i].getElementsByTagName("td")[1];
                if (tdDetails) {
                    let textValue = tdDetails.textContent || tdDetails.innerText;
                    tr[i].style.display = textValue.toUpperCase().indexOf(input) > -1 ? "" : "none";
                }
            }
        }
    </script>
</body>

</html>