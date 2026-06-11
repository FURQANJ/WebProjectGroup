<?php
include "db.php";

$booking_id = $_GET['id'];

$sql = "SELECT * FROM bookings WHERE booking_id = '$booking_id'";
$result = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page">
    <div class="sidebar">
        <img src="images/utem-logo.png" class="logo" alt="UTeM Logo">
    </div>

    <div class="main">
        <a href="booking_requests.php" class="back-btn">←</a>

        <div class="details-card">
            <div class="details-header">
                <h2>Booking Details</h2>
            </div>

            <div class="details-body">
                <p><?php echo $booking['booking_id']; ?></p>
                <p><?php echo $booking['name']; ?></p>
                <p><?php echo $booking['phone']; ?></p>
                <p><?php echo $booking['email']; ?></p>
                <p><?php echo $booking['court']; ?></p>
                <p><?php echo $booking['equipment']; ?></p>

                <p>
                    <?php echo $booking['booking_date']; ?>
                    (<?php echo $booking['start_time']; ?> -
                    <?php echo $booking['end_time']; ?>)
                </p>

                <label>Reason</label>
                <textarea readonly><?php echo $booking['reason']; ?></textarea>

                <?php if ($booking['status'] == 'Pending') { ?>

                    <form action="reject_booking.php" method="POST" class="rejection-form" id="rejectForm">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">

                        <label>Rejection reason</label>
                        <input type="text" name="rejection_reason" id="rejection_reason" placeholder="Type">

                        <div class="action-buttons">
                            <button type="button" class="image-btn" onclick="submitRejectForm();">
                                <img src="images/reject.png" alt="Reject">
                            </button>

                            <button type="submit" formaction="approve_booking.php" class="image-btn">
                                <img src="images/approve.png" alt="Approve">
                            </button>
                        </div>
                    </form>

                <?php } ?>

                <?php if ($booking['status'] == 'Approved') { ?>
                    <div class="status approved-status">
                        <img src="images/approve.png" alt="Approved">
                        <span>APPROVED</span>
                    </div>
                <?php } ?>

                <?php if ($booking['status'] == 'Rejected') { ?>
                    <div class="status rejected-status">
                        <img src="images/reject.png" alt="Rejected">
                        <span>REJECTED</span>
                    </div>

                    <?php if (!empty($booking['rejection_reason'])) { ?>
                        <p><strong>Rejection reason:</strong> <?php echo $booking['rejection_reason']; ?></p>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<script>
function submitRejectForm() {
    const reason = document.getElementById("rejection_reason").value.trim();

    if (reason === "") {
        alert("Please state your reason.");
        return;
    }

    document.getElementById("rejectForm").submit();
}
</script>

</body>
</html>