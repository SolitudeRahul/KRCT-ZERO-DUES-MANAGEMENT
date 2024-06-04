<?php
// Start the session
session_start();

// Check if user is not logged in
if (!isset($_SESSION["username"])) {
    // If not logged in, redirect to login page
    header("Location: index.html");
    exit;
}

// Retrieve the user's name from the session or database
$name = ""; // Initialize variable
if (isset($_SESSION["name"])) {
    // If the name is already stored in the session, retrieve it
    $name = $_SESSION["name"];
} else {
    // If name is not in the session, retrieve it from the database
    // Connect to your database (replace with your actual database credentials)
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "krctnodues";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to retrieve user's name
    $username = $_SESSION["username"];
    $sql = "SELECT name FROM login1 WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            $name = $row["name"];
            // Store name in session for future use
            $_SESSION["name"] = $name;
        }
    } else {
        echo "Error: Username not found in database.";
    }

    // Close connection
    $conn->close();
}

// Prevent caching of this page
header("Cache-Control: no-cache, must-revalidate");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        /* Internal CSS */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            background-image: url('bg-05.jpeg');
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 30vh;
        }
        .welcome {
            text-align: center;
        }
        .welcome h2 {
            color: black;
            font-size: 36px;
            margin-bottom: 10px;
        }
        .box {
            width: 300px;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background-color: #fff;
            margin: 0 20px;
            padding: 20px;
        }
        .box h3 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        /* Logout button styling */
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout-btn input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
    </style>
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
</head>
<body>
    <!-- Logout button -->
    <div class="logout-btn">
        <form action="logout.php" method="post">
            <input type="submit" value="Logout">
        </form>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Welcome <?php echo $name; ?></h2>
        </div>
    </div>
    <div class="container">
        <div class="box">
            <h3>KRCT Zero Dues</h3>
            <p>It will generate the nodue form then click button download form</p>
            <button class="button" onclick="window.location.href='generate_zero_dues.php'">Generate</button>
        </div>
        <div class="box">
            <h3>Check Balance</h3>
            <p>It will move to the page that displays balance</p>
            <button class="button"onclick="window.location.href='check_balance.php'">Check</button>
        </div>
    </div>
    <script>
        // Using JavaScript to prevent going back to the previous page (index.html)
        history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>
