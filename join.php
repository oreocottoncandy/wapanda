<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "wapanda_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate discount code (e.g., WAPANDA10)
function generateDiscountCode() {
    $prefix = "WAPANDA";
    $randomNumber = rand(1000, 9999);
    return $prefix . $randomNumber; // e.g., WAPANDA5678
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email from form
    $email = $_POST['email'];
    
    // Generate a discount code
    $discountCode = generateDiscountCode();

    // Store email and discount code in the database
    $stmt = $conn->prepare("INSERT INTO subscribers (email, discount_code) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $discountCode);
    
    if ($stmt->execute()) {
        // Prepare the email message
        $to = $email;
        $subject = "Your Wapanda Discount Code!";
        $message = "Welcome to the tribe! Your discount code is: " . $discountCode;
        $headers = "From: no-reply@wapanda.com\r\n";
        $headers .= "Reply-To: no-reply@wapanda.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; // Optional for HTML formatted emails
        
        // Send discount code via email
        if (mail($to, $subject, $message, $headers)) {
            echo "Success! Check your email for the discount code.";
        } else {
            echo "Discount code generated, but failed to send email.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
}

$conn->close();
?>
