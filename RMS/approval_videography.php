<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$req_id = $_GET['req_id'] ?? '';
if (!$req_id) {
    die("Missing request ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decision = $_POST['action'];
    $remark = $_POST['remark'];
    $new_leg_id = ($decision === 'approve') ? 2 : 3;
    $approver = $_SESSION['staff']; // Set approver ID

    $update = $conn->prepare("UPDATE CPR_REQIUIS_MASTER SET leg_id = ?, acknow_remark = ?, app_by = ? WHERE req_id = ?");
    $update->bind_param("issi", $new_leg_id, $remark, $approver, $req_id);
    $update->execute();

    if ($update->execute()) {
        echo "<script>alert('Request has been " . ($new_leg_id == 2 ? "approved" : "rejected") . ".');window.location='approval.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit;
}

// Fetch requisition and user info
$sql = "SELECT r.*, s.user_name_staff_no, s.contact_details 
        FROM CPR_REQIUIS_MASTER r
        JOIN staff_users s ON r.staffno = s.staff_no
        WHERE r.req_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $req_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Invalid request ID.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approval - Videography</title>
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
            margin-top: 15px;
            display: block;
        }

        .field {
            padding: 8px;
            background: #f0f0f0;
            border-radius: 6px;
            margin-top: 5px;
        }

        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .submit-btn {
            padding: 10px 30px;
            font-weight: bold;
            font-size: 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin: 0 10px;
            background-color: #00aaff;
            color: white;
        }

        .submit-btn.reject {
            background-color: #cc0000;
        }

    </style>
</head>
<body>

<div class="form-container">
    <h2>APPROVAL - VIDEOGRAPHY</h2>

    <label>User Name & Staff No.:</label>
    <div class="field"><?= htmlspecialchars($data['user_name_staff_no']) ?></div>

    <label>Event Details:</label>
    <div class="field"><?= nl2br(htmlspecialchars($data['event_detail'])) ?></div>

    <label>Event Date:</label>
    <div class="field"><?= date("d-m-Y", strtotime($data['event_date'])) ?></div>

    <label>Start Time:</label>
    <div class="field"><?= htmlspecialchars($data['event_start_time']) ?></div>

    <label>Service Hours:</label>
    <div class="field"><?= htmlspecialchars($data['service_hour']) ?></div>

    <label>Place of Event:</label>
    <div class="field"><?= htmlspecialchars($data['event_place']) ?></div>

    <label>Contact Details:</label>
    <div class="field"><?= htmlspecialchars($data['contact_no']) ?></div>

    <form method="post">
        <label>Remark / Comments of CPR:</label>
        <textarea name="remark" rows="3" required></textarea>

        
        <button class="submit-btn" type="submit" name="action" value="approve">Approve</button>
        <button class="submit-btn reject" type="submit" name="action" value="reject">Reject</button>
        
    </form>

</div>

</body>
</html>
