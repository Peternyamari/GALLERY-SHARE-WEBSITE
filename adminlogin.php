<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate admin credentials
    $admin_username = "admin";
    $admin_password = password_hash("admin321", PASSWORD_DEFAULT);

    $input_username = $_POST["username"];
    $input_password = $_POST["password"];

    if ($input_username == $admin_username && password_verify($input_password, $admin_password)) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $admin_username;
        $_SESSION["role"] = "admin";

        header("location: admindashboard.php");
        exit;
    } else {
        $error = "Invalid admin credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Login</title>
    <style>
            body {
            font-family: 'Arial', sans-serif;
            background-image: url("images/bg19.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
           
        }

        div {
            text-align: center;
            max-width: 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height:200px;
            margin:auto;
            margin-top:50px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            width: 300px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        p {
            margin-top: 20px;
            color: red;
        }

        a {
            color: #3498db;
            text-decoration: none;
            display: block;
            margin-top: 50px;
            background-color: #2980b9;
        }

        a:hover {
            text-decoration: underline;
        }
        #home-button {
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
    </style>
</head>
<body>

<div>
    <h2>Admin Login</h2>

    <?php if (isset($error)) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Admin Username" required>
        <input type="password" name="password" placeholder="Admin Password" required>
        <input type="submit" value="Login">
    </form>
    <a href="home.php" id="home-button">Home</a> 
</div>

</body>
</html>
