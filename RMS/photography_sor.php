<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$category_id = 6;
$staffno = $_SESSION['staff'];

// Get user role
$role_stmt = $conn->prepare("SELECT role FROM staff_users WHERE staff_no = ?");
$role_stmt->bind_param("s", $staffno);
$role_stmt->execute();
$role_stmt->bind_result($role);
$role_stmt->fetch();
$role_stmt->close();

// Construct query based on role
if ($role === 'admin') {
    // Admin sees all entries in the category
    $sql = "SELECT  
                r.Req_id, 
                r.add_dt, 
                r.event_detail, 
                r.event_date,
                l.status_description,
                r.acknow_remark AS cpr_remark
            FROM CPR_REQIUIS_MASTER r
            JOIN LEG_STATUS_MASTER l ON r.leg_id = l.leg_id
            WHERE r.category_id = ?
            GROUP BY r.Req_id
            ORDER BY r.add_dt DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
} else {
    // Regular user sees only their own entries
    $sql = "SELECT  
                r.Req_id, 
                r.add_dt, 
                r.event_detail, 
                r.event_date,
                l.status_description,
                r.acknow_remark AS cpr_remark
            FROM CPR_REQIUIS_MASTER r
            JOIN LEG_STATUS_MASTER l ON r.leg_id = l.leg_id
            WHERE r.category_id = ? AND r.staffno = ?
            GROUP BY r.Req_id
            ORDER BY r.add_dt DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $category_id, $staffno);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Photography Requisition Status</title>
    <style>
        body {
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url('images/wave3.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: black;
        }

        .container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.92);
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            color: red;
            margin-bottom: 30px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #1a68c6;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #e8f4fc;
        }

        tr:nth-child(odd) {
            background-color: #f6fbff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Status of Photography Requisition</h2>

    <table>
        <tr>
            <th>Req. No.</th>
            <th>Req. Date</th>
            <th>Event Detail</th>
            <th>Event Date</th>
            <th>Status</th>
            <th>Remark / Comments of CPR</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Req_id']) ?></td>
            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['add_dt']))) ?></td>
            <td><?= htmlspecialchars($row['event_detail']) ?></td>
            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['event_date']))) ?></td>
            <td><?= htmlspecialchars($row['status_description']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['cpr_remark'])) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
