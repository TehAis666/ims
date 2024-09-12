<?php
session_start();
include 'dbmanager.php';

// Check if the user is logged in
if (!isset($_SESSION['adminusername'])) {
    // Redirect to login page if not logged in
    header("Location: stafflogin.php");
    exit();
}

// Fetch user information from the database safely
$adminusername = $_SESSION['adminusername'];
$sql = "SELECT * FROM admins WHERE adminusername = ?";
$stmt = $conn->prepare($sql); // Use prepared statements for security
$stmt->bind_param("s", $adminusername);
$stmt->execute();
$result = $stmt->get_result();
$adminInfo = $result->fetch_assoc(); // Fetch the admin information

// Fetch Count Product from the database
$productCountQuery = "SELECT COUNT(*) AS product_count FROM product";
$productResult = $conn->query($productCountQuery);

$productCount = 0; // Default to 0 in case the query fails

if ($productResult && $productRow = $productResult->fetch_assoc()) {
    $productCount = $productRow['product_count'];
}

// Fetch Order Count from the database
$orderCountQuery = "SELECT COUNT(*) AS order_count FROM `order`";
$orderResult = $conn->query($orderCountQuery);

$orderCount = 0; // Default to 0 in case the query fails

if ($orderResult && $orderRow = $orderResult->fetch_assoc()) {
    $orderCount = $orderRow['order_count'];
}

// Fetch Count of Out-of-Stock Products from the database
$outOfStockCountQuery = "SELECT COUNT(*) AS out_of_stock_count FROM product WHERE quantityinstock = 0";
$outOfStockResult = $conn->query($outOfStockCountQuery);

$outOfStockCount = 0; // Default to 0 in case the query fails

if ($outOfStockResult && $outOfStockRow = $outOfStockResult->fetch_assoc()) {
    $outOfStockCount = $outOfStockRow['out_of_stock_count'];
}

// Fetch Recent Orders from the database
$recentOrdersQuery = "
    SELECT 
        p.productname, 
        c.customername, 
        c.customerstate AS location, 
        op.quantityorder
    FROM `order` o
    JOIN orderproduct op ON o.orderid = op.orderid
    JOIN product p ON op.productid = p.productid
    JOIN customers c ON o.customerid = c.customerid
    ORDER BY o.orderdate DESC
    LIMIT 5"; // Change the limit as needed

$recentOrdersResult = $conn->query($recentOrdersQuery);

$recentOrders = []; // Array to store recent orders

if ($recentOrdersResult && $recentOrdersResult->num_rows > 0) {
    while ($row = $recentOrdersResult->fetch_assoc()) {
        $recentOrders[] = $row; // Append each row to the recent orders array
    }
}

// Fetch month
$monthlyOrderData = [];

$sql = "SELECT DATE_FORMAT(orderdate, '%Y-%m') AS month_year, COUNT(*) AS order_count
        FROM `order`
        GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
        ORDER BY DATE_FORMAT(orderdate, '%Y-%m')";
$result = $conn->query($sql);

$months = [];
$orderCounts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $months[] = $row['month_year']; // Changed from 'month' to 'month_year'
        $orderCounts[] = $row['order_count'];
    }
}

// Convert PHP arrays to JSON format for JavaScript
$monthsJSON = json_encode($months);
$orderCountsJSON = json_encode($orderCounts);

