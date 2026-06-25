<?php
include "db.php";

if (!isset($_POST['booking_id']) || !isset($_POST['status'])) {
    header("Location: AdminBookingRequest.php");
    exit();
}

$booking_id = $_POST['booking_id'];
$status = $_POST['status'];

if ($status === "Approved") {
    $stmt = $conn->prepare("
        UPDATE booking
        SET booking_status = 'Approved',
            rejection_reason = NULL
        WHERE booking_id = ?
    ");

    $booking_id = (int) $_POST['booking_id'];

$sql = "UPDATE booking 
        SET booking_status = 'Approved' 
        WHERE booking_id = $booking_id";

$conn->query($sql);

    header("Location: BookingActionResult.php?status=Approved");
    exit();

} elseif ($status === "Rejected") {
    $rejection_reason = $_POST['rejection_reason'] ?? '';

    $stmt = $conn->prepare("
        UPDATE booking
        SET booking_status = 'Rejected',
            rejection_reason = ?
        WHERE booking_id = ?
    ");

   $booking_id = (int) $_POST['booking_id'];
$rejection_reason = mysqli_real_escape_string($conn, $_POST['rejection_reason']);

$sql = "UPDATE booking
        SET booking_status = 'Rejected',
            rejection_reason = '$rejection_reason'
        WHERE booking_id = $booking_id";

$conn->query($sql);

    header("Location: BookingActionResult.php?status=Rejected");
    exit();

} else {
    header("Location: AdminBookingRequest.php");
    exit();
}
?>