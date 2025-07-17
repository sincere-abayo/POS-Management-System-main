<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
    <?php require_once('partials/_sidebar.php'); ?>
    <div class="main-content">
        <?php require_once('partials/_topnav.php'); ?>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3>Your Cart</h3>
                            <!-- <a href="orders.php" class="btn btn-outline-primary"
                                onclick="window.location.href='orders.php'; return false;">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a> -->

                        </div>

                        <div class="card-body">
                            <?php
                            if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
                                echo '<div class="alert alert-info">Your cart is empty.</div>';
                            } else {
                                // Handle update/remove
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    if (isset($_POST['update_cart'])) {
                                        $error = '';
                                        foreach ($_POST['quantities'] as $idx => $qty) {
                                            $prod_id = $_SESSION['cart'][$idx]['prod_id'];
                                            $stock_stmt = $mysqli->prepare("SELECT quantity FROM rpos_products WHERE prod_id = ?");
                                            $stock_stmt->bind_param('s', $prod_id);
                                            $stock_stmt->execute();
                                            $stock_stmt->bind_result($available_stock);
                                            $stock_stmt->fetch();
                                            $stock_stmt->close();
                                            if ($qty > $available_stock) {
                                                $error = 'You cannot set a quantity greater than available stock for one or more products.';
                                                $_SESSION['cart'][$idx]['quantity'] = $available_stock > 0 ? $available_stock : 1;
                                            } else {
                                                $_SESSION['cart'][$idx]['quantity'] = max(1, intval($qty));
                                            }
                                        }
                                    }
                                    if (isset($_POST['remove_item'])) {
                                        $idx = $_POST['remove_item'];
                                        array_splice($_SESSION['cart'], $idx, 1);
                                    }
                                }
                                ?>
                            <?php if (!empty($error)) { ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                            <?php } ?>
                            <form method="post" id="cartForm">
                                <table class="table table-bordered align-items-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Code</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $total = 0;
                                            foreach ($_SESSION['cart'] as $idx => $item) {
                                                $prod_id = $item['prod_id'];
                                                $stock_stmt = $mysqli->prepare("SELECT quantity FROM rpos_products WHERE prod_id = ?");
                                                $stock_stmt->bind_param('s', $prod_id);
                                                $stock_stmt->execute();
                                                $stock_stmt->bind_result($available_stock);
                                                $stock_stmt->fetch();
                                                $stock_stmt->close();
                                                $subtotal = $item['prod_price'] * $item['quantity'];
                                                $total += $subtotal;
                                                $out_of_stock = ($available_stock <= 0);
                                                ?>
                                        <tr>
                                            <td>
                                                <img src="../admin/assets/img/products/<?php echo htmlspecialchars($item['prod_img']); ?>"
                                                    style="height:40px;width:40px;object-fit:cover;"
                                                    class="mr-2 rounded">
                                                <?php echo htmlspecialchars($item['prod_name']); ?>
                                                <?php if ($out_of_stock) { ?>
                                                <span class="badge badge-danger ml-2">Out of Stock</span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['prod_code']); ?></td>
                                            <td>RWF <?php echo htmlspecialchars($item['prod_price']); ?></td>
                                            <td><input type="number" name="quantities[<?php echo $idx; ?>]"
                                                    value="<?php echo $item['quantity']; ?>" min="1"
                                                    max="<?php echo $available_stock; ?>"
                                                    class="form-control cart-qty-input" style="width:80px;" <?php if ($out_of_stock)
                                                                echo 'disabled'; ?>
                                                    data-max="<?php echo $available_stock; ?>">
                                            </td>
                                            <td>RWF <?php echo htmlspecialchars($subtotal); ?></td>
                                            <td>
                                                <button type="submit" name="remove_item" value="<?php echo $idx; ?>"
                                                    class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Total:</th>
                                            <th colspan="2">RWF <?php echo htmlspecialchars($total); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" name="update_cart" class="btn btn-info"><i
                                            class="fas fa-sync"></i> Update Cart</button>
                                    <a href="checkout.php" class="btn btn-success"><i class="fas fa-credit-card"></i>
                                        Proceed to Checkout</a>
                                </div>
                            </form>
                            <script>
                            // Prevent entering quantity greater than available stock
                            document.querySelectorAll('.cart-qty-input').forEach(function(input) {
                                input.addEventListener('input', function() {
                                    var max = parseInt(this.getAttribute('data-max'));
                                    if (parseInt(this.value) > max) {
                                        this.value = max;
                                        alert(
                                            'You cannot set a quantity greater than available stock.'
                                        );
                                    }
                                });
                            });
                            </script>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once('partials/_footer.php'); ?>
        <?php require_once('partials/_scripts.php'); ?>
</body>

</html>