// Close statement and connection to avoid memory leaks
$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Staff Dashboard</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index-staff.php" class="logo d-flex align-items-center">
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <span class="d-none d-lg-block">IMS</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $adminInfo['adminusername']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>takde</h6>
              <span>takde</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            
            <li>
              <a class="dropdown-item d-flex align-items-center" href="stafflogin.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index_staff.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#category-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-archive"></i><span>Category</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="category-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="createcategory.php">
              <i class="bi bi-circle"></i><span>Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#product-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-hammer"></i><span>Product</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="product-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="createprod.php">
              <i class="bi bi-circle"></i><span>Create</span>
            </a>
          </li>
          <li>
            <a href="viewprod.php">
              <i class="bi bi-circle"></i><span>View</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#customer-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Customer</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="customer-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="createcustomer.php">
              <i class="bi bi-circle"></i><span>Create</span>
            </a>
          </li>

          <li>
            <a href="viewcustomer.php">
              <i class="bi bi-circle"></i><span>View</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#order-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-cart-fill"></i><span>Order</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="order-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="createorder.php">
              <i class="bi bi-circle"></i><span>Create</span>
            </a>
          </li>
          <li>
            <a href="vieworder.php">
              <i class="bi bi-circle"></i><span>View</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#warranty-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Warranty</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="warranty-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="viewwarranty.php">
              <i class="bi bi-circle"></i><span>View</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="staffregister.php">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="stafflogin.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li><!-- End Login Page Nav -->
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">

            <!-- User Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Product Count<span></span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="ps-4">
                      <h6><?php echo $productCount; ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">
                <div class="card-body">
                  <h5 class="card-title">Order<span></span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-receipt"></i>
                    </div>
                    <div class="ps-4">
                      <h6><?php echo $orderCount; ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">Out of Stock<span></span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div class="ps-4">
                      <h6><?php echo $outOfStockCount; ?></h6>
                    </div>
                  </div>
                </div>
              </div>

            </div><!-- End Customers Card -->

            </div><!-- End Reports -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <!-- <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div> -->

                <div class="card-body">
                  <h5 class="card-title">Recent Orders<span></span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Location</th>
                        <th scope="col">Quantity</th>
                      </tr>
                    </thead>
                    <tbody>
                           <?php if (!empty($recentOrders)) : ?>
                       <?php foreach ($recentOrders as $order) : ?>
                           <tr>
                               <td><?php echo htmlspecialchars($order['productname']); ?></td>
                               <td><?php echo htmlspecialchars($order['customername']); ?></td>
                               <td><?php echo htmlspecialchars($order['location']); ?></td>
                               <td><?php echo htmlspecialchars($order['quantityorder']); ?></td>
                           </tr>
                       <?php endforeach; ?>
                   <?php else : ?>
                       <tr>
                           <td colspan="4">No recent orders found.</td>
                       </tr>
                   <?php endif; ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

            <!-- Top Selling -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">
                <div class="card-body pb-0">
                  <h5 class="card-title">Monthly Reports<span></span></h5>

                  <canvas id="barChart" style="max-height: 400px;"></canvas>
                  <script>
        document.addEventListener("DOMContentLoaded", () => {
          const months = <?php echo $monthsJSON; ?>;
          const orderCounts = <?php echo $orderCountsJSON; ?>;

          new Chart(document.querySelector('#barChart'), {
            type: 'bar',
            data: {
              labels: months,  // Use the PHP months data here
              datasets: [{
                label: 'Monthly Orders',
                data: orderCounts,  // Use the PHP order counts data here
                backgroundColor: [
                  'rgba(255, 99, 132, 0.2)',
                  'rgba(255, 159, 64, 0.2)',
                  'rgba(255, 205, 86, 0.2)',
                  'rgba(75, 192, 192, 0.2)',
                  'rgba(54, 162, 235, 0.2)',
                  'rgba(153, 102, 255, 0.2)',
                  'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                  'rgb(255, 99, 132)',
                  'rgb(255, 159, 64)',
                  'rgb(255, 205, 86)',
                  'rgb(75, 192, 192)',
                  'rgb(54, 162, 235)',
                  'rgb(153, 102, 255)',
                  'rgb(201, 203, 207)'
                ],
                borderWidth: 1
              }]
            },
            options: {
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });
        });
      </script>

                </div>

              </div>
            </div><!-- End Top Selling -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <!-- <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div> -->
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      <!-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> -->
    </div>
  </footer><!-- End Footer -->

  <a href="#" clKotek-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>