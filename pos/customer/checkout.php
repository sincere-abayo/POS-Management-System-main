<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: cart.php');
    exit;
}
$customer_id = $_SESSION['customer_id'];
// Fetch customer info
$ret = "SELECT * FROM rpos_customers WHERE customer_id = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('s', $customer_id);
$stmt->execute();
$cust = $stmt->get_result()->fetch_object();
$customer_name = $cust->customer_name;
$customer_phoneno = $cust->customer_phoneno;
$customer_email = $cust->customer_email;
$delivery_address = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $order_type = 'online';
    $items = json_encode($_SESSION['cart']);
    $status = 'pending';
    $delivery_address = $_POST['delivery_address'];
    $ret = "INSERT INTO rpos_orders (customer_id, order_type, items, status, delivery_address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('sssss', $customer_id, $order_type, $items, $status, $delivery_address);
    $stmt->execute();
    if ($stmt) {
        unset($_SESSION['cart']);
        $success = 'Order placed successfully!';
    } else {
        $err = 'Failed to place order. Please try again.';
    }
}
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
                            <h3>Checkout</h3>
                            <a href="cart.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Back to
                                Cart</a>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)) {
                                echo '<div class="alert alert-success">' . $success . '</div>';
                            } ?>
                            <?php if (isset($err)) {
                                echo '<div class="alert alert-danger">' . $err . '</div>';
                            } ?>
                            <?php if (!isset($success)) { ?>
                                <form method="post">
                                    <h5>Delivery Information</h5>
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($customer_name); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo htmlspecialchars($customer_phoneno); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control"
                                            value="<?php echo htmlspecialchars($customer_email); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Delivery Address</label>
                                        <textarea name="delivery_address" class="form-control"
                                            required><?php echo htmlspecialchars($delivery_address); ?></textarea>
                                    </div>
                                    <h5>Order Summary</h5>
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Code</th>
                                                <th>Unit Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0;
                                            foreach ($_SESSION['cart'] as $item) {
                                                $subtotal = $item['prod_price'] * $item['quantity'];
                                                $total += $subtotal; ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['prod_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['prod_code']); ?></td>
                                                    <td>RWF <?php echo htmlspecialchars($item['prod_price']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                    <td>RWF <?php echo htmlspecialchars($subtotal); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-right">Total:</th>
                                                <th>RWF <?php echo htmlspecialchars($total); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <button type="submit" name="place_order" class="btn btn-success btn-block"><i
                                            class="fas fa-check"></i> Place Order</button>
                                </form>
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