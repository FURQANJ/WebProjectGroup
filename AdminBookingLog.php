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

<style>
    body 
    {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('Main Background.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      min-height: 100vh;
    }
    
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: #d3d3d3;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 30px;
    }

    .page {
        display: flex;
        min-height: 100vh;
    }

    main {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 40px;
        padding-top: 20px;
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

    .logo {
        margin-bottom: 25px;
    }

    .details-header {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-top: 10px;
        margin-bottom: 20px;
    }

    .details-body {
        margin-top: 0px;
        width: 100%;
        padding: 20px;
        max-width: none;
    }

    .details-header h2 {
        margin: 0px;
        position: relative;
        top: 8px;
    }

    .back-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
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
    }
</style>

<!DOCTYPE html>
<html>

<head>
    <title>Booking Log</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="page">
        <div class="sidebar">
            <img src="UTeM Clear.png" class="logo" alt="UTeM Logo">
        </div>


        <main>
            <div class="details-header">
                <a href="adminhome.html"><img src="backbtn.jpeg" class="back-btn"></a>
                <h2>Booking Log</h2>
            </div>

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

    </div>
    </div>
    </main>
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