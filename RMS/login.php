<?php
session_start();
$error = ''; 
include 'db.php'; // ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_no = trim($_POST['staff_no'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare("SELECT password, role FROM staff_users WHERE staff_no = ?");
    $stmt->bind_param("s", $staff_no);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($db_password, $role);
        $stmt->fetch();

        if ($password === $db_password) { // using plaintext passwords
            $_SESSION['staff'] = $staff_no;
            $_SESSION['role'] = $role; // 🔑 store role in session
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Staff number not found.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>C&PR Facilities System</title>
    <style>
        body {
            background-image: url('images/wave3.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: Arial;
            text-align: center;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h1 {
            font-weight: bold;
            color: #283d7f;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .login-container {
            display: flex;
            width: 750px;
            margin: auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-left {
            width: 40%;
            background-color: #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .login-left img {
            max-width: 100%;
            max-height: 150px;
        }

        .login-right {
            width: 60%;
            padding: 40px;
            background: rgba(26, 104, 198, 1); /* Your semi-transparent blue */
        }

        .login-right h2 {
            text-align: center;
            color: black;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: black;
        }

        input[type="text"], input[type="password"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            background: lightblue;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background: goldenrod;
            color: black;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }

        .error {
            color: yellow;
            margin-top: 10px;
        }
        
    </style>
</head>
<body>

<marquee><h1>C&PR FACILITIES SYSTEM</h1></marquee>

<div class="login-container">
    <!-- Left Logo Panel -->
    <div class="login-left">
        <img src="images/logo.jpg" alt="CompanyLogo">
    </div>

    <!-- Right Login Form -->
    <div class="login-right">
        <h2>Login</h2>
        <form method="post">
            <label>Staff No. :-</label><br>
            <input type="text" name="staff_no" required><br>
            <label>Password :-</label><br>
            <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
