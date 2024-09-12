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
    $productName = htmlspecialchars(trim($_POST['productname']));
    $productCategory = htmlspecialchars(trim($_POST['productcategory']));
    $quantityInStock = htmlspecialchars(trim($_POST['quantityinstock']));

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
    $fileName = null;
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
        $errors[] = "No file selected.";
    } else {
        $errors[] = "Error uploading file.";
    }

    // Check if product name already exists
    $sql = "SELECT * FROM product WHERE productname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Product name already exists.";
    }

    // Check if there are any errors
    if (empty($errors)) {
        // Prepare an SQL statement to prevent SQL injection
        $sql = "INSERT INTO product (productname, categoryid, quantityinstock, productimage) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siis", $productName, $productCategory, $quantityInStock, $fileName);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "<script>alert('Product created successfully'); window.location.href='viewprod.php';</script>";
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
