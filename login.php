<?php
session_start();

// Check if the user is already logged in, redirect to home.php if true
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}

// Database connection details


// Connect to the database
$conn = new mysqli("localhost:4306","root","","peterson");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Retrieve user data from the database
    $sql = "SELECT id, username, password FROM nyamari WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row["password"])) {
            // Store session data
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] = $row["id"];

            // Redirect to home.php
            header("location: home.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
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
    <title>Login</title>
</head>
<style>
    #txt{
    height:30px;
    width:300px;
    border-radius:4px;
    border:1px solid grey;
    }
</style>
<body>
<div class="whole_login">
<h2 style="color:white;">Login Page</h2>

<?php
// Display an error message if login fails
if (isset($error)) {
    echo "<p style='color: red;'>$error</p>";
}
?>
<div class="login">
<form action="" method="post">
    <h4>Login Here</h4> <br>
    <input type="text" id="txt" name="username" placeholder="Username" required><br>
<br>
    <input type="password" id="txt" name="password" placeholder="Password" required><br> <br>

    <input type="submit" value="Login" id="txt" style="background-color:darkslateblue;color:white;">
</form>
<a href="forgot_password.php">Forgot password</a>

<p>Don't have an account? <a href="signup.php">Signup here</a></p>
</div>
</div>
<a href="adminlogin.php">Admin Login</a>
</body>
</html>
