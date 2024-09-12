<?php
include 'dbmanager.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminusername = $_POST['adminusername'];
    $adminpassword = $_POST['adminpassword'];

    // Check username and password
    $query = $conn->prepare("SELECT * FROM admins WHERE adminusername = ?");
    $query->bind_param("s", $adminusername);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify password (direct comparison, without hashing)
        if ($adminpassword === $row['adminpassword']) { // Corrected to use $adminpassword
            session_start();
            $_SESSION['adminusername'] = $row['adminusername'];

            // Redirect to index-staff.php after successful login
            header("Location: index-staff.php");
            exit();
        } else {
            echo "<script>alert('Invalid username or password'); history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid username or password'); history.back();</script>";
        exit();
    }
} else {
    header("Location: stafflogin.php"); // Redirect if accessed directly
    exit();
}
?>
