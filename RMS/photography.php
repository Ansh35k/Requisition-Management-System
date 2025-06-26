<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_details = $_POST['event_details'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $service_hours = $_POST['service_hours'];
    $event_place = $_POST['event_place'];
    $contact = $_POST['contact_details'];

    $req_for = 'SELF'; // or make dynamic if needed
    $staffno = $_SESSION['staff'];
    $category_id = 6;
    $category_desc = 'Photography';
    $add_by = $_SESSION['staff'];
    $add_dt = date('Y-m-d H:i:s');
    $leg_id = 1;

    $sql = "INSERT INTO CPR_REQIUIS_MASTER (
        req_for, staffno, category_id, cateory_desc, event_detail, 
        event_date, event_start_time, service_hour, event_place, 
        contact_no, add_by, add_dt, leg_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssisssssssssi",
        $req_for,
        $staffno,
        $category_id,
        $category_desc,
        $event_details,
        $event_date,
        $start_time,
        $service_hours,
        $event_place,
        $contact,
        $add_by,
        $add_dt,
        $leg_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Photography requisition submitted successfully.');</script>";
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
    <title>Photography</title>
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
            width: 700px;
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

        .inline-group {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .inline-group input {
            width: 100%;
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

        .form-group {
            flex: 1;
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
    <h2>PHOTOGRAPHY</h2>

    <form method="post">
        <!-- Event Details -->
        <label>Event Details:</label>
        <textarea name="event_details" rows="3" required></textarea>

        <!-- Date/Time/Hours -->
        <div class="inline-group">
            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="event_date" required>
            </div>
            <div class="form-group">
                <label>Start Time:</label>
                <input type="time" name="start_time" required>
            </div>
            <div class="form-group">
                <label>Service Hours:</label>
                <input type="number" name="service_hours" min="1" max="24" required>
            </div>
        </div>

        <!-- Place -->
        <label>Place of Event:</label>
        <input type="text" name="event_place" required>

        <!-- Contact -->
        <label>Contact Details of Requisitioner:</label>
        <input type="text" name="contact_details" required>

        <!-- Submit -->
        <input type="submit" class="submit-btn" value="Submit">
    </form>
</div>

</body>
</html>
