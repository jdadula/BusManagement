<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}
require 'config.php';

$message = "";

// Handle Actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // ADD USER
    if ($_POST['action'] == 'add') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = $_POST['role'];

        if (empty($username) || empty($password)) {
            $message = "<p style='color:red;'>❌ All fields are required!</p>";
        } elseif (strlen($password) < 6) {
            $message = "<p style='color:red;'>❌ Password must be at least 6 characters!</p>";
        } else {
            $check = mysqli_prepare($conn, "SELECT * FROM users WHERE Username = ?");
            mysqli_stmt_bind_param($check, "s", $username);
            mysqli_stmt_execute($check);
            $checkResult = mysqli_stmt_get_result($check);

            if (mysqli_num_rows($checkResult) > 0) {
                $message = "<p style='color:red;'>❌ Username already exists!</p>";
            } else {
                $hashedPassword = hash('sha256', $password);
                $stmt = mysqli_prepare($conn, "INSERT INTO users (Username, Password, Role, is_active) VALUES (?, ?, ?, 1)");
                mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $role);
                if (mysqli_stmt_execute($stmt)) {
                    $newID = mysqli_insert_id($conn);
                    $message = "<p style='color:green;'>✅ User <strong>$username</strong> added! User ID: <strong>#$newID</strong></p>";
                } else {
                    $message = "<p style='color:red;'>❌ Failed to add user!</p>";
                }
            }
        }
    }

    // DEACTIVATE (Soft Delete)
    if ($_POST['action'] == 'delete') {
        $uid = intval($_POST['user_id']);
        if ($uid == $_SESSION['user_id']) {
            $message = "<p style='color:red;'>❌ You cannot deactivate your own account!</p>";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET is_active = 0 WHERE User_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $uid);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<p style='color:orange;'>⚠️ Account deactivated successfully!</p>";
            }
        }
    }

    // REACTIVATE
    if ($_POST['action'] == 'reactivate') {
        $uid = intval($_POST['user_id']);
        $stmt = mysqli_prepare($conn, "UPDATE users SET is_active = 1 WHERE User_ID = ?");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        if (mysqli_stmt_execute($stmt)) {
            $message = "<p style='color:green;'>✅ Account reactivated successfully!</p>";
        }
    }

    // CHANGE ROLE
    if ($_POST['action'] == 'change_role') {
        $uid = intval($_POST['user_id']);
        $newRole = $_POST['new_role'];
        if ($uid == $_SESSION['user_id']) {
            $message = "<p style='color:red;'>❌ You cannot change your own role!</p>";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET Role = ? WHERE User_ID = ?");
            mysqli_stmt_bind_param($stmt, "si", $newRole, $uid);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<p style='color:green;'>✅ Role updated successfully!</p>";
            }
        }
    }
}

