<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$query = "SELECT * FROM CPR_REQIUIS_MASTER WHERE category_id = 3 ORDER BY add_dt DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Rubber Stamp Requests</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: rgba(26, 104, 198, 0.9);
            color: white;
        }
    </style>
</head>
<body>

<h2>Rubber Stamp Requests</h2>
<table>
    <tr>
        <th>Staff No</th>
        <th>Req For</th>
        <th>Category</th>
        <th>Justification</th>
        <th>Contact</th>
        <th>File</th>
        <th>Date</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['staffno']) ?></td>
            <td><?= htmlspecialchars($row['Req_for']) ?></td>
            <td><?= htmlspecialchars($row['sub_category']) ?></td>
            <td><?= htmlspecialchars($row['purpose_justif']) ?></td>
            <td><?= htmlspecialchars($row['contact_no']) ?></td>
            <td>
                <?php if (!empty($row['item_desc']) && file_exists("uploads/" . $row['item_desc'])): ?>
                    <a href="uploads/<?= $row['item_desc'] ?>" target="_blank">View</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['add_dt']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
