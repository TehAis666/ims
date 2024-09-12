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
    $productID = intval($_POST['productid']); // Make sure product ID is an integer
    $productName = htmlspecialchars(trim($_POST['productname']));
    $productCategory = htmlspecialchars(trim($_POST['productcategory']));
    $quantityInStock = htmlspecialchars(trim($_POST['quantityinstock']));
    
    // Retrieve the existing product information
    $sql = "SELECT productimage FROM product WHERE productid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $currentImage = $product['productimage'];
    $stmt->close();

    // Validate input data
    $errors = [];

    // Ensure all fields are filled
    if (empty($productName) || $productCategory == "" || empty($quantityInStock)) {
        $errors[] = "Please fill in all fields.";
    }

    // Validate quantity in stock
    if ($quantityInStock < 0) {
        $errors[] = "Quantity in stock cannot be negative.";
    }

    // Handle file upload
    $fileName = $currentImage; // Default to current image if no new image is uploaded
    if (isset($_FILES['productimage']) && $_FILES['productimage']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['productimage']['tmp_name'];
        $fileName = $_FILES['productimage']['name'];
        $fileSize = $_FILES['productimage']['size'];
        $fileType = $_FILES['productimage']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Set allowed file extensions and file size limit (e.g., 2MB)
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($fileExtension, $allowedExts)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }

        if ($fileSize > $maxFileSize) {
            $errors[] = "File size exceeds the maximum limit of 2MB.";
        }

        // Move file to a permanent directory
        $uploadDir = 'imageproduct/';
        $destPath = $uploadDir . $fileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            $errors[] = "Error moving the uploaded file.";
        }
    } elseif ($_FILES['productimage']['error'] == UPLOAD_ERR_NO_FILE) {
        // No new file uploaded, keep the current image
        $fileName = $currentImage;
    } else {
        $errors[] = "Error uploading file.";
    }

    // Check if there are any errors
    if (empty($errors)) {
        // Prepare an SQL statement to prevent SQL injection
        $sql = "UPDATE product SET productname = ?, categoryid = ?, quantityinstock = ?, productimage = ? WHERE productid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siisi", $productName, $productCategory, $quantityInStock, $fileName, $productID);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully'); window.location.href='viewprod.php';</script>";
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
