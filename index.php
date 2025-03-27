<?php
session_start();
include 'dbconnect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT USERID, USERNAME, PASSWORD, UNIT FROM user WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userid, $db_username, $db_password, $unit);
        $stmt->fetch();

        // Verify password (assuming PASSWORD is hashed)
        if ($password == $db_password) {
            $_SESSION['USERID'] = $userid;
            $_SESSION['USERNAME'] = $db_username;
            $_SESSION['UNIT'] = $unit;

            header("Location: homepage.html"); // Redirect to dashboard
            exit();
        } else {
            $error_message = "Invalid username or password. Please try again.";
        }
    } else {
        $error_message = "Invalid username or password. Please try again.";

    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LKTN KPI Login</title>
    <link rel="icon" type="image/png" href="logo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9); /* Gradient background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }
        .login-container img {
            width: 120px;
            margin-bottom: 20px;
            border-radius: 50%; /* Circular logo */
            border: 3px solid #2e7d32; /* Green border around logo */
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #388e3c; /* Medium green */
            font-size: 24px;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .login-container input:focus {
            border-color: #388e3c; /* Green border on focus */
            outline: none;
            box-shadow: 0 0 5px rgba(56, 142, 60, 0.5);
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4caf50, #388e3c); /* Gradient button */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .login-container button:hover {
            background: linear-gradient(135deg, #388e3c, #2e7d32); /* Darker gradient on hover */
        }
        .login-container p {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .login-container a {
            color: #388e3c;
            text-decoration: none;
            font-weight: bold;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="logo.png" alt="LKTN Logo"> <!-- Replace 'logo.png' with the path to your logo -->
        <h2>LKTN KPI Login</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Enter your username" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>
        <p>Forgot your password? <a href="/reset-password">Reset it here</a>.</p>
    </div>
</body>
</html>