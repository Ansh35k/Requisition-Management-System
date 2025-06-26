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

// Fetch requisition and user details
$sql = "SELECT r.*, s.user_name_staff_no, s.contact_details 
        FROM CPR_REQIUIS_MASTER r
        JOIN staff_users s ON r.staffno = s.staff_no
        WHERE r.Req_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $req_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Invalid request ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decision = $_POST['action']; // approve or reject
    $remark = $_POST['acknow_remark'];

    $new_leg_id = ($decision === 'approve') ? 2 : 3;

    $approver = $_SESSION['staff']; // Add this
    $stmt = $conn->prepare("UPDATE CPR_REQIUIS_MASTER SET leg_id = ?, acknow_remark = ?, app_by = ? WHERE Req_id = ?");
    $stmt->bind_param("issi", $new_leg_id, $remark, $approver, $req_id);

    if ($stmt->execute()) {
        echo "<script>alert('Request has been " . ($new_leg_id == 2 ? "approved" : "rejected") . ".');window.location='approval.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approval - Mementoes & Gifts</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 50px;
            font-family: Arial;
            margin: 0;
            background-image: url('images/wave3.jpg');
            background-size: cover;
            background-repeat: no-repeat;
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
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
        }

        .field {
            margin-top: 6px;
            padding: 8px;
            background: #f0f0f0;
            border-radius: 6px;
        }

        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .submit-btn {
            display: inline-block;
            margin: 20px 10px 0 0;
            padding: 10px 25px;
            background-color: #00aaff;
            color: white;
            font-weight: bold;
            font-size: 14px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .submit-btn.reject {
            background-color: red;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>APPROVAL - MEMENTOES & GIFTS</h2>
    
    <label>User Name & Staff No.:</label>
    <div class="field"><?= htmlspecialchars($data['user_name_staff_no']) ?></div>

    <label>Category / Item:</label>
    <div class="field"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $data['sub_category']))) ?></div>

    <label>Contact Details:</label>
    <div class="field"><?= htmlspecialchars($data['contact_details']) ?></div>

    <label>Item Details:</label>
    <div class="field"><?= nl2br(htmlspecialchars($data['item_desc'])) ?></div>

    <label>Event Details:</label>
    <div class="field"><?= nl2br(htmlspecialchars($data['event_detail'])) ?></div>

    <form method="post">
        <label>Remark / Comments of CPR:</label>
        <textarea name="acknow_remark" rows="3" required></textarea>

        <button class="submit-btn" type="submit" name="action" value="approve">Approve</button>
        <button class="submit-btn reject" type="submit" name="action" value="reject">Reject</button>
    </form>
</div>

</body>
</html>
