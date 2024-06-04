<?php
// Connect to your database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "krctnodues";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message
$message = "";
$showPasswordFields = false;
$username = "";

// Check if username form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"])) {
    $username = $_POST["username"];

    // Fetch the username from the database
    $sql = "SELECT username FROM login1 WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $showPasswordFields = true;
    } else {
        $message = "Username not found.";
    }
}

// Check if password form is submitted and all necessary fields are set
if ($showPasswordFields && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["current_password"])) {
    // Retrieve input values from the form
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];
    $username = $_POST["username"];

    // Fetch the current password from the database for the given username
    $sql = "SELECT password FROM login1 WHERE username = '$username'";
    $result = $conn->query($sql);

    // Check if the query returned any rows
    if ($result->num_rows > 0) {
        // Extract the password hash from the fetched row
        $row = $result->fetch_assoc();
        $hashedCurrentPassword = $row["password"];

        // Verify if the entered current password matches the stored password hash
        if (password_verify($currentPassword, $hashedCurrentPassword)) {
            // Check if the new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Hash the new password for storage
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database for the given username
                $sql = "UPDATE login1 SET password = '$hashedNewPassword' WHERE username = '$username'";
                if ($conn->query($sql) === TRUE) {
                    // Password update successful
                    $message = "Password successfully changed.";
                    // Reset values to clear the form
                    $username = "";
                    $showPasswordFields = false;
                } else {
                    // Error updating password in the database
                    $message = "Error updating password: " . $conn->error;
                }
            } else {
                // New password and confirm password do not match
                $message = "New password and confirm password do not match.";
            }
        } else {
            // Incorrect current password entered
            $message = "Current password is incorrect.";
        }
    } else {
        // Username not found in the database
        $message = "Error: Username not found.";
    }
}

// Close database connection
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <style>
        /* Internal CSS */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            background-image: url('bg-05.jpeg');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .change-password-form {
            width: 350px;
            padding: 50px;
            border: 1px solid #ccc;
            border-radius: 10px;
            text-align: center;
            background-color: #fff;
        }
        .change-password-form h3 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .change-password-form .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .change-password-form input[type="text"],
        .change-password-form input[type="password"],
        .change-password-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
        .toggle-password:hover {
            color: #333;
        }
        .message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form class="change-password-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <h3>Change Password</h3>
        <?php if (!$showPasswordFields): ?>
            <div class="input-group">
                <input type="text" name="username" id="username" placeholder="Username" value="<?php echo $username; ?>" required>
            </div>
            <input type="submit" value="Submit Username">
        <?php else: ?>
            <input type="hidden" name="username" value="<?php echo $username; ?>">
            <div class="input-group">
                <input type="password" name="current_password" id="current_password" placeholder="Current Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('current_password')">👁️</span>
            </div>
            <div class="input-group">
                <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('new_password')">👁️</span>
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">👁️</span>
            </div>
            <input type="submit" value="Change Password">
        <?php endif; ?>
        <?php
        if ($message) {
            echo "<p class='message'>$message</p>";
        }
        ?>
    </form>
    <script>
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var icon = passwordField.nextElementSibling;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.textContent = "🙈"; // Change to closed eye icon
            } else {
                passwordField.type = "password";
                icon.textContent = "👁️"; // Change to open eye icon
            }
        }
    </script>
</body>
</html>
