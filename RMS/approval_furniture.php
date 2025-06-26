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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decision = $_POST['action'];  // 'approve' or 'reject'
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

$stmt = $conn->prepare("SELECT 
    m.Req_for, m.staffno, s.name, m.cateory_desc, m.sub_category, 
    m.contact_no, m.item_desc, m.purpose_justif
    FROM CPR_REQIUIS_MASTER m
    LEFT JOIN staff_users s ON m.staffno = s.staff_no
    WHERE m.Req_id = ?");
$stmt->bind_param("i", $req_id);
$stmt->execute();
$stmt->bind_result($user_type, $staff_no, $staff_name, $category, $sub_category, $contact, $item, $justification);
$stmt->fetch();
$stmt->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Approval - Furniture</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            font-family: Arial;
            background-image: url('images/wave3.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: black;
            padding: 30px 0;
        }

        .form-container {
            background: white;
            width: 650px;
            padding: 30px;
            border-radius: 10px;
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
            margin-top: 15px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f9f9f9;
        }

        textarea {
            resize: vertical;
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

        .submit-btn:hover {
            background-color: #008ecc;
        }

        .submit-btn.reject {
            background-color: red;
        }

    </style>
</head>
<body>

<div class="form-container">
    <h2>APPROVAL - OFFICE FURNITURE</h2>

    <label>User Name & Staff No.:</label>
    <input type="text" value="<?= htmlspecialchars("$staff_name / $staff_no") ?>" readonly>

    <label>Category:</label>
    <input type="text" value="<?= htmlspecialchars($sub_category) ?>" readonly>

    <label>Contact Details:</label>
    <input type="text" value="<?= htmlspecialchars($contact) ?>" readonly>

    <label>Item/Work Detail:</label>
    <textarea rows="3" readonly><?= htmlspecialchars($item) ?></textarea>

    <label>Justification:</label>
    <textarea rows="3" readonly><?= htmlspecialchars($justification) ?></textarea>

    <!-- Approval controls -->
    <form method="post">
        <label>Remark / Comments of CPR:</label>
        <textarea name="acknow_remark" rows="3" required></textarea>

        <button class="submit-btn" type="submit" name="action" value="approve">Approve</button>
        <button class="submit-btn reject" type="submit" name="action" value="reject">Reject</button>
    </form>

</div>

</body>
</html>
