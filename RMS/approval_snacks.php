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

$sql = "SELECT r.*, s.user_name_staff_no, s.contact_details AS user_contact
        FROM CPR_REQIUIS_MASTER r
        JOIN staff_users s ON r.staffno = s.staff_no
        WHERE r.Req_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $req_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Invalid request ID.");
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approval – Beverages & Snacks</title>
    <style>
        body { font-family: Arial; margin:0; padding:20px; background: url('images/wave3.jpg') center/cover no-repeat; }
        .container { background:#fff; max-width:700px; margin:auto; padding:30px; border-radius:12px; box-shadow:0 0 15px rgba(0,0,0,0.3); }
        h2 { text-align:center; color:#1a68c6; text-decoration:underline; }
        label { font-weight:bold; margin-top:20px; display:block; }
        .field { margin-top:6px; padding:8px; background:#f0f0f0; border-radius:6px; }
        textarea { width:100%; padding:8px; margin-top:8px; border-radius:6px; border:1px solid #ccc; }
        .btn { margin:20px 10px 0 0; padding:10px 25px; border:none; border-radius:20px; color:#fff; font-weight:bold; cursor:pointer;}
        .approve { background:#00aaff; } .reject { background:#cc0033; }
    </style>
</head>
<body>
<div class="container">
    <h2>APPROVAL – BEVERAGES & SNACKS</h2>

    <label>User Name & Staff No.:</label>
    <div class="field"><?= htmlspecialchars($data['user_name_staff_no']) ?></div>

    <label>Event Details:</label>
    <div class="field"><?= nl2br(htmlspecialchars($data['event_detail'])) ?></div>

    <label>Event Date / Time / Pax:</label>
    <div class="field">
        <?= htmlspecialchars(date('d-m-Y', strtotime($data['event_date']))) ?> |
        <?= htmlspecialchars($data['event_start_time']) ?> hrs |
        <?= htmlspecialchars($data['person_no']) ?> pax
    </div>

    <label>Event Place:</label>
    <div class="field"><?= htmlspecialchars($data['event_place']) ?></div>

    <label>Items Requested:</label>
    <div class="field"><?= nl2br(htmlspecialchars($data['item_desc'])) ?></div>

    <label>Contact Details:</label>
    <div class="field"><?= htmlspecialchars($data['contact_no']) ?></div>

    <form method="post">
        <label>Remark / Comments of CPR:</label>
        <textarea name="acknow_remark" rows="3" required></textarea>

        <button class="btn approve" type="submit" name="action" value="approve">Approve</button>
        <button class="btn reject" type="submit" name="action" value="reject">Reject</button>
    </form>
</div>
</body>
</html>
