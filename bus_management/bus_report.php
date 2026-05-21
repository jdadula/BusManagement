<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require 'config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Bus Income Report</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f0f0;
            padding: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #009688;
            color: white;
        }

        tr:hover {
            background: #f5f5f5;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .btn-blue {
            background: #2196F3;
        }

        .btn-red {
            background: #f44336;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header-bar">
            <h2>🚌 Income Report Per Bus</h2>
            <div>
                <a href="reports.php" class="btn btn-blue">📊 Back to Reports</a>
                <a href="logout.php" class="btn btn-red">Logout</a>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>💰 Total Income Per Bus</h3>
        <table>
            <tr>
                <th>Bus ID</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Couch Type</th>
                <th>Fare Per Ticket</th>
                <th>Total Tickets Sold</th>
                <th>Total Income</th>
            </tr>
            <?php
            $result = mysqli_query($conn, "
    SELECT b.Bus_no, b.Source, b.Destination, b.Couch_type, b.Fair,
           COUNT(t.TransactionID) as TotalTickets,
           SUM(t.Amount) as TotalIncome
    FROM Bus b
    LEFT JOIN Transactions t ON b.Bus_no = t.BusID
    LEFT JOIN Tickets tk ON t.TicketID = tk.TicketID AND tk.Status = 'BOOKED'
    GROUP BY b.Bus_no
    ORDER BY TotalIncome DESC
");
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['Bus_no']; ?></td>
                    <td><?php echo $row['Source']; ?></td>
                    <td><?php echo $row['Destination']; ?></td>
                    <td><?php echo $row['Couch_type']; ?></td>
                    <td>₱<?php echo number_format($row['Fair'], 2); ?></td>
                    <td><?php echo $row['TotalTickets']; ?></td>
                    <td>
                        <?php if ($row['TotalIncome'] > 0) { ?>
                            ₱<?php echo number_format($row['TotalIncome'], 2); ?>
                        <?php } else { ?>
                            <span style="color:gray;">No sales yet</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>