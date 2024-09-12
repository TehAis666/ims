<?php
session_start();
include 'dbmanager.php';

// Check if the user is logged in
if (!isset($_SESSION['adminusername'])) {
    // Redirect to login page if not logged in
    header("Location: stafflogin.php");
    exit();
}
// Fetch user information from the database
$adminusername = $_SESSION['adminusername'];
$sql = "SELECT * FROM admins WHERE adminusername='$adminusername'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Query to join orders with products and customers with warranties
$withwarrantyquery = "
    SELECT 
        p.productname, 
        c.customername,
        c.customerstate, 
        c.customeraddress, 
        c.customerpostalcode, 
        c.customercontact,
        op.quantityorder, 
        o.orderid,
        o.orderdate,
        w.warrantyid,
        w.warrantystartdate,
        w.warrantyenddate,
        w.warrantyperiod,
        w.warrantydetails
    FROM 
        `order` o
        JOIN orderproduct op ON o.orderid = op.orderid
        JOIN product p ON op.productid = p.productid
        JOIN customers c ON o.customerid = c.customerid
        JOIN warranty w ON o.orderid = w.orderid
";

// Execute the query
$resultwithwarranty = $conn->query($withwarrantyquery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Order</title>
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
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $row['adminusername']; ?></span>
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
        <a class="nav-link collapsed " href="index-staff.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#category-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-archive"></i><span>Category</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="category-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
        <ul id="product-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
          <i class="bi bi-person"></i><span>Warranty</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="customer-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
        <ul id="warranty-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
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
      <h1>Order</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index-staff.php">Home</a></li>
          <li class="breadcrumb-item">Order</li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">

          <div class="row">
        <div class="col-lg-14">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Order List</h5>

              <!-- Default Table -->
              <table class="table table-hover datatable">
    <thead>
        <tr>
            <th scope="col">Product Name</th>
            <th scope="col">Customer Name</th>
            <th scope="col">Location</th>
            <th scope="col">Quantity</th>
            <th scope="col">Order Date</th>
            <th scope="col">Under Coverage</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultwithwarranty->num_rows > 0) : ?>
            <?php while ($row = $resultwithwarranty->fetch_assoc()) : ?>
                <?php
                $productName = htmlspecialchars($row['productname'] ?? '');
                $customerName = htmlspecialchars($row['customername'] ?? '');
                $customerContact = htmlspecialchars($row['customercontact'] ?? '');
                $customerAddress = htmlspecialchars($row['customeraddress'] ?? '');
                $customerPostalCode = htmlspecialchars($row['customerpostalcode'] ?? '');
                $customerState = htmlspecialchars($row['customerstate'] ?? '');
                $quantityOrder = htmlspecialchars($row['quantityorder'] ?? '');
                $orderDate = htmlspecialchars($row['orderdate'] ?? '');
                $orderId = htmlspecialchars($row['orderid'] ?? '');
                $warrantyStartDate = htmlspecialchars($row['warrantystartdate'] ?? '');
                $warrantyEndDate = htmlspecialchars($row['warrantyenddate'] ?? '');
                $warrantyPeriod = htmlspecialchars($row['warrantyperiod'] ?? '');
                $warrantyDetails = htmlspecialchars($row['warrantydetails'] ?? '');

                // Determine warranty status
                $currentDate = new DateTime();
                $endDate = new DateTime($warrantyEndDate);
                $statusClass = $endDate >= $currentDate ? 'bg-success' : 'bg-danger';
                $statusText = $endDate >= $currentDate ? 'Under Coverage' : 'Expired';
                ?>
                <tr data-id="<?php echo $orderId; ?>"
                    data-productname="<?php echo $productName; ?>"
                    data-customername="<?php echo $customerName; ?>"
                    data-customercontact="<?php echo $customerContact; ?>"
                    data-customeraddress="<?php echo $customerAddress; ?>"
                    data-customerpostalcode="<?php echo $customerPostalCode; ?>"
                    data-customerstate="<?php echo $customerState; ?>"
                    data-quantityorder="<?php echo $quantityOrder; ?>"
                    data-orderdate="<?php echo $orderDate; ?>"
                    data-warrantystartdate="<?php echo $warrantyStartDate; ?>"
                    data-warrantyenddate="<?php echo $warrantyEndDate; ?>"
                    data-warrantyperiod="<?php echo $warrantyPeriod; ?>"
                    data-warrantydetails="<?php echo $warrantyDetails; ?>">
                    <td><?php echo $productName; ?></td>
                    <td><?php echo $customerName; ?></td>
                    <td><?php echo $customerState; ?></td>
                    <td><?php echo $quantityOrder; ?></td>
                    <td><?php echo $orderDate; ?></td>
                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                    <td>
                        <button class="btn btn-info rounded-pill btn-sm info-button" data-bs-toggle="modal" data-bs-target="#orderdetails">Info</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="7">No records found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

              <!-- End Default Table Example -->
            </div>
          </div>

          <div class="modal fade" id="orderdetails" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Order Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <h5 class="card-title">Customer Information</h5>
                <div class="row">
                    <div class="col-lg-3 col-md-4 label">Name</div>
                    <div class="col-lg-9 col-md-8 customer-name"></div>
                    <div class="col-lg-3 col-md-4 label">Phone Number</div>
                    <div class="col-lg-9 col-md-8 customer-contact"></div>
                    <div class="col-lg-3 col-md-4 label">Address</div>
                    <div class="col-lg-9 col-md-8 customer-address"></div>
                    <div class="col-lg-3 col-md-4 label">Postal Code</div>
                    <div class="col-lg-9 col-md-8 customer-postalcode"></div>
                    <div class="col-lg-3 col-md-4 label">State</div>
                    <div class="col-lg-9 col-md-8 customer-state"></div>
                </div>
                <h5 class="card-title">Order Information</h5>
                <div class="row">
                    <div class="col-lg-3 col-md-4 label">Product</div>
                    <div class="col-lg-9 col-md-8 product-name"></div>
                    <div class="col-lg-3 col-md-4 label">Quantity</div>
                    <div class="col-lg-9 col-md-8 order-quantityorder"></div>
                    <div class="col-lg-3 col-md-4 label">Order Date</div>
                    <div class="col-lg-9 col-md-8 order-orderdate"></div>
                </div>
                <h5 class="card-title">Warranty Coverage</h5>
                <div class="row">
                    <div class="col-lg-3 col-md-4 label">Warranty Period</div>
                    <div class="col-lg-9 col-md-8 warranty-period"></div>
                    <div class="col-lg-3 col-md-4 label">Warranty Start Date</div>
                    <div class="col-lg-9 col-md-8 warranty-startdate"></div>
                    <div class="col-lg-3 col-md-4 label">Warranty End Date</div>
                    <div class="col-lg-9 col-md-8 warranty-enddate"></div>
                    <div class="col-lg-3 col-md-4 label">Warranty Details</div>
                    <div class="col-lg-9 col-md-8 warranty-details"></div>
                </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div><!-- End Order Details Modal-->
        </section>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      <!-- &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved -->
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      <!-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> -->
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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

  <script>
