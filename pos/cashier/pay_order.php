<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  // Prevent Posting Blank Values
  if (empty($_POST['amount']) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_id = $_GET['order_id'];
    $customer_id = $_GET['customer_id'];
    $amount = $_POST['amount'];
    $pay_method = $_POST['pay_method'];
    $transaction_ref = null;

    // Insert payment
    $postQuery = "INSERT INTO rpos_payments (order_id, method, amount, status, transaction_ref) VALUES (?, ?, ?, 'paid', ?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isds', $order_id, $pay_method, $amount, $transaction_ref);
    $postStmt->execute();
    $payment_id = $mysqli->insert_id;

    // Update order status and link payment
    $upQry = "UPDATE rpos_orders SET status = 'delivered', payment_id = ? WHERE order_id = ?";
    $upStmt = $mysqli->prepare($upQry);
    $upStmt->bind_param('ii', $payment_id, $order_id);
    $upStmt->execute();

    // Send receipt to customer
    require_once __DIR__ . '/../../vendor/autoload.php';
    $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($order = $res->fetch_object()) {
      $items = json_decode($order->items, true);
      $customer_email = $order->customer_email;
      $customer_name = $order->customer_name;
      if ($customer_email && strpos($customer_email, '@noemail.com') === false) {
        $total = 0;
        ob_start();
        ?>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
          <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 style="color: #333; margin: 0;">BEST FRIEND SUPERMARKET</h2>
            <p style="margin: 5px 0;">REMERA, GISEMENTI</p>
            <p style="margin: 5px 0;">0785617132</p>
          </div>
          <div style="margin-bottom: 20px;">
            <h3 style="color: #333; text-align: center;">Receipt for Order #<?php echo $order_id; ?></h3>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order->customer_phoneno); ?></p>
            <p><strong>Date:</strong> <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></p>
          </div>
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
              <tr style="background-color: #f8f9fa;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Item</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Code</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Qty</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Unit Price</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (is_array($items) && count($items) > 0) {
                foreach ($items as $prod) {
                  $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                  $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                  $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                  $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                  $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : 0;
                  $total += $subtotal;
                  echo '<tr>';
                  echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_name) . '</td>';
                  echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_code) . '</td>';
                  echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . htmlspecialchars($prod_qty) . '</td>';
                  echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($prod_price) . '</td>';
                  echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($subtotal) . '</td>';
                  echo '</tr>';
                }
              }
              ?>
            </tbody>
            <tfoot>
              <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Total</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>RWF
                    <?php echo htmlspecialchars($total); ?></strong></td>
              </tr>
            </tfoot>
          </table>
          <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p style="color: #666; font-size: 14px;">Thank you for your purchase!</p>
            <p style="color: #666; font-size: 12px;">Please keep this receipt for your records.</p>
          </div>
        </div>
        <?php
        $receipt_html = ob_get_clean();
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'infofonepo@gmail.com';
          $mail->Password = 'zaoxwuezfjpglwjb';
          $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port = 587;
          $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
          $mail->addAddress($customer_email, $customer_name);
          $mail->isHTML(true);
          $mail->Subject = 'Your Receipt for Order #' . $order_id . ' - Best Friend Supermarket';
          $mail->Body = $receipt_html;
          $mail->send();
        } catch (Exception $e) {
          // Optionally log or handle email error
        }
      }
    }

    if ($upStmt && $postStmt) {
      $success = "Paid" && header("refresh:1; url=receipts.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>
    <?php
    $order_id = $_GET['order_id'];
    $ret = "SELECT * FROM rpos_orders WHERE order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($order = $res->fetch_object()) {
      // Calculate total from items JSON
      $items = json_decode($order->items, true);
      $total = 0;
      if (is_array($items) && count($items) > 0) {
        foreach ($items as $prod) {
          $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
          $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
          if (is_numeric($prod_price) && is_numeric($prod_qty)) {
            $total += $prod_price * $prod_qty;
          }
        }
      }
      ?>
      <!-- Header -->
      <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
        class="header  pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body"></div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <!-- Table -->
        <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3>Pay for Order</h3>
              </div>
              <div class="card-body">
                <!-- Order Details Section -->
                <div class="mb-4 p-3 border rounded bg-light">
                  <h4 class="mb-3">Order Details</h4>
                  <?php
                  // Fetch customer info for this order
                  $ret = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
                  $stmt2 = $mysqli->prepare($ret);
                  $stmt2->bind_param('i', $order_id);
                  $stmt2->execute();
                  $res2 = $stmt2->get_result();
                  if ($order2 = $res2->fetch_object()) {
                    $customer_name = $order2->customer_name ? $order2->customer_name : $order2->customer_id;
                    $customer_phone = $order2->customer_phoneno ? $order2->customer_phoneno : '-';
                    $customer_email = ($order2->customer_email && strpos($order2->customer_email, '@noemail.com') === false) ? $order2->customer_email : '-';
                    $order_date = $order2->created_at;
                    $items2 = json_decode($order2->items, true);
                    $total2 = 0;
                    ?>
                    <div class="mb-2">
                      <b>Customer:</b> <?php echo htmlspecialchars($customer_name); ?><br>
                      <b>Phone:</b> <?php echo htmlspecialchars($customer_phone); ?><br>
                      <?php if ($customer_email !== '-') { ?>
                        <b>Email:</b> <?php echo htmlspecialchars($customer_email); ?><br>
                      <?php } ?>
                      <b>Order Date:</b> <?php echo date('d/M/Y g:i', strtotime($order_date)); ?><br>
                    </div>
                    <table class="table table-bordered bg-white">
                      <thead>
                        <tr>
                          <th>Item</th>
                          <th>Code</th>
                          <th>Quantity</th>
                          <th class="text-center">Unit Price</th>
                          <th class="text-center">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if (is_array($items2) && count($items2) > 0) {
                          foreach ($items2 as $prod) {
                            $prod_name = isset($prod['prod_name']) ? $prod['prod_name'] : '-';
                            $prod_code = isset($prod['prod_code']) ? $prod['prod_code'] : '';
                            $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : 0;
                            $prod_price = isset($prod['prod_price']) ? $prod['prod_price'] : 0;
                            $subtotal = (is_numeric($prod_price) && is_numeric($prod_qty)) ? ($prod_price * $prod_qty) : 0;
                            $total2 += $subtotal;
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($prod_name) . '</td>';
                            echo '<td>' . htmlspecialchars($prod_code) . '</td>';
                            echo '<td class="text-center">' . htmlspecialchars($prod_qty) . '</td>';
                            echo '<td class="text-center">RWF ' . htmlspecialchars($prod_price) . '</td>';
                            echo '<td class="text-center">RWF ' . htmlspecialchars($subtotal) . '</td>';
                            echo '</tr>';
                          }
                        } else {
                          echo '<tr><td colspan="5">No products found in this order.</td></tr>';
                        }
                        ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="4" class="text-right">Total</th>
                          <th class="text-center">RWF <?php echo htmlspecialchars($total2); ?></th>
                        </tr>
                      </tfoot>
                    </table>
                  <?php } ?>
                </div>
                <!-- End Order Details Section -->
                <form method="POST" enctype="multipart/form-data">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Order ID</label>
                      <input type="text" readonly value="<?php echo htmlspecialchars($order_id); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Amount (RWF)</label>
                      <input type="text" name="amount" readonly value="<?php echo htmlspecialchars($total); ?>"
                        class="form-control">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Payment Method</label>
                      <select class="form-control" name="pay_method">
                        <option value="cash" selected>Cash</option>
                        <option value="momo">MTN MoMo</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="stripe">Stripe</option>
                        <option value="bank">Banking system</option>
                        <option value="paypal">Paypal</option>
                        <option value="western">Western Union</option>
                      </select>
                    </div>

                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
        <?php require_once('partials/_footer.php'); ?>
      </div>
    <?php } ?>
  </div>
  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>