<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$req_id = $_GET['req_id'] ?? null;
if (!$req_id) {
    echo "Invalid Request ID.";
    exit;
}

// Check if it's approved
$check = $conn->prepare("SELECT leg_id FROM CPR_REQIUIS_MASTER WHERE Req_id = ?");
$check->bind_param("i", $req_id);
$check->execute();
$check->bind_result($leg_id);
$check->fetch();
$check->close();

// Fetch details including approver name
$stmt = $conn->prepare("SELECT 
    m.staffno,
    s.name,
    m.event_detail,
    m.event_date,
    m.event_start_time,
    m.service_hour,
    m.event_place,
    m.contact_no,
    m.acknow_remark,
    a.name AS approved_by_name
FROM CPR_REQIUIS_MASTER m
LEFT JOIN staff_users s ON m.staffno = s.staff_no
LEFT JOIN staff_users a ON m.app_by = a.staff_no
WHERE m.Req_id = ?");
$stmt->bind_param("i", $req_id);
$stmt->execute();
$stmt->bind_result(
    $staff_no,
    $staff_name,
    $event_detail,
    $event_date,
    $start_time,
    $service_hour,
    $place,
    $contact,
    $acknow_remark,
    $approved_by_name
);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $next_leg_id = $_POST['next_leg_id'] ?? '';
    $final_remark = $_POST['final_remark'] ?? '';

    if (!in_array($next_leg_id, ['54', '55'])) {
        echo "Invalid status selected.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE CPR_REQIUIS_MASTER SET leg_id = ?, acknow_remark = ? WHERE Req_id = ?");
    $stmt->bind_param("isi", $next_leg_id, $final_remark, $req_id);
    if ($stmt->execute()) {
        echo "<script>alert('Requisition successfully updated.');window.location='action.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Action - Videography</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            padding-top: 50px;
            font-family: Arial;
            margin: 0;
            background-image: url('images/wave3.jpg');
            background-size: cover;
            background-position: center;
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
            margin-top: 15px;
            display: block;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #f0f0f0;
        }

        textarea {
            resize: vertical;
        }

        .radio-group {
            margin-top: 10px;
        }

        .radio-group label {
            font-weight: normal;
            margin-right: 15px;
        }

        .submit-btn {
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #00aaff;
            color: white;
            font-weight: bold;
            font-size: 14px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #008ecc;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>ACTION - VIDEOGRAPHY</h2>

    <label>User Name & Staff No.:</label>
    <input type="text" value="<?= htmlspecialchars("$staff_name / $staff_no") ?>" readonly>

    <label>Event Details:</label>
    <textarea rows="2" readonly><?= htmlspecialchars($event_detail) ?></textarea>

    <label>Event Date:</label>
    <input type="text" value="<?= date("d-m-Y", strtotime($event_date)) ?>" readonly>

    <label>Start Time:</label>
    <input type="text" value="<?= htmlspecialchars($start_time) ?>" readonly>

    <label>Service Hours:</label>
    <input type="text" value="<?= htmlspecialchars($service_hour) ?>" readonly>

    <label>Place of Event:</label>
    <input type="text" value="<?= htmlspecialchars($place) ?>" readonly>

    <label>Contact Details:</label>
    <input type="text" value="<?= htmlspecialchars($contact) ?>" readonly>

    <label>Remarks of CPR (Previous Stage):</label>
    <textarea rows="2" readonly><?= htmlspecialchars($acknow_remark) ?></textarea>

    <label>Approved by:</label>
    <input type="text" value="<?= htmlspecialchars($approved_by_name) ?>" readonly>

    <form method="post">
        <label>Update Status:</label>
        <div class="radio-group">
            <label><input type="radio" name="next_leg_id" value="54" required> Video Completed</label>
            <label><input type="radio" name="next_leg_id" value="55"> Data Shared with User</label>
        </div>

        <label>Final Comments:</label>
        <textarea name="final_remark" rows="3" required></textarea>

        <button class="submit-btn" type="submit">Close</button>
    </form>
</div>

</body>
</html>