document.addEventListener('DOMContentLoaded', function () {
    const infoButtons = document.querySelectorAll('.info-button');

    infoButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const modal = document.getElementById('orderdetails');

            modal.querySelector('.customer-name').textContent = row.dataset.customername || 'N/A';
            modal.querySelector('.customer-contact').textContent = row.dataset.customercontact || 'N/A';
            modal.querySelector('.customer-address').textContent = row.dataset.customeraddress || 'N/A';
            modal.querySelector('.customer-postalcode').textContent = row.dataset.customerpostalcode || 'N/A';
            modal.querySelector('.customer-state').textContent = row.dataset.customerstate || 'N/A';
            modal.querySelector('.product-name').textContent = row.dataset.productname || 'N/A';
            modal.querySelector('.order-quantityorder').textContent = row.dataset.quantityorder || 'N/A';
            modal.querySelector('.order-orderdate').textContent = row.dataset.orderdate || 'N/A';
            modal.querySelector('.warranty-startdate').textContent = row.dataset.warrantystartdate || 'N/A';
            modal.querySelector('.warranty-enddate').textContent = row.dataset.warrantyenddate || 'N/A';
            modal.querySelector('.warranty-details').textContent = row.dataset.warrantydetails || 'N/A';
            
            // Format warranty period with "months"
            const warrantyPeriod = row.dataset.warrantyperiod;
            modal.querySelector('.warranty-period').textContent = warrantyPeriod ? `${warrantyPeriod} months` : 'N/A';
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.warranty-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default link behavior
            
            // Get the closest row and retrieve the order ID from the data-id attribute
            const row = this.closest('tr');
            const orderId = row ? row.dataset.id : null;
            console.log('Order ID:', orderId);  // Debugging line
            
            if (orderId) {
                // Redirect to createwarranty.php with the order ID as a query parameter
                window.location.href = `createwarranty.php?orderid=${orderId}`;
            } else {
                alert('Order ID not found.');
            }
        });
    });
});
</script>

</body>

</html>