<?php
include "db.php";

$courtFilter = $_POST['court'] ?? '';
$dateFilter = $_POST['date'] ?? '';
$statusFilter = $_POST['status'] ?? '';
$sortFilter = $_POST['sort'] ?? 'DESC'; // Default to latest (DESC)

$query = "SELECT * FROM booking";
$result = mysqli_query($conn, $query);

$courts = [];
$resultCourt = mysqli_query($conn, "SELECT booking_details FROM booking");
while ($rowCourt = mysqli_fetch_assoc($resultCourt)) {
    $details = explode("\t", $rowCourt['booking_details']);
    if (isset($details[4]) && trim($details[4]) !== '') {
        $courts[] = trim($details[4]);
    }
}
$courts = array_unique($courts);
sort($courts);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Log</title>
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

        h2 {
            margin-bottom: 25px;
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
            text-align: left;
            font-weight: bold;
        }

        .booking-table td {
            padding: 15px;
            border: 1px solid #cfcfcf;
        }

        .booking-table tr:hover {
            background: #f5f5f5;
        }

        .details-body {
            margin-top: 0px;
            width: 100%;
            max-width: none;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap; 
            gap: 20px; 
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .filter-group select,
        .filter-group input {
            width: 200px;
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 0 10px;
            background: white;
            font-size: 14px;
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
        </div>

        <h2>Booking Log</h2>

        <div class="details-body">
            <form method="POST">
                <div class="filter-container">

                    <div class="filter-group">
                        <label>Court</label>
                        <select name="court" onchange="this.form.submit()">
                            <option value="">All Courts</option>
                            <?php foreach ($courts as $courtOption) { ?>
                                <option value="<?php echo htmlspecialchars($courtOption); ?>"
                                    <?php if ($courtFilter == $courtOption) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($courtOption); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="this.form.submit()">
                    </div>

                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php if ($statusFilter == 'Pending') echo "selected"; ?>>Pending</option>
                            <option value="Approved" <?php if ($statusFilter == 'Approved') echo "selected"; ?>>Approved</option>
                            <option value="Rejected" <?php if ($statusFilter == 'Rejected') echo "selected"; ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Sort By</label>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="DESC" <?php if ($sortFilter == 'DESC') echo "selected"; ?>>Latest First</option>
                            <option value="ASC" <?php if ($sortFilter == 'ASC') echo "selected"; ?>>Oldest First</option>
                        </select>
                    </div>

                </div>
            </form>

            <table class="booking-table">
                <tr>
                    <th>BOOKING ID</th>
                    <th>NAME</th>
                    <th>NUMBER</th>
                    <th>COURT</th>
                    <th>DATE</th>
                    <th>TIME</th>
                    <th>STATUS</th>
                </tr>

                <?php 
                
                // utk fetch dataaa
                $all_bookings = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $details = explode("\t", $row['booking_details']);
                    
                    $name = $details[0] ?? '';
                    $phone = $details[1] ?? '';
                    $court = $details[4] ?? '';
                    $date = $details[5] ?? '';
                    $timeFrom = $details[6] ?? '';
                    $timeTo = $details[7] ?? '';
                    $status = $row['booking_status'];

                    // utk filterrr
                    if ($courtFilter != '' && trim($court) != $courtFilter) continue;
                    if ($dateFilter != '' && trim($date) != $dateFilter) continue;
                    if ($statusFilter != '' && trim($status) != $statusFilter) continue;

                    $row_data = [
                        'id' => $row['booking_id'],
                        'name' => $name,
                        'phone' => $phone,
                        'court' => $court,
                        'date' => $date,
                        'time' => $timeFrom . " - " . $timeTo,
                        'status' => $status
                    ];
                    $all_bookings[] = $row_data;
                }

                // sorting ikut id booking
                usort($all_bookings, function($a, $b) use ($sortFilter) {
                    if ($sortFilter == 'DESC') {
                        return $b['id'] <=> $a['id'];
                    } else {
                        return $a['id'] <=> $b['id'];
                    }
                });

                foreach ($all_bookings as $booking) {
                ?>
                    <tr>
                        <td>B<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($booking['name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                        <td><?php echo htmlspecialchars($booking['court']); ?></td>
                        <td><?php echo htmlspecialchars($booking['date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['time']); ?></td>
                        <td>
                            <?php 
                            $statusColor = "black";
                            if ($booking['status'] == 'Approved') $statusColor = "green";
                            if ($booking['status'] == 'Rejected') $statusColor = "red";
                            if ($booking['status'] == 'Pending') $statusColor = "#d68910";
                            
                            echo "<span style='color: {$statusColor}; font-weight: bold;'>" . htmlspecialchars($booking['status']) . "</span>"; 
                            ?>
                        </td>
                    </tr>
                <?php } 
                
                if(empty($all_bookings)) {
                    echo "<tr><td colspan='7' style='text-align: center; font-style: italic;'>No bookings found matching criteria.</td></tr>";
                }
                ?>

            </table>

        </div>
    </main>

</body>

</html>