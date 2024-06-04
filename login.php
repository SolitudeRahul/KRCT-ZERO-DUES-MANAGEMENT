<?php
// Start the session
//session_start();

// Check if user is already logged in
if (isset($_SESSION["username"])) {
    // If logged in, redirect to welcome page
    header("Location: welcome.php");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Connect to your database (replace with your actual database credentials)
    $servername = "localhost";
    $db_username = "root";
    $db_password = "root";
    $dbname = "krctnodues";

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Prepare SQL statement to retrieve all users
    $sql = "SELECT username, password FROM login1";
    $result = $conn->query($sql);

    // Check if any users exist
    if ($result->num_rows > 0) {
        // Iterate through each user
        while ($row = $result->fetch_assoc()) {
            // Verify username and password
            if ($row['username'] == $username && $row['password'] == $password) {
                // Start a session
                session_start();

                // Set session variables
                $_SESSION["username"] = $username;

                // Redirect to welcome page
                header("Location: welcome.php");
                exit;
            }
        }
    }

    // Close connection
    $conn->close();

    // Display error message if login fails
    echo '<script>alert("Invalid username or password.");</script>';

}
?>
