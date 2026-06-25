<?php
session_start();
include "db.php";

// Set defaults
$view_mode = $_POST['view_mode'] ?? 'week'; 
$week_val = $_POST['week_val'] ?? date('Y-\WW'); 
$month_val = $_POST['month_val'] ?? date('Y-m'); 
$sort_filter = $_POST['sort_filter'] ?? 'all'; 

// Calculate start and end dates based on selected timeframe
$start_date = '';
$end_date = '';
$display_range = '';

if ($view_mode === 'week') {
    $dto = new DateTime();
    $year = substr($week_val, 0, 4);
    $week = substr($week_val, 6, 2);
    $dto->setISODate(intval($year), intval($week));
    $start_date = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $end_date = $dto->format('Y-m-d');
    $display_range = "Week: " . $start_date . " to " . $end_date;
} else {
    $start_date = $month_val . '-01';
    $end_date = date('Y-m-t', strtotime($start_date));
    $display_range = "Month: " . date('F Y', strtotime($start_date));
}

// Fetch active bookings (Pending or Approved)
$query = "SELECT booking_details FROM booking WHERE booking_status IN ('Pending', 'Approved')";
$result = mysqli_query($conn, $query);

$counts = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $details = explode("\t", $row['booking_details']);
        $court = trim($details[4] ?? '');
        $date = trim($details[5] ?? '');
        $eq = trim($details[8] ?? '');
        $qty = intval(trim($details[9] ?? '0'));
        
        if ($qty === 0 && $eq !== 'N/A' && $eq !== '') {
            $qty = 1; // Fallback if quantity wasn't recorded but equipment exists
        }

        // Filter by the calculated date range
        if ($date >= $start_date && $date <= $end_date) {
            if ($sort_filter === 'all') {
                if ($court) { 
                    $counts[$court] = ($counts[$court] ?? 0) + 1; 
                }
                if ($eq && $eq !== 'N/A') { 
                    $counts[$eq] = ($counts[$eq] ?? 0) + $qty; 
                }
            } elseif (in_array($sort_filter, ['courts_only', 'most_booked_courts', 'least_booked_courts'])) {
                // Focus only on courts
                if ($court) { 
                    $counts[$court] = ($counts[$court] ?? 0) + 1; 
                }
            } else {
                // For Most/Least equipment filters, focus only on equipment
                if ($eq && $eq !== 'N/A') { 
                    $counts[$eq] = ($counts[$eq] ?? 0) + $qty; 
                }
            }
        }
    }
}

// Apply sorting filters
if ($sort_filter === 'most_booked' || $sort_filter === 'most_booked_courts') {
    arsort($counts);
} elseif ($sort_filter === 'least_booked' || $sort_filter === 'least_booked_courts') {
    asort($counts);
}

// Prepare data for Chart.js
$chart_labels = json_encode(array_keys($counts));
$chart_data = json_encode(array_values($counts));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pusat Sukan Admin Page</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            align-items: center;
        }

        .form-centered {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }

        .container {
            width: 100%;
            max-width: 950px;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        h2 {
            margin: 0 0 15px 0;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .meta-text {
            font-size: 14px;
            margin-bottom: 25px;
            color: #555;
            font-weight: bold;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            align-items: flex-end;
            flex-wrap: wrap;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 0;
            flex: 1;
            min-width: 180px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        select,
        input[type="week"],
        input[type="month"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 13px;
            background: #fff;
            color: #000;
            height: 40px;
        }

        .submit-btn {
            background-color: #294797;
            color: #ffffff;
            border: none;
            cursor: pointer;
            padding: 0 25px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            height: 40px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .submit-btn:hover {
            background-color: #1f3675;
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        .chart-container {
            width: 100%;
            height: 400px;
            position: relative;
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
        <div class="form-centered">
            <div class="container">
                <h2>Data Analysis & Booking Trends</h2>
                <div class="meta-text">Showing Data For: <?php echo $display_range; ?></div>

                <form method="POST" action="" class="filter-form">
                    <div class="form-group">
                        <label for="view_mode">View By</label>
                        <select id="view_mode" name="view_mode">
                            <option value="week" <?php echo ($view_mode === 'week') ? 'selected' : ''; ?>>Weekly</option>
                            <option value="month" <?php echo ($view_mode === 'month') ? 'selected' : ''; ?>>Monthly</option>
                        </select>
                    </div>

                    <div class="form-group" id="week_input_group" style="display: <?php echo ($view_mode === 'week') ? 'block' : 'none'; ?>;">
                        <label for="week_val">Select Week</label>
                        <input type="week" id="week_val" name="week_val" value="<?php echo htmlspecialchars($week_val); ?>">
                    </div>

                    <div class="form-group" id="month_input_group" style="display: <?php echo ($view_mode === 'month') ? 'block' : 'none'; ?>;">
                        <label for="month_val">Select Month</label>
                        <input type="month" id="month_val" name="month_val" value="<?php echo htmlspecialchars($month_val); ?>">
                    </div>

                    <div class="form-group">
                        <label for="sort_filter">Data Filter</label>
                        <select id="sort_filter" name="sort_filter">
                            <option value="all" <?php echo ($sort_filter === 'all') ? 'selected' : ''; ?>>All Courts & Equipments</option>
                            <option value="courts_only" <?php echo ($sort_filter === 'courts_only') ? 'selected' : ''; ?>>All Courts</option>
                            <option value="most_booked_courts" <?php echo ($sort_filter === 'most_booked_courts') ? 'selected' : ''; ?>>Most Booked Courts</option>
                            <option value="least_booked_courts" <?php echo ($sort_filter === 'least_booked_courts') ? 'selected' : ''; ?>>Least Booked Courts</option>
                            <option value="most_booked" <?php echo ($sort_filter === 'most_booked') ? 'selected' : ''; ?>>Most Booked Equipment</option>
                            <option value="least_booked" <?php echo ($sort_filter === 'least_booked') ? 'selected' : ''; ?>>Least Booked Equipment</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">Filter Analysis</button>
                </form>

                <div class="chart-container">
                    <canvas id="analysisChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Toggle input fields based on Week/Month selection
        document.getElementById('view_mode').addEventListener('change', function() {
            if (this.value === 'week') {
                document.getElementById('week_input_group').style.display = 'block';
                document.getElementById('month_input_group').style.display = 'none';
            } else {
                document.getElementById('week_input_group').style.display = 'none';
                document.getElementById('month_input_group').style.display = 'block';
            }
        });

        // Initialize Chart.js Column Chart
        const ctx = document.getElementById('analysisChart').getContext('2d');
        const labels = <?php echo $chart_labels; ?>;
        const dataVals = <?php echo $chart_data; ?>;

        if (labels.length === 0) {
            // Display empty state if no data
            ctx.font = "16px Arial";
            ctx.fillStyle = "#555";
            ctx.textAlign = "center";
            ctx.fillText("No data found.", ctx.canvas.width / 4, ctx.canvas.height / 4);
        } else {
            const analysisChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Bookings / Quantity',
                        data: dataVals,
                        backgroundColor: 'rgba(41, 71, 151, 0.7)',
                        borderColor: 'rgba(41, 71, 151, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 
                            },
                            title: {
                                display: true,
                                text: 'Volume (Times Booked / Items Rented)',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Courts & Equipments',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>