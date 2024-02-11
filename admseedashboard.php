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

// Check if a search query is submitted
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];

    // Modify the default query to include the search condition
    $sql = "SELECT nyamari.id AS user_id, username, email, COUNT(media.media_id) AS num_uploads
            FROM nyamari 
            LEFT JOIN media ON nyamari.id = media.user_id
            WHERE nyamari.id = '$searchQuery' OR username LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%'
            GROUP BY nyamari.id, username, email";
} else {
    // Set the default query
    $sql = "SELECT nyamari.id AS user_id, username, email, COUNT(media.media_id) AS num_uploads
            FROM nyamari 
            LEFT JOIN media ON nyamari.id = media.user_id
            GROUP BY nyamari.id, username, email";
}

$result = $conn->query($sql);

if (!$result) {
    printf("Error: %s\n", $conn->error);
    exit();
}

// Query for user statistics
$userStatsQuery = "SELECT COUNT(DISTINCT id) AS total_users, COUNT(media_id) AS total_uploads
                  FROM nyamari 
                  LEFT JOIN media ON nyamari.id = media.user_id";

$userStatsResult = $conn->query($userStatsQuery);

if (!$userStatsResult) {
    printf("Error: %s\n", $conn->error);
    exit();
}

function getNumberOfUploads($userId, $conn) {
    $uploadsQuery = "SELECT COUNT(media_id) AS num_uploads FROM media WHERE user_id = $userId";
    $uploadsResult = $conn->query($uploadsQuery);

    if (!$uploadsResult) {
        printf("Error: %s\n", $conn->error);
        exit();
    }

    $numUploads = 0;

    if ($uploadsRow = $uploadsResult->fetch_assoc()) {
        $numUploads = $uploadsRow['num_uploads'];
    }

    return $numUploads;
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
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        h2 {
            color: #3498db;
            width: 100%;
            text-align: center;
            margin: 20px 0;
        }

        .user-details,
        .user-stats {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: small;
            margin: 10px;
            padding: 10px;
            box-sizing: border-box;
            width: 300px; /* Adjust the width based on your preference */
            max-height: 400px; /* Adjust the maximum height based on your preference */
    overflow-y: auto;
        }

        hr {
            margin-top: 15px;
            border: 0;
            border-top: 1px solid #ddd;
        }

        a {
            color: #3498db;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        a:hover {
            background-color: #3498db;
            color: #fff;
        }

        .top-links {
            position: fixed;
            top: 15px;
            right: 5px;
            gap: 10px;
        }

        .top-links a {
            color: #3498db;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .top-links a:hover {
            background-color: #3498db;
            color: #fff;
        }

        .user-stats h2 {
            color: #3498db;
            margin-bottom: 15px;
        }

        .user-stats p {
            margin: 5px 0;
        }

        .user-stats hr {
            margin-top: 15px;
            border: 0;
            border-top: 1px solid #ddd;
        }

        .search-container {
            text-align: right; /* Align to the right */
            margin-top: 20px;
            margin-right: 20px; /* Add some margin to separate from the top and right edges */
        }

        .search-input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 200px;
        }

        .search-button {
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #3498db;
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
        }
        
    </style>
</head>
<body>
    <div class="search-container">
    <form action="" method="GET">
        <input type="text" class="search-input" name="search" placeholder="Search by ID, Username, or Email">
        <input type="submit" class="search-button" value="Search">
    </form>
</div>

    <div class="top-links">
        <a href="admindashboard.php">Admin Action Dashboard</a>
        <a href="logout.php">Logout</a>
        <a href="admseedashboard.php">Top</a>
    </div>
    <h2 style="color: black;">Admin Stats Dashboard</h2>

    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="user-details">
            <p><strong>User ID:</strong> <?php echo $row['user_id']; ?></p>
            <p><strong>Username:</strong> <?php echo $row['username']; ?></p>
            <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
            <?php
            // Check if there are media records for the user
            if (isset($row['num_uploads']) && $row['num_uploads'] > 0) {
                $mediaQuery = "SELECT media_id FROM media WHERE user_id = {$row['user_id']}";
                $mediaResult = $conn->query($mediaQuery);

                if (!$mediaResult) {
                    printf("Error: %s\n", $conn->error);
                    exit();
                }

                echo "<p><strong>Media IDs:</strong> ";
                $mediaIds = array();
                while ($mediaRow = $mediaResult->fetch_assoc()) {
                    $mediaIds[] = $mediaRow['media_id'];
                }
                echo implode(', ', $mediaIds);
                echo "</p>";
            } else {
                echo "<p><strong>Media IDs:</strong> No media available</p>";
            }
            ?>

            <p><strong>Number of Uploads:</strong> <?php echo $row['num_uploads']; ?></p>
            <hr>
        </div>
    <?php endwhile; ?>


    <!-- Display total users and total uploads -->
    <div class="user-stats" style="background-color:darkblue;color:white">
        <?php if ($userStatsRow = $userStatsResult->fetch_assoc()) : ?>
            <h1><center><u>Statistics</u></center></h1>
            <p><strong>Total Users:</strong> <?php echo $userStatsRow['total_users']; ?></p>
            <p><strong>Total Uploads:</strong> <?php echo $userStatsRow['total_uploads']; ?></p>
            <hr>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
