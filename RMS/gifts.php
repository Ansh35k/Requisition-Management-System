<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Make sure this connects to the correct DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $contact = $_POST['contact'];
    $item_details = $_POST['item_details'];
    $event_details = $_POST['event_details'];

    $req_for = 'SELF';
    $staffno = $_SESSION['staff'];
    $category_id = 8;
    $category_desc = 'Mementoes & Gifts';
    $add_by = $_SESSION['staff'];
    $add_dt = date('Y-m-d H:i:s');
    $leg_id = 1;

    $sql = "INSERT INTO CPR_REQIUIS_MASTER (
        req_for, staffno, category_id, cateory_desc, sub_category,
        contact_no, item_desc, event_detail, add_by, add_dt, leg_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssisssssssi",
        $req_for,
        $staffno,
        $category_id,
        $category_desc,
        $item,
        $contact,
        $item_details,
        $event_details,
        $add_by,
        $add_dt,
        $leg_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Mementoes & Gifts requisition submitted successfully.');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mementoes & Gifts</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial;
            background-image: url('images/wave3.jpg'); /* use your background */
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
            color:rgba(26, 104, 198, 1);
            text-decoration: underline;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
        }

        .radio-group {
            margin-top: 8px;
        }

        .radio-group input {
            margin-right: 8px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        textarea {
            resize: vertical;
        }

        .submit-btn {
            display: block;
            margin: 30px auto 10px;
            padding: 10px 30px;
            background-color: #00aaff;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #008ecc;
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
    <h2>MEMENTOES & GIFTS</h2>

    <form method="post">
        <!-- Category / Item -->
        <label>Category / Item:</label>
        <div class="radio-group">
            <input type="radio" name="item" value="small_brass" required> Brass Statue (Small size, ₹700–₹1100)<br>
            <input type="radio" name="item" value="medium_brass"> Brass Statue (Medium size, ₹1200–₹2000)<br>
            <input type="radio" name="item" value="big_brass"> Brass Statue (Big size, ₹2100–₹3500)<br>
            <input type="radio" name="item" value="large_brass"> Brass Statue (Large size, ₹4000–₹8000)<br>
            <input type="radio" name="item" value="shawl"> Shawl<br>
            <input type="radio" name="item" value="other"> Other item
        </div>

        <!-- Contact Details -->
        <label>Contact Details:</label>
        <input type="text" name="contact" required>

        <!-- Item Details -->
        <label>Item Details:</label>
        <textarea name="item_details" rows="3" required></textarea>

        <!-- Event Details -->
        <label>Event Details:</label>
        <textarea name="event_details" rows="3" required></textarea>

        <!-- Submit -->
        <input type="submit" class="submit-btn" value="Submit">
    </form>
</div>

</body>
</html>
