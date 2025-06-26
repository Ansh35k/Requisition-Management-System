<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;    
}

include 'db.php'; // make sure this file has correct DB credentials

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_details = $_POST['event_details'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $pax = $_POST['pax'];
    $place = $_POST['place'];
    $beverages = isset($_POST['beverage']) ? implode(', ', $_POST['beverage']) : '';
    $snacks = $_POST['snacks'];
    $contact = $_POST['contact'];

    $req_for = 'SELF';
    $staffno = $_SESSION['staff'];
    $category_id = 7;
    $category_desc = 'Beverages & Snacks';
    $add_by = $_SESSION['staff'];
    $add_dt = date('Y-m-d H:i:s');
    $leg_id = 1;

    // Combine beverages and snacks into one item_desc field
    $item_desc = "Beverages: $beverages | Snacks: $snacks";

    $sql = "INSERT INTO CPR_REQIUIS_MASTER (
        req_for, staffno, category_id, cateory_desc, item_desc, 
        event_detail, event_date, event_start_time, person_no, 
        event_place, contact_no, add_by, add_dt, leg_id 
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssisssssissssi",
        $req_for,
        $staffno,
        $category_id,
        $category_desc,
        $item_desc,
        $event_details,
        $event_date,
        $event_time,
        $pax,
        $place,
        $contact,
        $add_by,
        $add_dt,
        $leg_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Beverages & Snacks requisition submitted successfully.');</script>";
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
    <title>Beverages & Snacks</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial;
            background-image: url('images/wave3.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: black;
        }

        .form-container {
            background: white;
            width: 750px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            color: rgba(26, 104, 198, 1);
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

        .inline-group {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        textarea {
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
    <h2>BEVERAGE & SNACKS</h2>

    <form method="post">
        <!-- Event Details -->
        <label>Event Details:</label>
        <textarea name="event_details" rows="2" required></textarea>

        <!-- Date, Time, Pax -->
        <div class="inline-group">
            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="event_date" required>
            </div>
            <div class="form-group">
                <label>Event Time:</label>
                <input type="time" name="event_time" required>
            </div>
            <div class="form-group">
                <label>Nos. of Pax:</label>
                <input type="number" name="pax" min="1" required>
            </div>
        </div>

        <!-- Place of Event -->
        <label>Place of Event:</label>
        <input type="text" name="place" required>

        <!-- Category / Items -->
        <label>Category/Items:- </label>
        <div class="inline-group">
            <div class="form-group">
                <label>Beverage:</label>
                <div class="radio-group">
                    <input type="checkbox" name="beverage[]" value="Tea"> Tea<br>
                    <input type="checkbox" name="beverage[]" value="Coffee"> Coffee<br>
                    <input type="checkbox" name="beverage[]" value="Cold drink"> Cold drink
                </div>
            </div>
            <div class="form-group">
                <label>Snacks:</label>
                <input type="text" name="snacks">
            </div>
        </div>

        <!-- Contact Details -->
        <label>Contact Details of Requisitioner:</label>
        <input type="text" name="contact" required>

        <!-- Submit -->
        <input type="submit" class="submit-btn" value="Submit">
    </form>
</div>

</body>
</html>
