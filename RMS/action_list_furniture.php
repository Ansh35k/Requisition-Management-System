<?php
session_start();
if (!isset($_SESSION['staff']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php';

$category_id = 1; // Furniture
$leg_id = 2;
$sql = "SELECT m.Req_id, m.add_dt, m.staffno, s.name 
        FROM CPR_REQIUIS_MASTER m
        JOIN staff_users s ON m.staffno = s.staff_no
        WHERE m.category_id = ? AND m.leg_id = ?
        ORDER BY m.add_dt DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $category_id, $leg_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Furniture Approvals</title>
    <style>
        body {
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial;
            background-image: url('images/wave3.jpg'); /* adjust path if needed */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: black;
        }

        .container {
            max-width: 1000px;
            margin: 60px auto; /* Add top margin for spacing */
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
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
            margin-top: 30px;
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

        a {
            color: #1a68c6;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Furniture - Pending Approvals</h2>
    <table>
        <tr>
            <th>Req. No.</th>
            <th>Req. Date</th>
            <th>Staff No.</th>
            <th>Name</th>
            <th>View</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Req_id']) ?></td>
            <td><?= date('d-m-Y', strtotime($row['add_dt'])) ?></td>
            <td><?= htmlspecialchars($row['staffno']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><a href="action_furniture.php?req_id=<?= $row['Req_id'] ?>">View</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
