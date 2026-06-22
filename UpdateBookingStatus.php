<?php
include "db.php";

if (!isset($_POST['booking_id']) || !isset($_POST['status'])) {
    header("Location: adminBookingRequests.php");
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

    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    header("Location: bookingActionResult.php?status=Approved");
    exit();

} elseif ($status === "Rejected") {
    $rejection_reason = $_POST['rejection_reason'] ?? '';

    $stmt = $conn->prepare("
        UPDATE booking
        SET booking_status = 'Rejected',
            rejection_reason = ?
        WHERE booking_id = ?
    ");

    $stmt->bind_param("si", $rejection_reason, $booking_id);
    $stmt->execute();
    $stmt->close();

    header("Location: bookingActionResult.php?status=Rejected");
    exit();

} else {
    header("Location: adminBookingRequests.php");
    exit();
}
?>