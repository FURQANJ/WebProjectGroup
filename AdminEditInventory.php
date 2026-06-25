<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$target_id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? '';

$details = '';
$status = '';
$quantity = 0;
$previous_notes = '';

if ($type === 'EQUIPMENT') {
    $stmt = $conn->prepare("SELECT equipment_details, equipment_status, equipment_quantity FROM equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $target_id);
    $stmt->execute();
    $stmt->bind_result($details, $status, $quantity);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT maintenance_report FROM maintenance WHERE equipment_id = ? ORDER BY maintenance_id DESC LIMIT 1");
    $stmt->bind_param("i", $target_id);
    $stmt->execute();
    $stmt->bind_result($previous_notes);
    $stmt->fetch();
    $stmt->close();
} else if ($type === 'COURT') {
    $stmt = $conn->prepare("SELECT venue_details, venue_status FROM venue WHERE venue_id = ?");
    $stmt->bind_param("i", $target_id);
    $stmt->execute();
    $stmt->bind_result($details, $status);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT maintenance_report FROM maintenance WHERE venue_id = ? ORDER BY maintenance_id DESC LIMIT 1");
    $stmt->bind_param("i", $target_id);
    $stmt->execute();
    $stmt->bind_result($previous_notes);
    $stmt->fetch();
    $stmt->close();
}

if (isset($_POST['update'])) {
    $new_status = $_POST['status'];
    $notes = $_POST['notes'];

    if ($type === 'EQUIPMENT') {
        $new_quantity = intval($_POST['quantity']);

        $stmt = $conn->prepare("UPDATE equipment SET equipment_status = ?, equipment_quantity = ? WHERE equipment_id = ?");
        $stmt->bind_param("sii", $new_status, $new_quantity, $target_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO maintenance (admin_id, equipment_id, maintenance_report, maintenance_status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $admin_id, $target_id, $notes, $new_status);
        $stmt->execute();
        $stmt->close();
    } else if ($type === 'COURT') {
        $stmt = $conn->prepare("UPDATE venue SET venue_status = ? WHERE venue_id = ?");
        $stmt->bind_param("si", $new_status, $target_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO maintenance (admin_id, venue_id, maintenance_report, maintenance_status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $admin_id, $target_id, $notes, $new_status);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: AdminUpdate.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Equipment/Court Update Form</title>
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
            display: flex;
            min-height: 100vh;
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

        .form-centered {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .container {
            width: 100%;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        h2 {
            margin: 0 0 25px 0;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .meta-text {
            font-size: 14px;
            margin-bottom: 12px;
            color: #000;
            text-align: left;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        select,
        textarea,
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 13px;
            background: #fff;
            color: #000;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        .submit-btn-container {
            display: flex;
            margin-top: 25px;
            justify-content: flex-end;
            align-items: center;
            height: 50px;
        }

        .submit-btn {
            background-color: #294797;
            color: #ffffff;
            border: none;
            cursor: pointer;
            padding: 12px 30px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 140px;
            height: 45px;
        }

        .submit-btn:hover {
            background-color: #1f3675;
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        .submit-btn.success-state {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            min-width: auto !important;
            height: 50px !important;
            cursor: default;
            box-shadow: none !important;
            transition: none !important;
        }

        .submit-btn.success-state img {
            width: 50px;
            height: 50px;
            object-fit: contain;
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
        <a href="AdminUpdate.php" class="back-btn">
            <img src="BackArrowButton.png" alt="Back" class="back-arrow-img">
        </a>

        <div class="form-centered">
            <div class="container">
                <h2>Equipment/Court Update</h2>

                <form id="updateForm" action="" method="post" onsubmit="animateSubmitButton(event)">
                    
                    <input type="hidden" name="update" value="1">

                    <div class="meta-text"><strong>ID:</strong> <?php echo htmlspecialchars($target_id); ?></div>
                    <div class="meta-text"><strong>Name:</strong> <?php echo htmlspecialchars($details); ?></div>
                    <div class="meta-text" style="margin-bottom: 25px;"><strong>Type:</strong> <?php echo htmlspecialchars($type); ?></div>

                    <div class="form-group" style="max-width: 300px;">
                        <label for="status">Availability Status :</label>
                        <select id="status" name="status">
                            <option value="AVAILABLE" <?php echo ($status === 'AVAILABLE') ? 'selected' : ''; ?>>AVAILABLE</option>
                            <option value="NOT AVAILABLE" <?php echo ($status === 'NOT AVAILABLE') ? 'selected' : ''; ?>>NOT AVAILABLE</option>
                            <option value="UNDER MAINTENANCE" <?php echo ($status === 'UNDER MAINTENANCE') ? 'selected' : ''; ?>>UNDER MAINTENANCE</option>
                        </select>
                    </div>

                    <?php if ($type === 'EQUIPMENT') { ?>
                        <div class="form-group" style="max-width: 300px;">
                            <label for="quantity">Quantity Count :</label>
                            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" min="0" required>
                        </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="notes">Maintenance Report / Notes</label>
                        <textarea id="notes" name="notes" placeholder="Type report here..." required><?php echo htmlspecialchars($previous_notes); ?></textarea>
                    </div>

                    <div class="submit-btn-container">
                        <button type="submit" id="submitBtn" class="submit-btn">Update Status</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script>
        function animateSubmitButton(event) {
            event.preventDefault();

            const form = document.getElementById('updateForm');
            const btn = document.getElementById('submitBtn');

            btn.classList.add('success-state');
            btn.innerHTML = '<img src="Updated Button.png" alt="Success Checkmark">';

            setTimeout(() => {
                form.submit();
            }, 600);
        }
    </script>
</body>

</html>