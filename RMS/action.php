<?php
session_start();
if (!isset($_SESSION['staff']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

include 'db.php';

// Define categories
$categories = [
    1 => "Furniture",
    2 => "Cutlery",
    3 => "Rubber Stamp",
    4 => "Visiting Card",
    5 => "Videography",
    6 => "Photography",
    7 => "Beverages & Snacks",
    8 => "Gifts"
];

// Get counts of submitted (leg_id = 1) requisitions for each category
$pending_counts = [];

foreach ($categories as $id => $name) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM CPR_REQIUIS_MASTER WHERE category_id = ? AND leg_id = 2");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $pending_counts[$id] = $count;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending for Action</title>
    <style>
        body {
            background-image: url('images/wave3.jpg');
            background-size: cover;
            font-family: Arial;
            padding: 40px;
            color: black;
        }

        h2 {
            text-align: center;
            color: rgba(26, 104, 198, 1);
            margin-bottom: 30px;
            text-decoration: underline;
        }

        .link-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
        }

        .category-link {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 12px 20px;
            background-color: rgba(26, 104, 198, 0.1);
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            color: black;
            transition: background-color 0.3s ease;
        }

        .category-link:hover {
            background-color: rgba(26, 104, 198, 0.2);
        }

        .pending {
            color: red;
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


<h2>Pending for Action (CPR Only)</h2>

<div class="link-container">
    <?php foreach ($categories as $id => $name): ?>
        <a class="category-link" href="action_list_<?= strtolower(str_replace(' ', '', $name)) ?>.php">
            <span><?= $name ?></span>
            <span class="pending"><?= $pending_counts[$id] ?> Pending</span>
        </a>
    <?php endforeach; ?>
</div>


</body>
</html>
