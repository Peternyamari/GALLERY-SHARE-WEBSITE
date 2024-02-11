<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    echo "You don't have permission to access this page.";
    exit;
}

// Include your database connection code here
$conn = new mysqli("localhost:4306", "root", "", "peterson");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user_id"])) {
    $user_id_to_delete = $_POST["delete_user_id"];

    // SQL query to delete the user
    $sql = "DELETE FROM nyamari WHERE id = $user_id_to_delete";

    if ($conn->query($sql) === true) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

// SQL query to fetch user data for display
$userResult = $conn->query("SELECT id, username FROM nyamari");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Delete User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #c0392b;
        }

        a {
            display: block;
            text-align: center;
            color: #3498db;
            text-decoration: none;
            margin-top: 20px;
            width: 100%;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<form action="" method="post">
    <h2>Delete User</h2>
    <label for="delete_user_id">Select User to Delete:</label>
    <select name="delete_user_id" id="delete_user_id">
        <?php
        while ($row = $userResult->fetch_assoc()) {
            echo "<option value=\"{$row['id']}\">{$row['username']}</option>";
        }
        ?>
    </select>
    <br><br>
    <input type="submit" value="Delete User">
</form>
<a href="admindashboard.php">Back ></a>

</body>
</html>



<?php
$conn->close();
?>
