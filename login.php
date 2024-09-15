<?php
// Start the session
session_start();

// Initialize variables for error messages and login success
$login_error = '';
$success_message = '';

// Database connection parameters
$host = 'localhost';
$dbname = 'artist_portfolio'; // Your database name
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password for XAMPP (blank)

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $inputUsername, $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user is found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($inputPassword, $hashed_password)) {
            // Start the session and redirect
            $_SESSION['username'] = $username;
            header("Location: home.html");
            exit();
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "User not found!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            width: 300px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-width: 92%;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Display error message if login fails -->
        <?php if (!empty($login_error)): ?>
            <div class="error-message"><?php echo $login_error; ?></div>
        <?php endif; ?>

        <!-- Login form -->
        <form action="login.php" method="POST">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.html">Register here</a></p>
    </div>
</body>
</html>
