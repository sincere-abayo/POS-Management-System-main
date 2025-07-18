<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  $errors = [];
  if (empty($_POST['amount'])) {
    $errors[] = "Amount is required";
  }
  if (empty($_POST['pay_method'])) {
    $errors[] = "Payment method is required";
  }
  $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
  $pay_method = isset($_POST['pay_method']) ? $_POST['pay_method'] : '';
  // Stripe minimum amount check
  if ($pay_method === 'stripe' && $amount < 500) {
    $errors[] = "Stripe payments require a minimum of 500 RWF.";
  }
  if (!empty($errors)) {
    $err = implode('<br>', $errors);
  } else {
    $order_id = $_GET['order_id'];
    $amount = $_POST['amount'];
    $pay_method = $_POST['pay_method'];
    $status = 'paid';
    // Stripe integration
    if ($pay_method === 'stripe') {

      require_once __DIR__ . '/../../vendor/autoload.php';
      $stripeConfig = require __DIR__ . '/config/stripe.php';
      \Stripe\Stripe::setApiKey($stripeConfig['secret_key']);
      // Fetch order and customer info
      $order_info = $mysqli->query("SELECT o.*, c.customer_name, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = $order_id")->fetch_object();
      $items = json_decode($order_info->items, true);
      $line_items = [];
      foreach ($items as $item) {
        $qty = isset($item['prod_qty']) ? $item['prod_qty'] : 1;
        $prod_name = isset($item['prod_name']) ? $item['prod_name'] : 'Product';
        $prod_price = isset($item['prod_price']) ? $item['prod_price'] : 0;
        $line_items[] = [
          'price_data' => [
            'currency' => 'rwf',
            'product_data' => [
              'name' => $prod_name,
            ],
            'unit_amount' => intval($prod_price), // Stripe expects cents
          ],
          'quantity' => intval($qty),
        ];
      }
      $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'customer_email' => $order_info->customer_email,
        'success_url' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/pay_order.php?order_id=$order_id&stripe=success",
        'cancel_url' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/pay_order.php?order_id=$order_id&stripe=cancel",
      ]);
      header('Location: ' . $session->url);
      exit;
    }
    // Insert payment
    $postQuery = "INSERT INTO rpos_payments (order_id, method, amount, status) VALUES (?, ?, ?, ?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isds', $order_id, $pay_method, $amount, $status);
    $postStmt->execute();
    $payment_id = $mysqli->insert_id;
    // Update order status and link payment
    $upQry = "UPDATE rpos_orders SET status = 'pending', payment_id = ? WHERE order_id = ?";
    $upStmt = $mysqli->prepare($upQry);
    $upStmt->bind_param('ii', $payment_id, $order_id);
    $upStmt->execute();
    if ($upStmt && $postStmt) {
      // Fetch order, customer, and items info for email
      $order_info = $mysqli->query("SELECT o.*, c.customer_name, c.customer_email, c.customer_phoneno FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = $order_id")->fetch_object();
      $items = json_decode($order_info->items, true);
      // Build modern HTML receipt for email
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
          <p><strong>Customer:</strong> <?php echo htmlspecialchars($order_info->customer_name); ?></p>
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_info->customer_phoneno); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($order_info->customer_email); ?></p>
          <p><strong>Order Date:</strong> <?php echo date('d/M/Y g:i', strtotime($order_info->created_at)); ?></p>
          <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order_info->delivery_address); ?></p>
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
            $total = 0;
            foreach ($items as $item) {
              $qty = isset($item['prod_qty']) ? $item['prod_qty'] : 1;
              $prod_name = isset($item['prod_name']) ? $item['prod_name'] : '-';
              $prod_code = isset($item['prod_code']) ? $item['prod_code'] : '';
              $prod_price = isset($item['prod_price']) ? $item['prod_price'] : 0;
              $subtotal = $prod_price * $qty;
              $total += $subtotal;
              echo '<tr>';
              echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_name) . '</td>';
              echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_code) . '</td>';
              echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . htmlspecialchars($qty) . '</td>';
              echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($prod_price) . '</td>';
              echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($subtotal) . '</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
          <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
              <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Total</strong>
              </td>
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
      // PHPMailer classes
      require_once __DIR__ . '/../../vendor/autoload.php';
      $mail = new PHPMailer\PHPMailer\PHPMailer(true);
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'infofonepo@gmail.com';
      $mail->Password = 'zaoxwuezfjpglwjb';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
      // Send to all staff
      $staff_res = $mysqli->query("SELECT staff_email FROM rpos_staff");
      while ($staff = $staff_res->fetch_object()) {
        $mail->addAddress($staff->staff_email);
      }
      $mail->isHTML(true);
      $mail->Subject = 'Order Paid - Please View, Pack, and Deliver';
      $mail->Body = $receipt_html . '<br><b>Please view, pack, and deliver this order promptly.</b>';
      try {
        $mail->send();
      } catch (Exception $e) { /* Optionally log or ignore */
      }
      // Send to customer
      $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
      $mail2->isSMTP();
      $mail2->Host = 'smtp.gmail.com';
      $mail2->SMTPAuth = true;
      $mail2->Username = 'infofonepo@gmail.com';
      $mail2->Password = 'zaoxwuezfjpglwjb';
      $mail2->SMTPSecure = 'tls';
      $mail2->Port = 587;
      $mail2->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
      $mail2->addAddress($order_info->customer_email, $order_info->customer_name);
      $mail2->isHTML(true);
      $mail2->Subject = 'Your Receipt for Order #' . $order_id . ' - Best Friend Supermarket';
      $mail2->Body = $receipt_html . '<br><b>Your payment was received. We will pack and deliver your order soon!</b>';
      try {
        $mail2->send();
      } catch (Exception $e) { /* Optionally log or ignore */
      }
      $success = "Paid" && header("refresh:1; url=payments_reports.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
// After form, handle Stripe success/cancel
if (isset($_GET['stripe']) && $_GET['stripe'] === 'success' && isset($_GET['order_id'])) {
  $order_id = intval($_GET['order_id']);
  $order = $mysqli->query("SELECT * FROM rpos_orders WHERE order_id = $order_id")->fetch_object();
  $send_email = false;
  if ($order && !$order->payment_id) {
    // Insert payment record
    $amount = 0;
    $items = json_decode($order->items, true);
    if (is_array($items)) {
      foreach ($items as $item) {
        $qty = isset($item['prod_qty']) ? $item['prod_qty'] : 1;
        $price = isset($item['prod_price']) ? $item['prod_price'] : 0;
        $amount += $qty * $price;
      }
    }
    $pay_method = 'stripe';
    $status = 'paid';
    $postQuery = "INSERT INTO rpos_payments (order_id, method, amount, status) VALUES (?, ?, ?, ?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isds', $order_id, $pay_method, $amount, $status);
    $postStmt->execute();
    $payment_id = $mysqli->insert_id;
    $upQry = "UPDATE rpos_orders SET status = 'pending', payment_id = ? WHERE order_id = ?";
    $upStmt = $mysqli->prepare($upQry);
    $upStmt->bind_param('ii', $payment_id, $order_id);
    $upStmt->execute();
    $send_email = true;
  } elseif ($order && $order->payment_id) {
    $send_email = true;
  }
  if ($send_email) {
    // Send receipt email (reuse existing logic)
    $order_info = $mysqli->query("SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = $order_id")->fetch_object();
    $items = json_decode($order_info->items, true);
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
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order_info->customer_name); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_info->customer_phoneno); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order_info->customer_email); ?></p>
        <p><strong>Order Date:</strong> <?php echo date('d/M/Y g:i', strtotime($order_info->created_at)); ?></p>
        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order_info->delivery_address); ?></p>
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
          $total = 0;
          foreach ($items as $item) {
            $qty = isset($item['prod_qty']) ? $item['prod_qty'] : 1;
            $prod_name = isset($item['prod_name']) ? $item['prod_name'] : '-';
            $prod_code = isset($item['prod_code']) ? $item['prod_code'] : '';
            $prod_price = isset($item['prod_price']) ? $item['prod_price'] : 0;
            $subtotal = $prod_price * $qty;
            $total += $subtotal;
            echo '<tr>';
            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_name) . '</td>';
            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($prod_code) . '</td>';
            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . htmlspecialchars($qty) . '</td>';
            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($prod_price) . '</td>';
            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">RWF ' . htmlspecialchars($subtotal) . '</td>';
            echo '</tr>';
          }
          ?>
        </tbody>
        <tfoot>
          <tr style="background-color: #f8f9fa; font-weight: bold;">
            <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Total</strong>
            </td>
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
    // PHPMailer classes
    require_once __DIR__ . '/../../vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'infofonepo@gmail.com';
    $mail->Password = 'zaoxwuezfjpglwjb';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
    // Send to all staff
    $staff_res = $mysqli->query("SELECT staff_email FROM rpos_staff");
    while ($staff = $staff_res->fetch_object()) {
      $mail->addAddress($staff->staff_email);
    }
    $mail->isHTML(true);
    $mail->Subject = 'Order Paid - Please View, Pack, and Deliver';
    $mail->Body = $receipt_html . '<br><b>Please view, pack, and deliver this order promptly.</b>';
    try {
      $mail->send();
    } catch (Exception $e) { /* Optionally log or ignore */
    }
    // Send to customer
    $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail2->isSMTP();
    $mail2->Host = 'smtp.gmail.com';
    $mail2->SMTPAuth = true;
    $mail2->Username = 'infofonepo@gmail.com';
    $mail2->Password = 'zaoxwuezfjpglwjb';
    $mail2->SMTPSecure = 'tls';
    $mail2->Port = 587;
    $mail2->setFrom('infofonepo@gmail.com', 'Best Friend Supermarket');
    $mail2->addAddress($order_info->customer_email, $order_info->customer_name);
    $mail2->isHTML(true);
    $mail2->Subject = 'Your Receipt for Order #' . $order_id . ' - Best Friend Supermarket';
    $mail2->Body = $receipt_html . '<br><b>Your payment was received. We will pack and deliver your order soon!</b>';
    try {
      $mail2->send();
    } catch (Exception $e) { /* Optionally log or ignore */
    }
  }
  header("Location: payments_reports.php");
  exit;
}
require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php
    require_once('partials/_topnav.php');
    $order_id = $_GET['order_id'];
    $ret = "SELECT * FROM rpos_orders WHERE order_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($order = $res->fetch_object()) {
      $items = json_decode($order->items, true);
      $total = 0;
      if (is_array($items)) {
        foreach ($items as $item) {
          $qty = isset($item['quantity']) ? $item['quantity'] : (isset($item['prod_qty']) ? $item['prod_qty'] : 1);
          $price = isset($item['prod_price']) ? $item['prod_price'] : 0;
          $total += $qty * $price;
        }
      }
      ?>
      <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
        class="header  pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body"></div>
        </div>
      </div>
      <div class="container-fluid mt--8">
        <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3>Pay for Order #<?php echo htmlspecialchars($order_id); ?></h3>
              </div>
              <div class="card-body">
                <!-- Order Details Section -->
                <div class="mb-4 p-3 border rounded bg-light">
                  <h4 class="mb-3">Order Details</h4>
                  <?php
                  // Fetch customer info for this order
                  $ret2 = "SELECT o.*, c.customer_name, c.customer_phoneno, c.customer_email FROM rpos_orders o LEFT JOIN rpos_customers c ON o.customer_id = c.customer_id WHERE o.order_id = ?";
                  $stmt2 = $mysqli->prepare($ret2);
                  $stmt2->bind_param('i', $order_id);
                  $stmt2->execute();
                  $res2 = $stmt2->get_result();
                  if ($order2 = $res2->fetch_object()) {
                    $customer_name = $order2->customer_name ? $order2->customer_name : $order2->customer_id;
                    $customer_phone = $order2->customer_phoneno ? $order2->customer_phoneno : '-';
                    $customer_email = ($order2->customer_email && strpos($order2->customer_email, '@noemail.com') === false) ? $order2->customer_email : '-';
                    $order_date = $order2->created_at;
                    $delivery_address = $order2->delivery_address ? $order2->delivery_address : '-';
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
                      <b>Delivery Address:</b> <?php echo htmlspecialchars($delivery_address); ?><br>
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
                            $prod_qty = isset($prod['prod_qty']) ? $prod['prod_qty'] : (isset($prod['quantity']) ? $prod['quantity'] : 0);
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
                      <input type="text" name="order_id" readonly value="<?php echo htmlspecialchars($order_id); ?>"
                        class="form-control">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Amount (RWF)</label>
                      <input type="text" name="amount" readonly value="<?php echo htmlspecialchars($total); ?>"
                        class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Payment Method</label>
                      <select class="form-control" name="pay_method" id="pay_method_select">
                        <option value="momo">momo</option>
                        <option value="airtel">airtel</option>
                        <option value="stripe">stripe</option>
                      </select>
                    </div>
                    <div class="col-md-6" id="phone_number_group" style="display:none;">
                      <label>Mobile Number</label>
                      <input type="text" name="mobile_number" id="mobile_number_input" class="form-control" minlength="10"
                        maxlength="10" pattern="07[0-9]{8}" placeholder="e.g. 07XXXXXXXX"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                      <small id="phone_example" class="form-text text-muted"></small>
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
        <?php require_once('partials/_footer.php'); ?>
      </div>
    <?php }
    ?>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var payMethod = document.getElementById('pay_method_select');
      var phoneGroup = document.getElementById('phone_number_group');
      var phoneInput = document.getElementById('mobile_number_input');
      var phoneExample = document.getElementById('phone_example');

      function togglePhoneInput() {
        if (payMethod.value === 'momo') {
          phoneGroup.style.display = '';
          phoneInput.required = true;
          phoneInput.placeholder = 'e.g. 0781234567 or 0791234567';
          phoneExample.textContent = 'MoMo: e.g. 0781234567 or 0791234567';
        } else if (payMethod.value === 'airtel') {
          phoneGroup.style.display = '';
          phoneInput.required = true;
          phoneInput.placeholder = 'e.g. 0721234567 or 0731234567';
          phoneExample.textContent = 'Airtel: e.g. 0721234567 or 0731234567';
        } else {
          phoneGroup.style.display = 'none';
          phoneInput.required = false;
          phoneInput.value = '';
          phoneExample.textContent = '';
        }
      }
      payMethod.addEventListener('change', togglePhoneInput);
      togglePhoneInput();
    });
  </script>
</body>

</html>