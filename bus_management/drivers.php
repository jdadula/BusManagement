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
    <title>Driver Management</title>
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
            background: #2196F3;
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
            display: inline-block;
            margin: 3px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .btn-green {
            background: #4CAF50;
        }

        .btn-blue {
            background: #2196F3;
        }

        .btn-red {
            background: #f44336;
        }

        .btn-orange {
            background: #FF9800;
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
            <h2>🚗 Driver Management System</h2>
            <div>
                <a href="add_driver.php" class="btn btn-green">➕ Add New Driver</a>
                <a href="admin_dashboard.php" class="btn btn-blue">🏠 Dashboard</a>
                <a href="logout.php" class="btn btn-red">Logout</a>
            </div>
        </div>
        <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | Role: ADMIN</p>
    </div>

    <div class="card">
        <h3>📋 All Registered Drivers</h3>
        <?php
        // Join Drivers with users using Username column
        $result = mysqli_query($conn, "
    SELECT d.DriverID, d.Username, d.FirstName, d.LastName,
           d.LicenseNumber, d.ContactNumber, d.BusID,
           u.User_ID, u.is_active
    FROM Drivers d
    LEFT JOIN users u ON u.Username = d.Username
    ORDER BY u.User_ID ASC
");

        if (mysqli_num_rows($result) == 0) {
            echo "<p style='color:gray;'>No drivers registered yet.</p>";
        } else {
        ?>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>License Number</th>
                    <th>Contact Number</th>
                    <th>Assigned Bus</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) {
                    $rowStyle = $row['is_active'] ? '' : 'style="opacity:0.5; background:#f8f8f8;"';
                    $statusBadge = $row['is_active']
                        ? '<span style="background:#4CAF50; color:white; padding:3px 8px; border-radius:10px; font-size:11px;">✅ Active</span>'
                        : '<span style="background:#f44336; color:white; padding:3px 8px; border-radius:10px; font-size:11px;">❌ Deactivated</span>';
                ?>
                    <tr <?php echo $rowStyle; ?>>
                        <td><?php echo $row['User_ID'] ?? 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($row['Username']); ?></td>
                        <td><?php echo htmlspecialchars($row['FirstName']); ?></td>
                        <td><?php echo htmlspecialchars($row['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($row['LicenseNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['ContactNumber']); ?></td>
                        <td><?php echo $row['BusID'] ?? 'Not Assigned'; ?></td>
                        <td><?php echo $statusBadge; ?></td>
                        <td>
                            <?php if ($row['is_active']) { ?>
                                <a href="view_driver.php?id=<?php echo $row['DriverID']; ?>" class="btn btn-blue">👁 View</a>
                                <a href="edit_driver.php?id=<?php echo $row['DriverID']; ?>" class="btn btn-orange">✏️ Edit</a>
                                <a href="delete_driver.php?id=<?php echo $row['DriverID']; ?>" class="btn btn-red" onclick="return confirm('Delete this driver?')">🗑 Delete</a>
                            <?php } else { ?>
                                <span style="color:gray; font-size:12px;">Account Deactivated</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</body>

</html>