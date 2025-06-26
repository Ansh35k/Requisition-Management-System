<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Status of Requisition</title>
    <style>
        body {
            display: flex;
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

        .form-container {
            background: white;
            width: 700px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            color: red;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        ul {
            list-style-type: square;
            padding-left: 50px;
            font-size: 18px;
        }

        li {
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            color: #283d7f;
        }

        a:hover {
            text-decoration: underline;
            color: #283d7f;
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

<div class="form-container">
    <h2>STATUS OF REQUISITION</h2>
    <ul>
        <li><a href="furniture_sor.php">Office Furniture</a></li>
        <li><a href="cutlery_sor.php">Office Cutlery</a></li>
        <li><a href="stamp_sor.php">Rubber Stamp</a></li>
        <li><a href="visitingcard_sor.php">Visiting Card</a></li>
        <li><a href="videography_sor.php">Videography</a></li>
        <li><a href="photography_sor.php">Photography</a></li>
        <li><a href="snacks_sor.php">Beverages & Snacks (For Official Meetings)</a></li>
        <li><a href="gifts_sor.php">Mementoes & Gifts</a></li>
    </ul>
</div>

</body>
</html>
