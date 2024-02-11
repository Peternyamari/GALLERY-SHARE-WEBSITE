<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    echo "You don't have permission to access this page.";
    exit;
}

$conn = new mysqli("localhost:4306", "root", "", "peterson");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_media_id"])) {
    $media_id_to_delete = $_POST["delete_media_id"];

    $sql_media = "DELETE FROM media WHERE media_id = $media_id_to_delete";
    if ($conn->query($sql_media) === true) {
        echo "Media record deleted successfully.";
    } else {
        echo "Error deleting media record: " . $conn->error;
    }
}

$mediaResult = $conn->query("SELECT media_id FROM media");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Delete Media</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
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
            margin-top: 50px;
            margin-bottom: 20px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Delete Media</h2>

<form action="" method="post">
    <label for="delete_media_id">Select Media to Delete:</label>
    <select name="delete_media_id" id="delete_media_id">
        <?php
        while ($row = $mediaResult->fetch_assoc()) {
            echo "<option value=\"{$row['media_id']}\">Media ID: {$row['media_id']}</option>";
        }
        ?>
    </select>
    <br><br>
    <input type="submit" value="Delete Media">
</form>

<a href="admindashboard.php">Back ></a>

</body>
</html>


<?php
$conn->close();
?>
