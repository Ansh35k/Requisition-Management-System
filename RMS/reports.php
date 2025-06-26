<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$staffno = $_SESSION['staff'];

// Check role
$role_stmt = $conn->prepare("SELECT role FROM staff_users WHERE staff_no = ?");
$role_stmt->bind_param("s", $staffno);
$role_stmt->execute();
$role_stmt->bind_result($role);
$role_stmt->fetch();
$role_stmt->close();

if ($role !== 'admin') {
    echo "Access denied.";
    exit;
}

$categories = [
    1 => 'Furniture',
    2 => 'Cutlery',
    3 => 'Stamps',
    4 => 'VisitingCard',
    5 => 'Videography',
    6 => 'Photography',
    7 => 'Snacks',
    8 => 'Gifts'
];

$results = [];
$selected_category = '';
$entered_staffno = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_category = $_POST['category'];
    $entered_staffno = trim($_POST['staffno']);

    $query = "SELECT r.Req_id, r.category_id, DATE_FORMAT(r.add_dt, '%d-%m-%Y') AS req_date,
                     r.sub_category, r.item_desc, r.purpose_justif, r.event_detail, r.event_date,
                     l.status_description, r.acknow_remark
              FROM CPR_REQIUIS_MASTER r
              JOIN LEG_STATUS_MASTER l ON r.leg_id = l.leg_id
              WHERE r.category_id = ? AND r.staffno = ?
              ORDER BY r.add_dt DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $selected_category, $entered_staffno);
    $stmt->execute();
    $results = $stmt->get_result();
}

if (isset($_POST['download']) && $results && $results->num_rows > 0) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=requisition_report.xls");
    echo "<table border='1'>";
    echo "<tr><th>Req. No.</th><th>Req. Date</th>";

    switch ($selected_category) {
        case 1: echo "<th>Category</th><th>Item Desc</th><th>Justification</th>"; break;
        case 2: echo "<th>Category</th><th>Item Desc</th>"; break;
        case 3: case 4: echo "<th>Category</th><th>Item /Works Details</th>"; break;
        case 5: case 6: case 7: echo "<th>Event Detail</th><th>Event Date</th>"; break;
        case 8: echo "<th>Category</th><th>Item Desc</th>"; break;
    }

    echo "<th>Status</th><th>CPR Remark</th></tr>";

    while ($row = $results->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Req_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['req_date']) . "</td>";

        switch ($selected_category) {
            case 1:
                echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                echo "<td>" . htmlspecialchars($row['purpose_justif']) . "</td>";
                break;
            case 2:
                echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                break;
            case 3: case 4:
                echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['purpose_justif']) . "</td>";
                break;
            case 5: case 6: case 7:
                echo "<td>" . htmlspecialchars($row['event_detail']) . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['event_date'])) . "</td>";
                break;
            case 8:
                echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                break;
        }

        echo "<td>" . htmlspecialchars($row['status_description']) . "</td>";
        echo "<td>" . nl2br(htmlspecialchars($row['acknow_remark'])) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit;
}


?>

<!DOCTYPE html>
<html>
    
<head>
    <title>Requisition Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/wave3.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            margin: 60px auto;
            padding: 30px;
            max-width: 1100px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            color: red;
            text-transform: uppercase;
            text-decoration: underline;
        }

        form {
            margin-bottom: 25px;
            text-align: center;
        }

        input, select {
            padding: 10px;
            margin: 5px;
            width: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background: #1a68c6;
            color: white;
        }

        tr:nth-child(even) {
            background: #e8f4fc;
        }

        tr:nth-child(odd) {
            background: #f6fbff;
        }
        .top-right {
            position: absolute;
            top: 20px;
            right: 30px;
            z-index: 10;
        }

        .top-right button {
            padding: 8px 16px;
            background-color: whitesmoke;
            color: black;
            border: black;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-family: "Lucida Console", "Courier New", monospace;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            transition: background 0.3s;
        }

        .top-right button:hover {
            background-color: darkmagenta;
        }
    </style>
</head>
<body>

<div class="top-right">
    <form method="post" action="dashboard.php">
        <button type="submit">Dashboard</button>
    </form>
</div>

<div class="container">
    <h2>Requisition Report</h2>

    <form method="post">
        <input type="text" name="staffno" placeholder="Enter Staff Number" value="<?= htmlspecialchars($entered_staffno) ?>" required>
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $id => $name): ?>
                <option value="<?= $id ?>" <?= ($id == $selected_category) ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="search" value="Search">
        <input type="submit" name="download" value="Download Excel">
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php if ($results && $results->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Req. No.</th>
                    <th>Req. Date</th>
                    <?php
                        switch ($selected_category) {
                            case 1: echo "<th>Category</th><th>Item Desc</th><th>Justification</th>"; break;
                            case 2: echo "<th>Category</th><th>Item Desc</th>"; break;
                            case 3: case 4: echo "<th>Category</th><th>Item /Works Details</th>"; break;
                            case 5: case 6: case 7: echo "<th>Event Detail</th><th>Event Date</th>"; break;
                            case 8: echo "<th>Category</th><th>Item Desc</th>"; break;
                        }
                    ?>
                    <th>Status</th>
                    <th>CPR Remark</th>
                    <th>Action</th>
                </tr>

                <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Req_id']) ?></td>
                        <td><?= htmlspecialchars($row['req_date']) ?></td>
                        <?php
                            switch ($selected_category) {
                                case 1:
                                    echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['purpose_justif']) . "</td>";
                                    break;
                                case 2:
                                    echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                                    break;
                                case 3: case 4:
                                    echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['purpose_justif']) . "</td>";
                                    break;
                                case 5: case 6: case 7:
                                    echo "<td>" . htmlspecialchars($row['event_detail']) . "</td>";
                                    echo "<td>" . date('d-m-Y', strtotime($row['event_date'])) . "</td>";
                                    break;
                                case 8:
                                    echo "<td>" . htmlspecialchars($row['sub_category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                                    break;
                            }
                        ?>
                        <td><?= htmlspecialchars($row['status_description']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['acknow_remark'])) ?></td>
                        <td><a href="action_<?= strtolower(str_replace(" ", "", $categories[$selected_category])) ?>.php?req_id=<?= $row['Req_id'] ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;color:crimson;">No records found for the selected criteria.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
