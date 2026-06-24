<?php
include "db.php";

$courtFilter = $_POST['court'] ?? '';
$dateFilter = $_POST['date'] ?? '';
$sql = "SELECT * FROM booking";
$result = mysqli_query($conn, $sql);

$courts = [];

$resultCourt = mysqli_query($conn, "SELECT booking_details FROM booking");

while ($rowCourt = mysqli_fetch_assoc($resultCourt)) {
    $details = explode("\t", $rowCourt['booking_details']);

    if (isset($details[4])) {
        $courts[] = trim($details[4]);
    }
}

$courts = array_unique($courts);
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
        }

        .booking-table th {
            background: #d9d9d9;
            padding: 15px;
            border: 1px solid #cfcfcf;
            text-align: left;
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
            gap: 30px;
            margin-top: 10px;
            margin-bottom: 40px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 1000;
        }

        .filter-group select,
        .filter-group input {
            width: 220px;
            height: 45px;
            border: none;
            border-radius: 8px;
            padding: 0 15px;
            background: white;
            font-size: 15px;
            border: 1px solid #ccc;
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
                            <option value="">Choose Court</option>

                            <?php foreach ($courts as $courtOption) { ?>
                                <option value="<?php echo $courtOption; ?>"
                                    <?php if ($courtFilter == $courtOption) echo "selected"; ?>>
                                    <?php echo $courtOption; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo $dateFilter; ?>" onchange="this.form.submit()">
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

                <?php while ($row = mysqli_fetch_assoc($result)) {

                    $details = explode("\t", $row['booking_details']);

                    $name = $details[0] ?? '';
                    $phone = $details[1] ?? '';
                    $court = $details[4] ?? '';
                    $date = $details[5] ?? '';
                    if ($courtFilter != '' && $court != $courtFilter) {
                        continue;
                    }

                    if ($dateFilter != '' && $date != $dateFilter) {
                        continue;
                    }
                    $timeFrom = $details[6] ?? '';
                    $timeTo = $details[7] ?? '';

                ?>

                    <tr>
                        <td>B<?php echo str_pad($row['booking_id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $phone; ?></td>
                        <td><?php echo $court; ?></td>
                        <td><?php echo $date; ?></td>
                        <td><?php echo $timeFrom . " - " . $timeTo; ?></td>
                        <td><?php echo $row['booking_status']; ?></td>
                    </tr>

                <?php } ?>

            </table>

        </div>
    </main>

</body>

</html>