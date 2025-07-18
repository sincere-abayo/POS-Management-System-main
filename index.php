<?php
session_start();
include('pos/customer/config/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="BRO - Modern Retail Operations Management System">
    <meta name="author" content="Alaine">
    <title>BRO | Welcome</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="pos/admin/assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="pos/admin/assets/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="pos/admin/assets/img/icons/favicon-16x16.png">
    <link rel="manifest" href="pos/admin/assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="pos/admin/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Icons -->
    <link href="pos/customer/assets/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="pos/customer/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Argon CSS -->
    <link type="text/css" href="pos/customer/assets/css/argon.css?v=1.0.0" rel="stylesheet">
    <style>
        .landing-header {
            background: none !important;
            color: #222;
            padding: 30px 0 30px 0;
            text-align: center;
        }
        .landing-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .landing-header p {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 30px;
        }
        .landing-header .btn {
            margin: 0 10px 10px 0;
        }
        .product-card {
            transition: box-shadow 0.2s;
        }
        .product-card:hover {
            box-shadow: 0 0 0.5rem #5e72e4;
        }
        .out-of-stock {
            opacity: 0.7;
        }
        /* Ensure carousel images fit and no extra background */
        .carousel {
            background: none !important;
        }
        .carousel-inner {
            background: none !important;
        }
    </style>
</head>
<body>
    <header class="landing-header p-0" style="background:none;">
        <h1>Welcome to BRO</h1>
        <p>Your modern Based Retail Operations Management System</p>
        <a href="pos/admin/" class="btn btn-light btn-lg"><i class="fas fa-user-shield"></i> Admin Login</a>
        <a href="pos/cashier/" class="btn btn-light btn-lg"><i class="fas fa-cash-register"></i> Cashier Login</a>
        <a href="pos/customer/" class="btn btn-light btn-lg"><i class="fas fa-user"></i> Customer Login</a>
    </header>
    <div class="container mt-5 mb-5">
        <h2 class="mb-4 text-center">Featured Products</h2>
        <div class="row">
            <?php
            $ret = "SELECT * FROM rpos_products WHERE status = 'active' ORDER BY created_at DESC LIMIT 12";
            $stmt = $mysqli->prepare($ret);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($prod = $res->fetch_object()) {
                $out_of_stock = ($prod->quantity <= 0);
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 product-card <?php if ($out_of_stock) echo 'out-of-stock'; ?>">
                        <img src="pos/admin/assets/img/products/<?php echo htmlspecialchars($prod->prod_img); ?>" class="card-img-top" style="height:200px;object-fit:cover;" alt="<?php echo htmlspecialchars($prod->prod_name); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($prod->prod_name); ?></h5>
                            <p class="card-text">Code: <?php echo htmlspecialchars($prod->prod_code); ?></p>
                            <p class="card-text font-weight-bold">RWF <?php echo htmlspecialchars($prod->prod_price); ?></p>
                            <?php if ($out_of_stock) { ?>
                                <span class="badge badge-danger">Out of Stock</span>
                            <?php } else { ?>
                                <span class="badge badge-success">In Stock</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-4">
            <a href="pos/customer/" class="btn btn-primary btn-lg">Shop Now <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <section class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="mb-3">About Us</h2>
                <p class="lead">BRO (Based Retail Operations Management System) is a modern, user-friendly platform designed to streamline and optimize retail business operations. Our system empowers supermarkets, shops, and retail businesses to manage products, sales, staff, and customers efficiently—all in one place.</p>
            </div>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 text-center">
                <h2 class="mb-3">What We Do</h2>
                <ul class="list-unstyled lead">
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Inventory & Product Management</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Sales & Order Processing</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Customer Relationship Management</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Staff & Cashier Management</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Real-time Reporting & Analytics</li>
                    <li><i class="fas fa-check-circle text-success mr-2"></i>Secure, Role-based Access for Admins, Cashiers, and Customers</li>
                </ul>
                <p class="mt-3">Our mission is to help your retail business grow by providing reliable, easy-to-use, and scalable management tools. Whether you run a small shop or a large supermarket, BRO adapts to your needs and helps you deliver the best service to your customers.</p>
            </div>
        </div>
    </section>
    <?php include('pos/customer/partials/_footer.php'); ?>
    <?php include('pos/customer/partials/_scripts.php'); ?>
</body>
</html>