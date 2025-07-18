<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
        ?>
        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Select On Any Product To Make An Order
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col"><b>No</b></th>
                                        <th scope="col"><b>Image</b></th>
                                        <th scope="col"><b>Product Code</b></th>
                                        <th scope="col"><b>Name</b></th>
                                        <th scope="col"><b>Price</b></th>
                                        <th scope="col"><b>Stock Level</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM  rpos_products ";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $i = 1;
                                    while ($prod = $res->fetch_object()) {
                                        $out_of_stock = ($prod->quantity <= 0);
                                        ?>
                                        <tr>
                                            <td class="text-success"><?php echo $i++; ?></td>
                                            <td>
                                                <?php
                                                if ($prod->prod_img) {
                                                    echo "<img src='assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                                                } else {
                                                    echo "<img src='assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $prod->prod_code; ?></td>
                                            <td><?php echo $prod->prod_name; ?></td>
                                            <td>RWF <?php echo $prod->prod_price; ?></td>
                                            <td>
                                                <?php if ($out_of_stock) {
                                                    echo "<span style='color:red;font-weight:bold;'>Out of Stock</span>";
                                                } else {
                                                    echo $prod->quantity;
                                                } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>