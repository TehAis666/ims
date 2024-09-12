<?php
session_start();
include 'dbmanager.php'; // Include the database connection file

// Check if the user is logged in
if (!isset($_SESSION['adminusername'])) {
    header("Location: index.php");
    exit();
}

// Check if the product ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $productID = intval($_GET['id']);

    // Prepare an SQL statement to prevent SQL injection
    $sql = "DELETE FROM product WHERE productid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Optionally delete the product image from the server
        $stmt->close();
        $conn->close();
        echo "<script>alert('Product deleted successfully'); window.location.href='viewprod.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); history.back();</script>";
}
?>
