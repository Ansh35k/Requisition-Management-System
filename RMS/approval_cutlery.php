<?php
session_start();
if (!isset($_SESSION['staff']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_id = $_POST['req_id'];
    $remark = trim($_POST['acknow_remark']);
    $action = $_POST['action'];

    $new_leg_id = ($action === 'approve') ? 2 : 3;

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

// GET: Show requisition
if (!isset($_GET['req_id'])) {
    echo "Request ID missing.";
    exit;
}

$req_id = $_GET['req_id'];
$sql = "SELECT * FROM CPR_REQIUIS_MASTER WHERE Req_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $req_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Invalid Request ID.";
    exit;
}

$staffno = $data['staffno'];
$staff_name = '';
$userQuery = $conn->prepare("SELECT user_name_staff_no FROM staff_users WHERE staff_no = ?");
$userQuery->bind_param("s", $staffno);
$userQuery->execute();
$userQuery->bind_result($staff_name);
$userQuery->fetch();
$userQuery->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approval -  Cutlery</title>
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

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f0f0f0;
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
    <h2>APPROVAL - CUTLERY</h2>

    <form method="post">
        <input type="hidden" name="req_id" value="<?= htmlspecialchars($req_id) ?>">

        <label>User Name & Staff No.:</label>
        <input type="text" readonly value="<?= htmlspecialchars($staff_name) ?>">

        <label>Category / Item:</label>
        <input type="text" readonly value="<?= htmlspecialchars($data['sub_category']) ?>">

        <label>Contact Details:</label>
        <input type="text" readonly value="<?= htmlspecialchars($data['contact_no']) ?>">

        <label>Item Details:</label>
        <textarea rows="3" readonly><?= htmlspecialchars($data['item_desc']) ?></textarea>

        <label>Purpose:</label>
        <textarea rows="3" readonly><?= htmlspecialchars($data['purpose_justif']) ?></textarea>

        <label>Remark / Comments of CPR:</label>
        <textarea name="acknow_remark" rows="3" required></textarea>

        <button class="submit-btn" type="submit" name="action" value="approve">Approve</button>
        <button class="submit-btn reject" type="submit" name="action" value="reject">Reject</button>
    </form>
</div>
</body>
</html>
