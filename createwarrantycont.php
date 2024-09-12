<?php
session_start();
include 'dbmanager.php';

// Check if the user is logged in
if (!isset($_SESSION['adminusername'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit();
}

// Fetch form data
$orderid = $_POST['orderid'] ?? null;
$warrantystartdate = $_POST['warrantystartdate'] ?? null;
$warrantyPeriodMonths = $_POST['warrantyperiod'] ?? null;
$warrantydetails = $_POST['warrantydetails'] ?? null;

// Validate required fields
if (!$orderid || !$warrantystartdate || !$warrantyPeriodMonths || !$warrantydetails) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

// Fetch the order date from the database
$orderQuery = $conn->prepare("SELECT orderdate FROM `order` WHERE orderid = ?");
$orderQuery->bind_param("i", $orderid);
$orderQuery->execute();
$orderQuery->bind_result($orderdate);
$orderQuery->fetch();
$orderQuery->close();

// Validate the fetched order date
if (!$orderdate) {
    echo "<script>alert('Order not found.'); window.history.back();</script>";
    exit();
}

// Convert dates to DateTime objects
$startDate = DateTime::createFromFormat('Y-m-d', $warrantystartdate);
$orderDate = DateTime::createFromFormat('Y-m-d', $orderdate);

if (!$startDate) {
    echo "<script>alert('Invalid start date format.'); window.history.back();</script>";
    exit();
}

// Ensure the start date is not before the order date
if ($startDate < $orderDate) {
    echo "<script>alert('Warranty start date cannot be before the order date.'); window.history.back();</script>";
    exit();
}

// Calculate the end date by adding the warranty period (in months)
$startDate->modify("+{$warrantyPeriodMonths} months");
$warrantyenddate = $startDate->format('Y-m-d');

// Prepare and execute the query to insert warranty details into the database
$stmt = $conn->prepare("INSERT INTO warranty (orderid, warrantystartdate, warrantyenddate, warrantydetails, warrantyperiod) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $orderid, $warrantystartdate, $warrantyenddate, $warrantydetails, $warrantyPeriodMonths);

if ($stmt->execute()) {
    echo "<script>alert('Warranty created successfully.'); window.location.href='vieworder.php?success=1';</script>";
} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
