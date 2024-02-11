<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: adminlogin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        div {
            text-align: center;
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        a {
            background-color: #3498db;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    <p>This is the admin dashboard.</p>
    <!-- Add admin dashboard content here -->
</div>
<a href="deleteuser.php">Delete User</a>
<a href="deletemedia.php">Delete Media</a>
<a href="dashboard.php">Gallery Dashboard</a>
<a href="admseedashboard.php">Stats Dashboard</a>
<a href="logout.php">Logout</a>
</body>
</html>
