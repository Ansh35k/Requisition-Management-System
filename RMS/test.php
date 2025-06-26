<?php
session_start();

$error = '';

// Only process when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_no = $_POST['staff_no'] ?? '';
    $password = $_POST['password'] ?? '';

    // Dummy check (you can replace this with real database logic)
    if ($staff_no === 'admin' && $password === '1234') {
        $_SESSION['staff'] = $staff_no;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid Staff No. or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>C&PR Facilities System</title>
    <style>
        body {
            background-image: url('images/wave3.jpg');  /* Use your image filename here */
            background-size: cover;           /* Stretch image to cover full screen */
            background-repeat: no-repeat;     /* Prevent repeating */
            background-position: center;      /* Center the image */
            font-family: Arial;
            text-align: center;
            height: 100vh;                    /* Full height */
            margin: 0;
        }

        h1 {
            font-weight: bold;
            color: #283d7f;
            font-size: 32px;
        }

        .login-box {
            margin: 50px auto;
            width: 350px;
            padding: 20px;  

            /* Semi-transparent white background */
            background: rgba(26, 104, 198, 0.8); /* white with 80% opacity */

            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3); /* soft shadow */  
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
        }
        .error { color: red; }
        .icons {
            margin-top: 30px;
        }
        .icons img {
            width: 60px;
            margin: 0 10px;
        }
        .logo-footer {
            margin-top: 30px;
        }

        .logo-footer img {
            width: 200px;  /* Adjust size as needed */
            opacity: 0.9;
        }
        .logo-inside {
            width: 100px;           /* Adjust based on preference */
            margin-bottom: 10px;
            margin-top: -10px;
        }


    </style>
</head>
<body>

<marquee><h1>C&PR FACILITIES SYSTEM</h1></marquee>

<div class="login-box">

    <h2><u>Login</u></h2>

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

<!-- Footer logo -->
<div class="logo-footer">
    <img src="images/BHEL_Logo.png" alt="C&PR Logo">
</div>


</body>
</html>
