<?php
include 'dbmanager.php';  // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $product_id = $_POST['productid'];
    $customer_id = $_POST['customerid'];
    $order_date = $_POST['orderdate'];
    $quantity_order = $_POST['quantityorder'];

    // Validation
    if (empty($product_id) || empty($customer_id) || empty($order_date) || empty($quantity_order)) {
        echo "<script>alert('Error: Please fill all fields'); history.back();</script>";
        exit;
    }

    // Check if the order date is valid
    $current_date = date("Y-m-d");
    if ($order_date < $current_date) {
        echo "<script>alert('Error: Order date cannot be before today'); history.back();</script>";
        exit;
    }

    // Check if the product quantity in stock is sufficient
    $checkStockQuery = "SELECT quantityinstock FROM product WHERE productid = ?";
    $stmt = $conn->prepare($checkStockQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($quantity_in_stock);
    $stmt->fetch();
    $stmt->close();

    if ($quantity_in_stock === null) {
        echo "<script>alert('Error: Product not found'); history.back();</script>";
        exit;
    }

    if ($quantity_in_stock <= 0) {
        echo "<script>alert('Error: Product is out of stock'); history.back();</script>";
        exit;
    }

    if ($quantity_order > $quantity_in_stock) {
        echo "<script>alert('Error: Quantity ordered exceeds stock'); history.back();</script>";
        exit;
    }

    // Insert into the `order` table
    $insertOrderQuery = "INSERT INTO `order` (customerid, orderdate) VALUES (?, ?)";
    $stmt = $conn->prepare($insertOrderQuery);
    $stmt->bind_param("is", $customer_id, $order_date);

    if ($stmt->execute()) {
        // Get the last inserted order ID
        $order_id = $conn->insert_id;

        // Insert into the `orderproduct` table
        $insertOrderProductQuery = "INSERT INTO orderproduct (orderid, productid, quantityorder) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($insertOrderProductQuery);
        $stmt2->bind_param("iii", $order_id, $product_id, $quantity_order);

        if ($stmt2->execute()) {
            // Update the `product` table to reduce the quantity in stock
            $updateProductQuery = "UPDATE product SET quantityinstock = quantityinstock - ? WHERE productid = ?";
            $stmt3 = $conn->prepare($updateProductQuery);
            $stmt3->bind_param("ii", $quantity_order, $product_id);

            if ($stmt3->execute()) {
                echo "<script>alert('Success: Order created successfully'); window.location.href='vieworder.php';</script>";
            } else {
                echo "<script>alert('Error updating product quantity: " . $stmt3->error . "'); history.back();</script>";
            }
        } else {
            echo "<script>alert('Error inserting into orderproduct table: " . $stmt2->error . "'); history.back();</script>";
        }
    } else {
        echo "<script>alert('Error inserting into order table: " . $stmt->error . "'); history.back();</script>";
    }

    // Close all prepared statements and the database connection
    $stmt->close();
    $stmt2->close();
    $stmt3->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); history.back();</script>";
}
?>
