<?php
session_start();

// Check if the user is already logged in, redirect to home.php if true
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}

// Database connection details
$conn = new mysqli("localhost:4306", "root", "", "peterson");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the error and success messages
$error = $success = "";

// Process signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    // Check if the username already exists
    $checkUsername = "SELECT * FROM nyamari WHERE username = '$username'";
    $resultUsername = $conn->query($checkUsername);

    // Check if the email already exists
    $checkEmail = "SELECT * FROM nyamari WHERE email = '$email'";
    $resultEmail = $conn->query($checkEmail);

    if ($resultUsername->num_rows > 0) {
        $error = "Username already exists. Please choose a different username.";
    } elseif ($resultEmail->num_rows > 0) {
        $error = "Email already exists. Please use a different email address.";
    } elseif ($_POST["password"] !== $_POST["confirm_password"]) {
        $error = "Password and confirm password do not match";
    } else {
        // Insert user data into the database
        $sql = "INSERT INTO nyamari (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

        if ($conn->query($sql) === true) {
            $success = "Signup successful. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Signup</title>
    <style>
        #npp {
            height:30px;
            width:300px;
            border-radius:4px;
            border:1px solid grey;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="whole_signup">
        <h2>Signup</h2>

        <?php
        // Display success or error messages
        if (!empty($success)) {
            echo "<p style='color: green;'>$success</p>";
        } elseif (!empty($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        
        <div class="signup">
            <form action="" method="post">
                <input type="text" id="npp" name="username" placeholder="Username" required><br> <br>
                <input type="email" id="npp" name="email" placeholder="Email" required><br> <br>
                <input type="password" id="npp" name="password" placeholder="Password" required><br> <br>
                <input type="password" id="npp" name="confirm_password" placeholder="Confirm Password" required><br> <br>
                
                <!-- Add a dropdown for selecting the role -->
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select><br><br>

                <input type="submit" value="Signup" id="npp" style="background-color:darkslateblue;color:white;">
            </form>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
