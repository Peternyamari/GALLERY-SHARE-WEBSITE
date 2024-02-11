<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"])) {
    $media_id = $_POST["media_id"];
    $user_id = $_SESSION['user_id'];
    $comment = $_POST["comment"];

    // Insert the comment into the database
    $conn = new mysqli("localhost:4306", "root", "", "peterson");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $insertCommentQuery = "INSERT INTO comments (media_id, user_id, comment) VALUES ($media_id, $user_id, '$comment')";
    $conn->query($insertCommentQuery);

    $conn->close();
}

// Handle rating submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["rating"])) {
    $media_id = $_POST["media_id"];
    $rating = $_POST["rating"];

    // Update the database with the new rating
    $conn = new mysqli("localhost:4306", "root", "", "peterson");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $updateRatingQuery = "UPDATE media SET ratings = ratings + $rating, votes = votes + 1 WHERE media_id = $media_id";
    $conn->query($updateRatingQuery);

    $conn->close();
}

// Retrieve media data from the database
$conn = new mysqli("localhost:4306", "root", "", "peterson");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve media data for all users with comments
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT nyamari.username AS upload_user, media.media_id, media.media_type, media.contact_info, media.file_path, media.ratings, media.votes, comments.comment, comments.created_at as comment_date, comments.user_id as commentator_id, commentator.username AS commentator_name
            FROM media
            JOIN nyamari ON media.user_id = nyamari.id
            LEFT JOIN comments ON media.media_id = comments.media_id
            LEFT JOIN nyamari AS commentator ON comments.user_id = commentator.id
            WHERE nyamari.username = '$search'
            ORDER BY media.media_id, comments.created_at";
} else {
    $sql = "SELECT nyamari.username AS upload_user, media.media_id, media.media_type, media.contact_info, media.file_path, media.ratings, media.votes, comments.comment, comments.created_at as comment_date, comments.user_id as commentator_id, commentator.username AS commentator_name
            FROM media
            JOIN nyamari ON media.user_id = nyamari.id
            LEFT JOIN comments ON media.media_id = comments.media_id
            LEFT JOIN nyamari AS commentator ON comments.user_id = commentator.id
            ORDER BY media.media_id, comments.created_at";
}

