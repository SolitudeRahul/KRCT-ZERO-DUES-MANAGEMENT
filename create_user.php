<?php
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = strtoupper(trim($_POST['name']));

    // Validate input
    if (!empty($username) && !empty($password) && !empty($name)) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM login1 WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Username already exists
            $status = "Username '$username' is already taken. Please choose a different one.";
        } else {
            // SQL query to insert new user into the database
            $stmt = $conn->prepare("INSERT INTO login1 (username, password, name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $name);

            if ($stmt->execute()) {
                $status = "User created successfully!";
            } else {
                $status = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $status = "All fields are required.";
    }
    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 450px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .status {
            margin-top: 10px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create User</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="text" name="name" placeholder="Enter Name" required>
            <div class="status"><?php echo isset($status) ? htmlspecialchars($status) : ''; ?></div>
            <input type="submit" value="Create User">
        </form>
        <form action="index.html" method="post">
            <br>
            <input type="submit" value="Login">
        </form>
        <div>
            <h3>Name will be 'FULL NAME AND SPACE initial' respectively Example: "SASIKANTH U"</h3>
        </div>
    </div>
</body>
</html>
