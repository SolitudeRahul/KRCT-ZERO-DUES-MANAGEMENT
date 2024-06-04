<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    // If not logged in, redirect to login page
    header("Location: index.html");
    exit;
}

// Retrieve the username from the session
$username = $_SESSION["username"];

// Connect to the database (replace with your actual database credentials)
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

// Initialize variables
$name = "";
$department = "";
$tuition_fee = "";
$hostel_fee = "";
$bus_fee = "";
$miscellaneous_fee = "";
$mess_fee = "";
$exam_fee = "";
$library_fee = "";
$fine = "";
$total_amount = "";

// Prepare SQL statement to retrieve user's name
$sql = "SELECT name FROM login1 WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch user's name
    $row = $result->fetch_assoc();
    $name = $row["name"];

    // Prepare SQL statement to retrieve user details from student table
    $sql = "SELECT * FROM student1 WHERE name = '$name'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user details
        $row = $result->fetch_assoc();
        $department = $row["department"];
        $tuition_fee = $row["tutionfee"];
        $hostel_fee = $row["hostelfee"];
        $bus_fee = $row["busfee"];
        $miscellaneous_fee = $row["miscellaneousfee"];
        $mess_fee = $row["messfee"];
        $exam_fee = $row["examfee"];
        $library_fee = $row["libraryfee"];
        $fine = $row["fine"];
        // Calculate total amount
        $total_amount = $tuition_fee + $hostel_fee + $bus_fee + $miscellaneous_fee + $mess_fee + $exam_fee + $library_fee + $fine;

        // Include the number to words function
        include('number_to_words.php');

        // Convert total amount to words
        $total_amount_in_words = numberToWords($total_amount);
    } else {
        // User details not found in student table
        $error_message = "User details not found in the database.";
        exit($error_message);
    }
} else {
    // Username not found in login table
    $error_message = "Username not found in the database.";
    exit($error_message);
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Balance</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
</head>
<body>
    <div class="container">
        <div class="box">
            <h2>Check Balance</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Tuition Fee</th>
                    <th>Hostel Fee</th>
                    <th>Bus Fee</th>
                    <th>Miscellaneous Fee</th>
                    <th>Mess Fee</th>
                    <th>Exam Fee</th>
                    <th>Library Fee</th>
                    <th>Fine</th>
                </tr>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $department; ?></td>
                    <td><?php echo $tuition_fee; ?></td>
                    <td><?php echo $hostel_fee; ?></td>
                    <td><?php echo $bus_fee; ?></td>
                    <td><?php echo $miscellaneous_fee; ?></td>
                    <td><?php echo $mess_fee; ?></td>
                    <td><?php echo $exam_fee; ?></td>
                    <td><?php echo $library_fee; ?></td>
                    <td><?php echo $fine; ?></td>
                </tr>
                <tr>
                    <th colspan="9">Total</th>
                    <td><?php echo $total_amount; ?></td>
                </tr>
                <tr>
                    <th colspan="1">Total in Words</th>
                    <td colspan="9"><?php echo $total_amount_in_words; ?></td>
                </tr>
            </table>
            <button onclick="window.location.href='generate_zero_dues.php'">Generate Zero Dues</button>
        </div>
    </div>
</body>
</html>