$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Add Font Awesome CDN for star icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        .media-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .media-item {
            width: 400px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);  
            margin: 20px;
            padding: 10px;
            box-sizing: border-box;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
            font-size:small;
        }

        .media-item:hover {
            transform: scale(1.05);
        }

        .media-item img,
        .media-item video {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .download-link {
            color: #3498db;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
        }

        .lightbox img, .lightbox video {
            max-width: 80%;
            max-height: 80%;
        }

        .chat-button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top:20px;
        }

        .chat-button:hover {
            background-color: #2980b9;
        }

        .rating {
            unicode-bidi: bidi-override;
            direction: rtl;
            text-align: left;
            margin-top: 10px;
        }

        .rating input {
            display: none;
        }

        .rating label {
            display: inline-block;
            padding: 5px;
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
        }

        .rating label:hover,
        .rating label:hover ~ label,
        .rating input:checked ~ label {
            color: #f39c12;
        }
        .top-links {
        position: fixed;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
        }

        .top-links a {
        color: #3498db;
        text-decoration: none;
        }
        .top-links {
        position: fixed;
        top: 20px;
        right: 20px;
        display: flex;
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
            color:white;
        }

        /* Styles for the search form */
        .search-form {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        .search-form label {
            margin-right: 10px;
            font-weight: bold;
        }

        .search-form #search {
            padding: 8px;
            border: 1px solid #3498db;
            border-radius: 4px;
            margin-right: 10px;
        }

        .search-form #search:focus {
            outline: none;
            border-color: #2980b9;
        }

        .search-form input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-form input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .comments-container {
            max-height: 70px; /* Set a maximum height for comments */
            overflow-y: auto; /* Enable vertical scroll if comments exceed the maximum height */
        }

        .comment {
          /* Your styles for individual comments */
            font-size: smaller;
        }

        .comments-count {
            font-size: smaller;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<h2>User Dashboard</h2> <br>

<div class="top-links">
    <a href="upload_media.php">Upload</a>
    <a href="logout.php">Logout</a> <br>
    <a href="adminlogin.php">Admin Login</a>
    <a href="dashboard.php">Top</a>
</div>

<!-- Add search form -->
<div class="search-form">
    <form method="get" action="">
        <label for="search">Search by Username:</label>
        <input type="text" id="search" name="search">
        <input type="submit" value="Search">
    </form>
</div>

<div class="media-grid">
    <?php
    // Organize media and comments
    $mediaData = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mediaData[$row["media_id"]]["details"] = $row;
            $mediaData[$row["media_id"]]["comments"][] = $row["comment"] ? $row : null;
        }
    }

    // Display uploaded media and details with rating options and comments
    foreach ($mediaData as $mediaId => $media) {
        $mediaDetails = $media["details"];
        $mediaComments = array_filter($media["comments"], function ($comment) {
            return !is_null($comment);
        });

        echo "<div class='media-item'>";
        echo "<p>Upload User: " . $mediaDetails["upload_user"] . "</p>";
        echo "<p>Media ID: " . $mediaDetails["media_id"] . "</p>";
        echo "<p>Media Type: " . $mediaDetails["media_type"] . "</p>";
        echo "<p>Description: " . $mediaDetails["contact_info"] . "</p>";

        // Display the media content based on the media type
        switch ($mediaDetails["media_type"]) {
            case 'photo':
                // Make the image clickable to open in a modal/lightbox
                echo "<img src='uploads/{$mediaDetails["file_path"]}' alt='Photo' onclick=\"openLightbox('uploads/{$mediaDetails["file_path"]}', 'conversation_{$mediaDetails["media_id"]}')\">";
                // Add download link for the image
                echo "<a class='download-link' href='uploads/{$mediaDetails["file_path"]}' download>Download Image</a>";
                break;
            case 'video':
                // Make the video clickable to open in a modal/lightbox
                echo "<video controls onclick=\"openLightbox('uploads/{$mediaDetails["file_path"]}', 'conversation_{$mediaDetails["media_id"]}')\">
                            <source src='uploads/{$mediaDetails["file_path"]}' type='video/mp4'>
                      </video>";
                // Add download link for the video
                echo "<a class='download-link' href='uploads/{$mediaDetails["file_path"]}' download>Download Video</a>";
                break;
            // Add cases for other media types if needed
        }

        // Display the chat button for the conversation
        echo "<button class='chat-button' onclick='toggleConversation(\"conversation_{$mediaDetails["media_id"]}\")'>Chat</button>";

        // Display the current rating and votes
        echo "<p>Rating: " . ($mediaDetails["votes"] > 0 ? round($mediaDetails["ratings"] / $mediaDetails["votes"], 1) : "N/A") . " stars ({$mediaDetails["votes"]} votes)</p>";

        // Display the comments count
        echo "<p class='comments-count'>Comments: " . count($mediaComments) . "</p>";

        // Display the star rating form
        echo "<form method='post'>";
        echo "<div class='rating'>";
        echo "<input type='hidden' name='media_id' value='{$mediaDetails["media_id"]}'>";

        

        // Adjust the name attribute to include media_id
        for ($i = 5; $i >= 1; $i--) {
            echo "<input type='radio' id='star{$mediaDetails["media_id"]}_$i' name='rating' value='$i'>";
            echo "<label for='star{$mediaDetails["media_id"]}_$i'><i class='fas fa-star'></i></label>";
        }

        echo "</div>";
        echo "<input type='submit' value='Submit Rating' style='margin-top: 10px; padding: 5px 15px; background-color: #4CAF50; color: #fff; border: 1px solid #4CAF50; border-radius: 5px; cursor: pointer;'>";
        echo "</form>";

        // Display comments
        echo "<div class='conversation' id='conversation_{$mediaDetails["media_id"]}'>";
        echo "<strong>Comments:</strong><br>";

        // Display comments container
        echo "<div class='comments-container'>";

        // Display individual comments
        foreach ($mediaComments as $comment) {
            echo "<p class='comment'><strong>{$comment["commentator_name"]}:</strong> {$comment["comment"]} ({$comment["comment_date"]})</p>";
        }

        echo "</div>"; // Close the comments container

        // Comment form
        echo "<form class='comment-form' method='post' style='margin-top: 20px;'>";
        echo "<input type='hidden' name='media_id' value='{$mediaDetails["media_id"]}'>";
        echo "<label for='comment' style='display: block; margin-bottom: 5px;'>Comment:</label>";
        echo "<textarea id='comment' name='comment' placeholder='Enter your comment' style='height: 70px; width: 70%;' required></textarea>";
        echo "<input type='submit' value='Submit Comment' style='margin-top: 10px; padding: 5px 15px; background-color: #4CAF50; color: #fff; border: 1px solid #4CAF50; border-radius: 5px; cursor: pointer;'>";

        echo "</form>";
        echo "</div>";
        echo "</div>";
    }

?>
</div>

<!-- Lightbox for displaying images and videos -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
<img id="lightbox-image" src="" alt="Full Screen">
</div>

<!-- JavaScript for lightbox and conversation toggle -->
<script>
function openLightbox(mediaPath, conversationId) {
    document.getElementById('lightbox-image').src = mediaPath;
    document.getElementById('lightbox').style.display = 'flex';

    // Close all other conversations
    var allConversations = document.querySelectorAll('.conversation');
    allConversations.forEach(function (conversation) {
        conversation.style.display = 'none';
    });

    // Open the selected conversation
    document.getElementById(conversationId).style.display = 'block';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';

    // Close all conversations when the lightbox is closed
    var allConversations = document.querySelectorAll('.conversation');
    allConversations.forEach(function (conversation) {
        conversation.style.display = 'none';
    });
}

function toggleConversation(conversationId) {
    var conversation = document.getElementById(conversationId);

    // Close all other conversations
    var allConversations = document.querySelectorAll('.conversation');
    allConversations.forEach(function (conversation) {
        conversation.style.display = 'none';
    });

    // Toggle the selected conversation
    conversation.style.display = (conversation.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>

