<?php
session_start();

// Check if the user is not logged in, redirect to login.php if true
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add centering styles */
        .whole_home {
            text-align: center;
            background-image: url("images/bg18.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            height: 600px;
            height:600px;
        }

        /* Style the button */
        .upload-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .menu a{
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="whole_home">
        <br>
        <h2>Welcome to the Home Page</h2>
        <p>This is the main page that requires login.</p>
        <h1>WELCOME TO OFFICE SHARE 8</h1>
        <a href="upload_media.php" class="upload-button">UPLOAD</a> <br> <br>
        
        <div class="menu">
        
        <a href="adminlogin.php">Admin Login</a>
        <span style="margin: 0 10px;"></span>
        <a href="logout.php">Logout</a>
         </div>

    </div>
</body>
</html>

