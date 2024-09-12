<?php
session_start();
include 'dbmanager.php'; // Include the database connection file

// Check if the user is logged in (if required)
if (!isset($_SESSION['adminusername'])) {
    header("Location: index.php");
    exit();
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $customerId = $_POST['customerid']; // Retrieve customer ID from hidden input field
    $customerName = htmlspecialchars(trim($_POST['customername']));
    $customerContact = htmlspecialchars(trim($_POST['customercontact']));
    $customerAddress = htmlspecialchars(trim($_POST['customeraddress']));
    $customerPostalCode = htmlspecialchars(trim($_POST['customerpostalcode']));
    $customerState = htmlspecialchars(trim($_POST['customerstate']));

    // Validate input data
    $errors = [];

    // Ensure all fields are filled
    if (empty($customerName) || empty($customerContact) || empty($customerAddress) || empty($customerPostalCode) || $customerState == "0") {
        $errors[] = "Please fill in all fields.";
    }

    // Validate phone number pattern
    if (!preg_match('/^01\d{8,9}$/', $customerContact)) {
        $errors[] = "Phone number must start with '01' and be 10 or 11 digits long.";
    }

    // Check if there are any errors
    if (empty($errors)) {
        // Prepare an SQL statement to update customer data
        $sql = "UPDATE customers SET customername = ?, customercontact = ?, customeraddress = ?, customerpostalcode = ?, customerstate = ? WHERE customerid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisi", $customerName, $customerContact, $customerAddress, $customerPostalCode, $customerState, $customerId);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "<script>alert('Customer updated successfully'); window.location.href='viewcustomer.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); history.back();</script>";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        // Print validation errors
        $errorMessages = implode("\\n", $errors);
        echo "<script>alert('$errorMessages'); history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); history.back();</script>";
}
?>
