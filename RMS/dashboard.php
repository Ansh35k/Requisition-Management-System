<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

//If you remove the 3 lines above then it wont validate the login/logout

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure this connects to your MySQL

$staff_no = $_SESSION['staff'];
$name = 'User'; // Default fallback

// Fetch name from database
$stmt = $conn->prepare("SELECT name FROM staff_users WHERE staff_no = ?");
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$stmt->bind_result($name_from_db);
if ($stmt->fetch()) {
    $name = $name_from_db;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/wave3.jpg'); /* Make sure this image path is correct */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: black;
            padding: 40px;
            margin: 0;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            color: #283d7f;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            margin-top: 50px;
            color: #283d7f;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 40px;
        }

        .box {
            position: relative;
            aspect-ratio: 4/3;
            height: 250px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .box:hover {
            transform: scale(1.1);
        }

        .box .overlay {
            position: absolute;
            bottom: -100%;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 14px;
            color: #fff;
            transition: bottom 0.4s ease, background 0.4s ease, font-size 0.3s;
        }
        

        .box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .box:hover img {
            transform: scale(1.1);
        }

        .box:hover .overlay {
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            font-size: 16px;
        }
        .small-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px 40px;
        }

        .small-box {
            height: 100px;
            background-color: rgba(198, 3, 3, 0.8);
            border-radius: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .small-box:hover {
            transform: scale(1.05);
        }

        a {
            text-decoration: none;
            color: inherit;
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
            background-color: red;
        }

    </style>
</head>
<body>

<!-- Logout button top-right -->
<div class="top-right">
    <form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>
</div>

<h1>Dashboard</h1>

<h2>Welcome, <?= htmlspecialchars($name) ?>!</h2>

<!-- 8 Big Boxes -->
<div class="grid">
    <a href="furniture.php">
        <div class="box">
            <img src="images/furniture.webp" alt="Furniture">
            <div class="overlay">Office Furniture</div>
        </div>
    </a>

    <a href="cutlery.php">
        <div class="box">
            <img src="images/cutlery.png" alt="Cutlery">
            <div class="overlay">Office Cutlery</div>
        </div>
    </a>

    <a href="stamp.php">
        <div class="box">
            <img src="images/stamp.png" alt="Rubber Stamp">
            <div class="overlay">Rubber Stamp</div>
        </div>
    </a>

    <a href="visitingcard.php">
        <div class="box">
            <img src="images/visitingcard.jpg" alt="Visiting Card">
            <div class="overlay">Visiting Card</div>
        </div>
    </a>

    <a href="videography.php">
        <div class="box">
            <img src="images/videography.jpg" alt="Videography">
            <div class="overlay">Videography</div>
        </div>
    </a>

    <a href="photography.php">
        <div class="box">
            <img src="images/photography.png" alt="Photography">
            <div class="overlay">Photography</div>
        </div>
    </a>

    <a href="snacks.php">
        <div class="box">
            <img src="images/snacks.jpg" alt="Snacks">
            <div class="overlay">Beverages and Snacks</div>
        </div>
    </a>

    <a href="gifts.php">
        <div class="box">
            <img src="images/gifts.jpg" alt="Gifts">
            <div class="overlay">Mementoes and Gifts</div>
        </div>
    </a>
</div>

<!-- 4 Small Red Boxes -->
<div class="small-grid">
    <a href="status.php">
        <div class="small-box">Status of Requisition</div>
    </a>
    <a href="approval.php">
        <div class="small-box">Pending for Approval</div>
    </a>
    <a href="action.php">
        <div class="small-box">Pending for Action</div>
    </a>
    <a href="reports.php">
        <div class="small-box">Reports</div>
    </a>
</div>

</body>
</html>