// Count by role
$counts = mysqli_query($conn, "SELECT Role, is_active, COUNT(*) as Total FROM users GROUP BY Role, is_active");
$active = ['admin' => 0, 'driver' => 0, 'passenger' => 0];
$inactive = ['admin' => 0, 'driver' => 0, 'passenger' => 0];
while ($c = mysqli_fetch_assoc($counts)) {
    if ($c['is_active']) $active[$c['Role']] = $c['Total'];
    else $inactive[$c['Role']] = $c['Total'];
}
$totalActive = array_sum($active);
$totalInactive = array_sum($inactive);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #3F51B5; color: white; }
        tr:hover { background: #f5f5f5; }
        input, select { width: 100%; padding: 10px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 8px 15px; border-radius: 5px; color: white; border: none; cursor: pointer; text-decoration: none; display: inline-block; font-size: 13px; margin: 2px; }
        .btn-green { background: #4CAF50; }
        .btn-blue { background: #2196F3; }
        .btn-red { background: #f44336; }
        .btn-orange { background: #FF9800; }
        .badge { padding: 3px 10px; border-radius: 10px; color: white; font-size: 12px; font-weight: bold; }
        .badge-admin { background: #4CAF50; }
        .badge-driver { background: #2196F3; }
        .badge-passenger { background: #FF9800; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; }
        .form-row > div { flex: 1; min-width: 150px; }
        label { font-weight: bold; font-size: 14px; }
        .summary { display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap; }
        .summary-box { flex: 1; background: white; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); min-width: 130px; }
        .summary-box h3 { margin: 0; font-size: 12px; color: #666; }
        .summary-box p { margin: 8px 0 0 0; font-size: 24px; font-weight: bold; }
        .inactive-row { opacity: 0.5; background: #f8f8f8 !important; }
        .filter-tabs { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .filter-tab { padding: 8px 15px; border-radius: 5px; cursor: pointer; background: #ddd; border: none; font-size: 13px; }
        .filter-tab.active { background: #3F51B5; color: white; }
    </style>
</head>
<body>

<div class="card">
    <div class="header-bar">
        <h2>👥 Manage All Users</h2>
        <div>
            <a href="admin_dashboard.php" class="btn btn-blue">🏠 Dashboard</a>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>
    </div>
    <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | Role: ADMIN</p>
</div>

<!-- Summary Cards -->
<div class="summary">
    <div class="summary-box">
        <h3>👤 Total Active</h3>
        <p style="color:#3F51B5;"><?php echo $totalActive; ?></p>
    </div>
    <div class="summary-box">
        <h3>🛠️ Admins</h3>
        <p style="color:#4CAF50;"><?php echo $active['admin']; ?></p>
    </div>
    <div class="summary-box">
        <h3>🚗 Drivers</h3>
        <p style="color:#2196F3;"><?php echo $active['driver']; ?></p>
    </div>
    <div class="summary-box">
        <h3>🎫 Passengers</h3>
        <p style="color:#FF9800;"><?php echo $active['passenger']; ?></p>
    </div>
    <div class="summary-box">
        <h3>❌ Deactivated</h3>
        <p style="color:#f44336;"><?php echo $totalInactive; ?></p>
    </div>
</div>

<?php if ($message) echo "<div class='card'>$message</div>"; ?>

<!-- Add New User Form -->
<div class="card">
    <h3>➕ Add New User</h3>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div>
                <label>Username:</label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <div>
                <label>Role:</label>
                <select name="role">
                    <option value="passenger">🎫 Passenger</option>
                    <option value="admin">🛠️ Admin</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-green">➕ Add User</button>
    </form>
</div>

<!-- Filter Tabs -->
<div class="card">
    <h3>📋 All User Accounts</h3>
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterTable('all', this)">👤 All</button>
        <button class="filter-tab" onclick="filterTable('active', this)">✅ Active</button>
        <button class="filter-tab" onclick="filterTable('inactive', this)">❌ Deactivated</button>
        <button class="filter-tab" onclick="filterTable('admin', this)">🛠️ Admins</button>
        <button class="filter-tab" onclick="filterTable('driver', this)">🚗 Drivers</button>
        <button class="filter-tab" onclick="filterTable('passenger', this)">🎫 Passengers</button>
    </div>

    <table id="usersTable">
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Account Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM users ORDER BY User_ID ASC");
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='5' style='text-align:center; color:gray;'>No users found.</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                $badge = "badge-" . $row['Role'];
                $isCurrentUser = ($row['User_ID'] == $_SESSION['user_id']);
                $isActive = $row['is_active'];
                $rowClass = $isActive ? '' : 'inactive-row';

                $statusBadge = $isActive
                    ? '<span style="background:#4CAF50; color:white; padding:3px 10px; border-radius:10px; font-size:12px;">✅ Active</span>'
                    : '<span style="background:#f44336; color:white; padding:3px 10px; border-radius:10px; font-size:12px;">❌ Deactivated</span>';

                // Data attributes for filtering
                $activeAttr = $isActive ? 'active' : 'inactive';

                echo "<tr class='$rowClass' data-status='$activeAttr' data-role='{$row['Role']}'>
                    <td>{$row['User_ID']}</td>
                    <td>{$row['Username']}" . ($isCurrentUser ? " <span style='color:green; font-size:11px;'>(You)</span>" : "") . "</td>
                    <td><span class='badge $badge'>" . strtoupper($row['Role']) . "</span></td>
                    <td>$statusBadge</td>
                    <td>";

                // Change Role (only for active non-current users)
                if (!$isCurrentUser && $isActive) {
                    echo "<form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='change_role'>
                        <input type='hidden' name='user_id' value='{$row['User_ID']}'>
                        <select name='new_role' style='width:auto; padding:5px; margin:0; display:inline;'>
                            <option value='passenger'" . ($row['Role']=='passenger'?' selected':'') . ">Passenger</option>
                            <option value='driver'" . ($row['Role']=='driver'?' selected':'') . ">Driver</option>
                            <option value='admin'" . ($row['Role']=='admin'?' selected':'') . ">Admin</option>
                        </select>
                        <button type='submit' class='btn btn-orange'>✏️ Change</button>
                    </form>";
                }

                // Deactivate or Reactivate button
                if (!$isCurrentUser) {
                    if ($isActive) {
                        echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Deactivate this account?\")'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='user_id' value='{$row['User_ID']}'>
                            <button type='submit' class='btn btn-red'>🗑 Deactivate</button>
                        </form>";
                    } else {
                        echo "<form method='POST' style='display:inline;'>
                            <input type='hidden' name='action' value='reactivate'>
                            <input type='hidden' name='user_id' value='{$row['User_ID']}'>
                            <button type='submit' class='btn btn-green'>✅ Reactivate</button>
                        </form>";
                    }
                }

                echo "</td></tr>";
            }
        }
        ?>
    </table>
</div>

<script>
function filterTable(filter, el) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    const rows = document.querySelectorAll('#usersTable tr:not(:first-child)');
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const role = row.getAttribute('data-role');

        if (filter === 'all') {
            row.style.display = '';
        } else if (filter === 'active' || filter === 'inactive') {
            row.style.display = (status === filter) ? '' : 'none';
        } else {
            row.style.display = (role === filter) ? '' : 'none';
        }
    });
}
</script>
</body>
</html>