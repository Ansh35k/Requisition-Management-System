<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$staff_no = $_SESSION['staff'];
$name_staff = '';
$contact = '';

$stmt = $conn->prepare("SELECT user_name_staff_no, contact_details FROM staff_users WHERE staff_no = ?");
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$stmt->bind_result($name_staff, $contact);
$stmt->fetch();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form fields
    $req_for = $_POST['user_type']; // SELF/OTHERS
    $staffno = $_SESSION['staff']; // comes from login session
    $dept_code = 0; // Example department code, replace dynamically if needed
    $category_id = 1; // Let's assume 1 = Furniture
    $cateory_desc = "Furniture";
    $sub_category = $_POST['category']; // New or Change
    $item_desc = $_POST['item_details'];
    $purpose_justif = $_POST['justification'];
    $contact_no = $_POST['contact'];
    $add_by = $staffno;
    $add_dt = date('Y-m-d H:i:s');
    $leg_id = 1; // 1 = Submitted

    // Optional if needed in form: event, place, etc.
    $event_detail = NULL;
    $event_date = NULL;
    $event_place = NULL;
    $event_start_time = NULL;
    $service_hour = NULL;
    $person_no = NULL;

    $stmt = $conn->prepare("INSERT INTO CPR_REQIUIS_MASTER (
    Req_for, staffno, dept_code, category_id, cateory_desc, sub_category,
    item_desc, purpose_justif, contact_no, add_by, add_dt, event_detail,
    event_date, event_place, event_start_time, service_hour, person_no, leg_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
    "ssiisssssissssssii",
    $req_for, $staffno, $dept_code, $category_id, $cateory_desc, $sub_category,
    $item_desc, $purpose_justif, $contact_no, $add_by, $add_dt, $event_detail,
    $event_date, $event_place, $event_start_time, $service_hour, $person_no, $leg_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Furniture requisition submitted successfully.');</script>";
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
    <title>Furniture</title>
    <style>
        body {
            display: flex;
            justify-content: center;    /* Horizontal center */
            align-items: center;        /* Vertical center */
            height: 100vh;              /* Full viewport height */
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
            width: 650px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            
        }

        h2 {
            text-align: center;
            color:rgba(26, 104, 198, 1);
            text-decoration: underline;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
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

        .note {
            text-align: center;
            color: green;
            font-size: 14px;
            margin-top: 10px;
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
    <h2>OFFICE FURNITURE</h2>

    <form method="post">
        <!-- User selection -->
        <label>User:</label>
        <div class="radio-group">
            <input type="radio" name="user_type" value="self" required> SELF
            <input type="radio" name="user_type" value="others"> OTHERS
        </div>

        <!-- Staff details if OTHERS -->
        <label>User Name & Staff No. (If Others):</label>
        <input type="text" id="staff_name" name="staff_name" placeholder="User Name / Staff No." required>

        <!-- Category -->
        <label>Category:</label>
        <div class="radio-group">
            <input type="radio" name="category" value="New" required> New Furniture
            <input type="radio" name="category" value="Repair"> Repairing of Furniture
        </div>

        <!-- Contact -->
        <label>Contact Details:</label>
        <input type="text" id="contact" name="contact" required>

        <!-- Work details -->
        <label>Item/Work Detail:</label>
        <textarea name="item_details" rows="3" required></textarea>

        <!-- Justification -->
        <label>Justification:</label>
        <textarea name="justification" rows="3" required></textarea>

        <!-- Submit button -->
        <input type="submit" class="submit-btn" value="Submit">
    </form>
</div>

<script>
    const nameField = document.getElementById("staff_name");
    const contactField = document.getElementById("contact");

    function fillSelf() {
        nameField.value = "<?= htmlspecialchars($name_staff) ?>";
        contactField.value = "<?= htmlspecialchars($contact) ?>";
        nameField.readOnly = true;
        contactField.readOnly = true;
    }

    function fillOthers() {
        nameField.value = "";
        contactField.value = "";
        nameField.readOnly = false;
        contactField.readOnly = false;
    }

    // Attach to radio buttons
    document.querySelectorAll('input[name="user_type"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            if (this.value === 'self') {
                fillSelf();
            } else {
                fillOthers();
            }
        });
    });
</script>

</body>
</html>
