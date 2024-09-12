<?php
session_start();
include 'dbmanager.php'; // Include the database connection file

// Check if the user is logged in (if required)
if (!isset($_SESSION['adminusername'])) {
    header("Location: stafflogin.php");
    exit();
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $categoryName = htmlspecialchars(trim($_POST['categoryname']));

    // Validate input data
    if (empty($categoryName)) {
        echo "<script>alert('Please enter a category name.'); history.back();</script>";
        exit();
    }

    // Check if category already exists
    $query = $conn->prepare("SELECT * FROM category WHERE categoryname = ?");
    $query->bind_param("s", $categoryName);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Category already exists. Please choose another name.'); history.back();</script>";
        exit();
    }

    // Insert new category into the database
    $stmt = $conn->prepare("INSERT INTO category (categoryname) VALUES (?)");
    $stmt->bind_param("s", $categoryName);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "<script>alert('New category created successfully'); window.location.href='createcategory.php';</script>";
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
