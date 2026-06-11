<?php
include "db.php";

$booking_id = $_POST['booking_id'];
$rejection_reason = trim($_POST['rejection_reason']);

if ($rejection_reason == "") {
    echo "<script>
            alert('Please state your reason.');
            window.location.href = 'booking_details.php?id=$booking_id';
          </script>";
    exit();
}

$sql = "UPDATE bookings 
        SET status = 'Rejected', rejection_reason = '$rejection_reason' 
        WHERE booking_id = '$booking_id'";

mysqli_query($conn, $sql);

header("Location: booking_details.php?id=$booking_id");
exit();
?>