<?php
include "db.php";

$sql = "SELECT * FROM bookings WHERE status = 'Pending'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Requests</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page">
    <div class="sidebar">
        <img src="images/utem-logo.png" class="logo" alt="UTeM Logo">
    </div>

    <div class="main">
        <a href="admin_home.php" class="back-btn">←</a>

        <h2>BOOKING REQUESTS</h2>

        <table>
            <tr>
                <th>BOOKING ID</th>
                <th>NAME / ORGANIZATION</th>
                <th>NUMBER</th>
                <th>COURT</th>
                <th>EQUIPMENT</th>
                <th>DATE</th>
                <th>TIME</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>
                        <a href="booking_details.php?id=<?php echo $row['booking_id']; ?>">
                            <?php echo $row['booking_id']; ?>
                        </a>
                    </td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['court']; ?></td>
                    <td><?php echo $row['equipment']; ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td>
                        <?php echo $row['start_time']; ?> -
                        <?php echo $row['end_time']; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>