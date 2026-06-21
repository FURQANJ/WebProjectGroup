<?php
include "db.php";

$booking_id = $_POST['booking_id'];

$sql = "UPDATE bookings 
        SET status = 'Approved' 
        WHERE booking_id = '$booking_id'";

mysqli_query($conn, $sql);

header("Location: booking_details.php?id=$booking_id");
exit();
?>