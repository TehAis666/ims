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
    $categoryId = $_POST['categoryid'];

    // Prepare an SQL statement to prevent SQL injection
    $sql = "DELETE FROM category WHERE categoryid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "<script>alert('Selected category deleted successfully'); window.location.href='createcategory.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); history.back();</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); history.back();</script>";
}
?>
