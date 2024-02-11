<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Handle media upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the file size exceeds the limit
    $maxFileSize = 41943040; // 40 MB
    if ($_SERVER['CONTENT_LENGTH'] > $maxFileSize) {
        $error = "File size exceeds the limit of 40 MB.";
    } else {
        // Check if the required keys are set
        if (isset($_POST["media_type"], $_POST["contact_info"], $_FILES["media_file"])) {
            $user_id = $_SESSION['user_id'];
            $media_type = $_POST["media_type"];
            $contact_info = $_POST["contact_info"];

            // Ensure the "uploads" directory exists
            $uploadDir = "uploads/";

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Handle file upload
            $fileName = uniqid() . '_' . basename($_FILES["media_file"]["name"]);
            $targetFile = $uploadDir . $fileName;

            // Check file type based on media type
            $allowedPhotoTypes = ["jpg", "jpeg", "png"];
            $allowedVideoTypes = ["mp4", "avi"];

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (($media_type === "photo" && !in_array($fileExtension, $allowedPhotoTypes))
                || ($media_type === "video" && !in_array($fileExtension, $allowedVideoTypes))) {
                $error = "Invalid file type for the selected media type.";
            } elseif (move_uploaded_file($_FILES["media_file"]["tmp_name"], $targetFile)) {
                // Save media details to the database (you need to adjust the SQL based on your schema)
                $conn = new mysqli("localhost:4306", "root", "", "peterson");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $insertSql = "INSERT INTO media (user_id, media_type, file_path, contact_info) VALUES ('$user_id', '$media_type', '$fileName', '$contact_info')";
                $conn->query($insertSql);

                $conn->close();

                $success = "Media uploaded successfully.";
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid request. Please make sure all required fields are provided.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Media</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url("images/bg17.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }

        select, input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            margin-top: 20px;
        }

        a {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<h2>Upload Media(Video/Photo)</h2>

<?php
// Display success or error messages
if (isset($success)) {
    echo "<p style='color: green;'>$success</p>";
} elseif (isset($error)) {
    echo "<p class='error'>$error</p>";
}
?>

<form action="upload_media.php" method="post" enctype="multipart/form-data">
    <label for="media_type">Select Media Type:</label>
    <select name="media_type" id="media_type" required>
        <option value="photo">Photo</option>
        <option value="video">Video</option>
    </select><br>

    <label for="media_file">Choose File:</label>
    <input type="file" name="media_file" id="media_file" accept="image/*,video/*" required><br>

    <label for="contact_info">Contact Information:</label>
    <textarea name="contact_info" id="contact_info" rows="3"></textarea><br>

    <input type="submit" value="Upload">
</form>

<a href="home.php">Home</a>
<span style="margin 0 10px"></span>
<a href="logout.php">Logout</a>
<span style="margin 0 10px"></span>
<a href="dashboard.php">Gallery</a>
</body>
</html>
