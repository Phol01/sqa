<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["login-username"];
    $password = $_POST["login-password"];

    $sql = "SELECT user_id, fullname, username, email, password FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        session_start();
        $_SESSION["user_id"] = $user["user_id"];

    
        echo "<script>
                alert('Login successful. Welcome back, {$user["fullname"]}!');
                window.location.href = '../index.php';
              </script>";
        exit();
    } else {
        echo "Login failed.";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #3F87A6, #ebf8e1);
            display: flex;
            flex-direction: column; /* Center content vertically */
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold; /* Make the title bold */
            color: #333;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 400px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-switch {
            margin-top: 10px;
            color: #333;
        }

        .form-switch a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="title">Paybills Bank</div> <!-- Title at the top and centered, in bold -->
    <div class="form-container">
        <h2>Login</h2> <!-- Original title inside the modal -->

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="login-username">Username:</label>
                <input type="text" id="login-username" name="login-username" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="login-password" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>
        <div class="form-switch">
            <a href="signup.php">Don't have an account? Sign up</a>
        </div>
    </div>
    <script src="js/login.js"></script>
</body>
</html>
