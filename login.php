<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$host = 'localhost';
$dbname = 'flavours_of_south_india';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get form data
$email = $_POST['email']; // Ensure your login form uses 'email' as the name attribute
$password = $_POST['password'];

// Fetch user from database
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Validate credentials
if ($user && password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['user_id'] = $user['id']; // Assuming 'id' is the primary key
    $_SESSION['username'] = $user['first_name'];

    // Redirect to the home page
    header('Location: index.html');
    exit;
} else {
    // Redirect back to login with error
    header('Location: login.html?error=1');
    exit;
}
?>