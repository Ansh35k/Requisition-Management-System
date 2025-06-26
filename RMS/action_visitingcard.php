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

// Check if request is approved
$check = $conn->prepare("SELECT leg_id FROM CPR_REQIUIS_MASTER WHERE Req_id = ?");
$check->bind_param("i", $req_id);
$check->execute();
$check->bind_result($leg_id);
$check->fetch();
$check->close();

// Fetch full data with user and approver details
$sql = "SELECT 
            r.staffno,
            s.name AS staff_name,
            s.contact_details,
            r.sub_category,
            r.item_desc,
            r.purpose_justif,
            r.acknow_remark,
            a.name AS approved_by_name
        FROM CPR_REQIUIS_MASTER r
        JOIN staff_users s ON r.staffno = s.staff_no
        LEFT JOIN staff_users a ON r.app_by = a.staff_no
        WHERE r.Req_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $req_id);
$stmt->execute();
$stmt->bind_result($staff_no, $staff_name, $contact, $sub_category, $file_name, $justification, $cpr_remark, $approved_by_name);
$stmt->fetch();
$stmt->close();

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $next_leg_id = $_POST['next_leg_id'] ?? '';
    $final_remark = $_POST['final_remark'] ?? '';

    if (!in_array($next_leg_id, ['44', '45', '46'])) {
        echo "Invalid status selected.";
        exit;
    }

    $update = $conn->prepare("UPDATE CPR_REQIUIS_MASTER SET leg_id = ?, acknow_remark = ? WHERE Req_id = ?");
    $update->bind_param("isi", $next_leg_id, $final_remark, $req_id);
    if ($update->execute()) {
        echo "<script>alert('Requisition successfully updated.'); window.location='action.php';</script>";
        exit;
    } else {
        echo "Error: " . $update->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Action - Visiting Card</title>
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
            padding: 30px 0;
        }

        .form-container {
            background: white;
            width: 700px;
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

        a.download-link {
            display: inline-block;
            margin-top: 5px;
            color: #1a68c6;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>ACTION - VISITING CARD</h2>

    <label>User Name & Staff No.:</label>
    <input type="text" value="<?= htmlspecialchars("$staff_name / $staff_no") ?>" readonly>

    <label>Category:</label>
    <input type="text" value="<?= htmlspecialchars($sub_category) ?>" readonly>

    <label>Contact Details:</label>
    <input type="text" value="<?= htmlspecialchars($contact) ?>" readonly>

    <label>Justification:</label>
    <textarea rows="3" readonly><?= htmlspecialchars($justification) ?></textarea>

    <label>Uploaded File:</label>
    <input type="text" value="<?= htmlspecialchars($file_name) ?>" readonly>
    <a class="download-link" href="uploads/<?= htmlspecialchars($file_name) ?>" target="_blank">View File</a>

    <label>Remarks of CPR (Previous Stage):</label>
    <textarea rows="2" readonly><?= htmlspecialchars($cpr_remark) ?></textarea>

    <label>Approved by:</label>
    <input type="text" value="<?= htmlspecialchars($approved_by_name) ?>" readonly>
    
    <form method="post">
        <label>Update Status:</label>
        <div class="radio-group">
            <label><input type="radio" name="next_leg_id" value="44" required> Design Finalized</label>
            <label><input type="radio" name="next_leg_id" value="45"> Printed</label>
            <label><input type="radio" name="next_leg_id" value="46"> Issued</label>
        </div>

        <label>Final Comments:</label>
        <textarea name="final_remark" rows="3" required></textarea>

        <button class="submit-btn" type="submit">Close</button>
    </form>
</div>

</body>
</html>
