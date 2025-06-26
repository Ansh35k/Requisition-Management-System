<?php
session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // ensure this is your working connection file

$staff_no = $_SESSION['staff'];
$name_staff = '';
$contact = '';

$stmt = $conn->prepare("SELECT user_name_staff_no, contact_details FROM staff_users WHERE staff_no = ?");
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$stmt->bind_result($name_staff, $contact);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Form values
    $user = $_POST['user'];
    $other_staff = $_POST['other_staff'] ?? '';
    $category = $_POST['category'];
    $contact = $_POST['contact'];
    $justification = $_POST['justification'];
    $leg_id = 1;

    // Who is making the request?
    $req_for = strtoupper($user); // SELF or OTHERS
    $staffno = ($user === 'others') ? $other_staff : $_SESSION['staff'];

    // File upload
    $upload_dir = "uploads/"; // make sure this folder exists in BHEL/
    $file_name = $_FILES["card_file"]["name"];
    $file_tmp = $_FILES["card_file"]["tmp_name"];
    $target_path = $upload_dir . basename($file_name);

    if (move_uploaded_file($file_tmp, $target_path)) {
        // File uploaded successfully
        $category_id = 4;
        $category_desc = 'Visiting Card';
        $add_by = $_SESSION['staff'];
        $add_dt = date('Y-m-d H:i:s');

        $sql = "INSERT INTO CPR_REQIUIS_MASTER (
            req_for, staffno, category_id, cateory_desc, sub_category, 
            purpose_justif, contact_no, item_desc, add_by, add_dt, leg_id
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssisssssssi",
            $req_for,
            $staffno,
            $category_id,
            $category_desc,
            $category,
            $justification,
            $contact,
            $file_name,   // item_desc = uploaded file name (optional usage)
            $add_by,
            $add_dt,
            $leg_id
        );

        if ($stmt->execute()) {
            echo "<script>alert('Card requisition submitted successfully.');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<script>alert('File upload failed!');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visiting Card</title>
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
            margin-bottom: 50px;
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
            gap: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
        }

        input[type="text"],
        input[type="file"],
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
    <h2>VISITING CARD</h2>

    <form method="post" enctype="multipart/form-data">
       <div class="inline-group">
             <div class="form-group">
                <label>User:</label>
                <div class="radio-group">
                    <input type="radio" name="user" value="self" required> SELF<br>
                    <input type="radio" name="user" value="others"> OTHERS
                </div>
            </div>

            <div class="form-group">
                <label>User Name & Staff No. (If Others):</label>
                <input type="text" id="staff_name" name="staff_name" placeholder="User Name / Staff No." required>
            </div>
        </div>


        <!-- Category & Contact -->
        <div class="inline-group">
            <div class="form-group">
                <label>Category:</label>
                <div class="radio-group">
                    <input type="radio" name="category" value="new" required> New<br>
                    <input type="radio" name="category" value="change"> Change
                </div>
            </div>
            <div class="form-group">
                <label>Contact Details:</label>
                <input type="text" id="contact" name="contact" required>
            </div>
        </div>

        <!-- Justification -->
        <label>Justification:</label>
        <textarea name="justification" rows="3" required></textarea>

        <!-- Upload File -->
        <label>Upload the template for Visiting Card:</label>
        <input type="file" name="card_file" accept=".jpg,.jpeg,.png,.pdf" required>
        <!-- Submit -->
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
    document.querySelectorAll('input[name="user"]').forEach((radio) => {
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
