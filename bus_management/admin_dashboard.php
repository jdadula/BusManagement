<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}
require 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; display: inline-block; margin: 3px; font-size: 13px; }
        .btn-green { background: #4CAF50; }
        .btn-blue { background: #2196F3; }
        .btn-red { background: #f44336; }
        .btn-purple { background: #9C27B0; }
        .btn-orange { background: #FF9800; }
        .btn-teal { background: #009688; }
        .btn-indigo { background: #3F51B5; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .badge { padding: 3px 10px; border-radius: 10px; color: white; font-size: 12px; font-weight: bold; }
        .inactive-row { opacity: 0.5; background: #f8f8f8 !important; }
    </style>
</head>
<body>

<!-- Header Card -->
<div class="card">
    <div class="header-bar">
        <h2>🛠️ Admin Dashboard</h2>
        <div>
            <a href="tickets.php" class="btn btn-orange">🎫 View All Tickets</a>
            <a href="reports.php" class="btn btn-indigo">📊 Income Reports</a>
            <a href="bus_report.php" class="btn btn-teal">🚌 Bus Reports</a>
            <a href="drivers.php" class="btn btn-purple">🚗 Driver Management</a>
            <a href="manage_users.php" class="btn btn-indigo">👥 Manage Users</a>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>
    </div>
    <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong>! Role: <strong>ADMIN</strong></p>
</div>

<!-- All Buses Card -->
<div class="card">
    <h3>🚌 All Buses</h3>
    <table>
        <tr>
            <th>Bus No</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Couch Type</th>
            <th>Fair</th>
            <th>Seat Capacity</th>
            <th>Status</th>
        </tr>
        <?php
        $busResult = mysqli_query($conn, "SELECT * FROM Bus ORDER BY Bus_no ASC");
        if (mysqli_num_rows($busResult) == 0) {
            echo "<tr><td colspan='8' style='color:gray; text-align:center;'>No buses found.</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($busResult)) {
                // Color code status
                if ($row['Status'] == 'PERFECT CONDITION') $statusColor = 'color:#4CAF50; font-weight:bold;';
                elseif ($row['Status'] == 'GOOD CONDITION') $statusColor = 'color:#2196F3; font-weight:bold;';
                elseif ($row['Status'] == 'UNDER MAINTENANCE') $statusColor = 'color:#FF9800; font-weight:bold;';
                else $statusColor = 'color:#f44336; font-weight:bold;';

                echo "<tr>
                    <td>{$row['Bus_no']}</td>
                    <td>{$row['Source']}</td>
                    <td>{$row['Destination']}</td>
                    <td>{$row['Couch_type']}</td>
                    <td>₱{$row['Fair']}</td>
                    <td>{$row['SeatCapacity']}</td>
                    <td style='$statusColor'>{$row['Status']}</td>
                </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- All User Accounts Card -->
<div class="card">
    <div class="header-bar">
        <h3>👥 All User Accounts</h3>
        <a href="manage_users.php" class="btn btn-indigo">⚙️ Manage Users</a>
    </div>
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
        <?php
        // Show ALL accounts including inactive ordered by User_ID
        $result = mysqli_query($conn, "
            SELECT User_ID, Username, Role, is_active 
            FROM users 
            ORDER BY User_ID ASC
        ");
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='4' style='color:gray; text-align:center;'>No users registered yet.</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['Role'] == 'admin') $color = '#4CAF50';
                elseif ($row['Role'] == 'driver') $color = '#2196F3';
                else $color = '#FF9800';

                $rowStyle = $row['is_active'] ? '' : 'class="inactive-row"';

                $activeStatus = $row['is_active']
                    ? '<span style="background:#4CAF50; color:white; padding:3px 10px; border-radius:10px; font-size:12px;">✅ Active</span>'
                    : '<span style="background:#f44336; color:white; padding:3px 10px; border-radius:10px; font-size:12px;">❌ Deactivated</span>';

                echo "<tr $rowStyle>
                    <td>{$row['User_ID']}</td>
                    <td>{$row['Username']}</td>
                    <td><span style='background:{$color}; color:white; padding:3px 10px; border-radius:10px; font-size:12px;'>"
                        . strtoupper($row['Role']) .
                    "</span></td>
                    <td>$activeStatus</td>
                </tr>";
            }
        }
        ?>
    </table>
</div>

</body>
</html>