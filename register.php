<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$dbname = 'flavours_of_south_india';
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($phone) || empty($address)) {
        die("All fields are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = :email");
    $checkEmail->bindParam(':email', $email);
    $checkEmail->execute();

    if ($checkEmail->rowCount() > 0) {
        die("Email already exists.");
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    try {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, address) VALUES (:first_name, :last_name, :email, :password, :phone, :address)");
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        $stmt->execute();

        // Redirect to login page after successful registration
        header('Location: login.html');
        exit();
    } catch (PDOException $e) {
        die("Error inserting data: " . $e->getMessage());
    }
}
?>