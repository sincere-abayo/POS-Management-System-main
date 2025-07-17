<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['make'])) {

  //Prevent Posting Blank Values
  if (empty($_POST["order_code"]) || empty($_POST["customer_name"]) || empty($_POST['prod_qty'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_id = $_POST['order_id'];
    $order_code = $_POST['order_code'];
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = isset($_POST['customer_phone']) ? $_POST['customer_phone'] : '';
    // If walk-in, create a new customer record
    if ($customer_id === 'walkin') {
      $customer_id = bin2hex(random_bytes(8));
      $timestamp = time();
      $customer_email = 'walkin_' . $timestamp . '@noemail.com';
      $default_password = sha1(md5('walkin123'));
      $addCustomerQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno, customer_email, customer_password) VALUES (?,?,?,?,?)";
      $addCustomerStmt = $mysqli->prepare($addCustomerQuery);
      $addCustomerStmt->bind_param('sssss', $customer_id, $customer_name, $customer_phone, $customer_email, $default_password);
      $addCustomerStmt->execute();
    }
    $prod_id = $_GET['prod_id'];
    $prod_name = $_GET['prod_name'];
    $prod_price = $_GET['prod_price'];
    $prod_qty = $_POST['prod_qty'];
    // Fetch prod_code from the database
    $prod_code = '';
    $ret = "SELECT prod_code FROM rpos_products WHERE prod_id = ? LIMIT 1";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $prod_id);
    $stmt->execute();
    $stmt->bind_result($prod_code);
    $stmt->fetch();
    $stmt->close();
    // Build items JSON array
    $items = json_encode([
      [
        'prod_id' => $prod_id,
        'prod_code' => $prod_code,
        'prod_name' => $prod_name,
        'prod_price' => $prod_price,
        'prod_qty' => $prod_qty
      ]
    ]);
    $order_type = 'in_person'; // Set order type for cashier orders
    //Insert Captured information to a database table
    $postQuery = "INSERT INTO rpos_orders (customer_id, order_type, items) VALUES (?, ?, ?)";
    $postStmt = $mysqli->prepare($postQuery);
    $rc = $postStmt->bind_param('sss', $customer_id, $order_type, $items);
    $postStmt->execute();
    if ($postStmt) {
      $success = "Order Submitted" && header("refresh:1; url=payments.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
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
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
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
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-md-12 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="walkinToggle" checked>
                                            <label class="form-check-label" for="walkinToggle">Unregistered/Walk-in
                                                Customer</label>
                                        </div>
                                    </div>
                                    <!-- Remove the separate search input and make the select searchable -->
                                    <link
                                        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
                                        rel="stylesheet" />
                                    <div class="col-md-4 d-none" id="registeredCustomerBlock">
                                        <label>Customer Name</label>
                                        <select class="form-control" name="customer_name" id="custName">
                                            <option value="">Select Customer Name</option>
                                            <?php
                      //Load All Customers
                      $ret = "SELECT * FROM  rpos_customers ";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->execute();
                      $res = $stmt->get_result();
                      while ($cust = $res->fetch_object()) {
                        ?>
                                            <option value="<?php echo htmlspecialchars($cust->customer_name); ?>"
                                                data-id="<?php echo htmlspecialchars($cust->customer_id); ?>"
                                                data-phone="<?php echo htmlspecialchars($cust->customer_phoneno); ?>"
                                                data-email="<?php echo htmlspecialchars($cust->customer_email); ?>">
                                                <?php echo htmlspecialchars($cust->customer_name . ' | ' . $cust->customer_phoneno . ' | ' . $cust->customer_email); ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="customer_id" id="registeredCustomerID" value="">
                                        <div id="customerInfo" class="mt-2" style="display:none;">
                                            <div><b>Phone:</b> <span id="customerPhone"></span></div>
                                            <div><b>Email:</b> <span id="customerEmail"></span></div>
                                        </div>
                                        <input type="hidden" name="order_id" value="<?php echo $orderid; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-4 d-none" id="walkinCustomerBlock">
                                        <label>Customer Name</label>
                                        <input type="text" name="customer_name" class="form-control"
                                            placeholder="Enter customer name">
                                        <label class="mt-2">Phone Number (optional)</label>
                                        <input type="text" name="customer_phone" class="form-control"
                                            placeholder="Enter phone number" pattern="\d{10}" maxlength="10"
                                            minlength="10" inputmode="numeric" id="walkinPhone">
                                        <input type="hidden" name="customer_id" value="walkin">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Customer ID</label>
                                        <input type="text" name="customer_id" readonly id="customerID"
                                            class="form-control" value="walkin">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Order Code</label>
                                        <input type="text" name="order_code"
                                            value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control"
                                            value="">
                                    </div>
                                </div>
                                <hr>
                                <?php
                // Product lookup logic for both prod_id and prod_code
                $prod_id = isset($_GET['prod_id']) ? $_GET['prod_id'] : null;
                if (!$prod_id && isset($_GET['prod_code'])) {
                  // Lookup product by prod_code
                  $prod_code = $_GET['prod_code'];
                  $ret = "SELECT * FROM rpos_products WHERE prod_code = ? LIMIT 1";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->bind_param('s', $prod_code);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  if ($prod = $res->fetch_object()) {
                    $prod_id = $prod->prod_id;
                    $_GET['prod_id'] = $prod->prod_id;
                    $_GET['prod_name'] = $prod->prod_name;
                    $_GET['prod_price'] = $prod->prod_price;
                  } else {
                    $err = "Product not found for scanned code.";
                  }
                }
                $ret = "SELECT * FROM  rpos_products WHERE prod_id = '$prod_id'";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($prod = $res->fetch_object()) {
                  ?>
                                <div class="form-row align-items-end mb-3">
                                    <div class="col-md-4">
                                        <label>Product Quantity</label>
                                        <input type="number" name="prod_qty" id="prodQty" class="form-control" min="1"
                                            max="<?php echo $prod->quantity; ?>" value="1" required <?php if ($prod->quantity == 0)
                             echo 'disabled'; ?>>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Total Price</label>
                                        <input type="text" id="totalPrice" class="form-control"
                                            value="RWF <?php echo $prod->prod_price; ?>" readonly>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <input type="submit" name="make" value="Make Order"
                                            class="btn btn-success w-100" <?php if ($prod->quantity == 0)
                        echo 'disabled'; ?>>
                                    </div>
                                </div>
                                <?php if ($prod->quantity == 0) { ?>
                                <div class="alert alert-warning text-center" role="alert">
                                    <strong>Notice:</strong> This product is out of stock and cannot be ordered.
                                </div>
                                <?php } ?>
                                <div class="card mb-4">
                                    <div class="row no-gutters">
                                        <div class="col-md-4 text-center p-3">
                                            <img src="../admin/assets/img/products/<?php echo $prod->prod_img ? $prod->prod_img : 'default.jpg'; ?>"
                                                class="img-fluid rounded" style="max-height:180px;">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h4 class="card-title mb-1">
                                                    <?php echo htmlspecialchars($prod->prod_name); ?>
                                                </h4>
                                                <p class="mb-1"><b>Code:</b>
                                                    <?php echo htmlspecialchars($prod->prod_code); ?> |
                                                    <b>Category:</b>
                                                    <?php echo $prod->category ? htmlspecialchars($prod->category) : 'Uncategorized'; ?>
                                                    |
                                                    <b>Status:</b> <span
                                                        class="badge badge-<?php echo $prod->status == 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($prod->status); ?></span>
                                                </p>
                                                <p class="mb-1"><b>Available Stock:</b> <?php echo $prod->quantity; ?>
                                                    <?php if ($prod->quantity <= $prod->min_stocks) { ?><span
                                                        class="badge badge-warning ml-2">Low
                                                        Stock!</span><?php } ?>
                                                </p>
                                                <p class="mb-1"><b>Unit Price:</b> RWF<span
                                                        id="unitPrice"><?php echo $prod->prod_price; ?></span>
                                                </p>
                                                <p class="mb-2"><b>Description:</b>
                                                    <?php echo nl2br(htmlspecialchars($prod->prod_desc)); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var qtyInput = document.getElementById('prodQty');
                                    var unitPrice = parseFloat(document.getElementById('unitPrice')
                                        .textContent);
                                    var totalPrice = document.getElementById('totalPrice');
                                    var maxQty = <?php echo (int) $prod->quantity; ?>;
                                    // Create or get warning message element
                                    var qtyWarning = document.getElementById('qtyWarning');
                                    if (!qtyWarning) {
                                        qtyWarning = document.createElement('div');
                                        qtyWarning.id = 'qtyWarning';
                                        qtyWarning.className = 'text-danger small mt-1';
                                        qtyInput.parentNode.appendChild(qtyWarning);
                                    }
                                    qtyWarning.style.display = 'none';
                                    if (qtyInput && !qtyInput.disabled) {
                                        qtyInput.addEventListener('input', function() {
                                            var qty = parseInt(qtyInput.value) || 1;
                                            if (qty > maxQty) {
                                                qtyInput.value = maxQty;
                                                qty = maxQty;
                                                qtyWarning.textContent =
                                                    'Cannot order more than available stock (' +
                                                    maxQty + ').';
                                                qtyWarning.style.display = 'block';
                                            } else {
                                                qtyWarning.style.display = 'none';
                                            }
                                            var total = unitPrice * qty;
                                            totalPrice.value = 'RWF ' + total.toFixed(2);
                                        });
                                    }
                                });
                                </script>
                                <?php } ?>
                            </form>
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
    <!-- Add select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var walkinToggle = document.getElementById('walkinToggle');
        var regBlock = document.getElementById('registeredCustomerBlock');
        var walkinBlock = document.getElementById('walkinCustomerBlock');
        var custIdInput = document.getElementById('customerID');
        // Set default state: walk-in checked, walk-in block visible, registered block hidden, customerID set
        walkinToggle.checked = true;
        regBlock.classList.add('d-none');
        walkinBlock.classList.remove('d-none');
        custIdInput.value = 'walkin';
        custIdInput.readOnly = true;
        walkinToggle.addEventListener('change', function() {
            if (walkinToggle.checked) {
                regBlock.classList.add('d-none');
                walkinBlock.classList.remove('d-none');
                custIdInput.value = 'walkin';
                custIdInput.readOnly = true;
            } else {
                regBlock.classList.remove('d-none');
                walkinBlock.classList.add('d-none');
                custIdInput.value = '';
                custIdInput.readOnly = false;
            }
        });
        var walkinPhone = document.getElementById('walkinPhone');
        if (walkinPhone) {
            walkinPhone.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
                // Limit to 10 digits
                if (this.value.length > 10) this.value = this.value.slice(0, 10);
            });
        }
        // Registered customer search and info logic
        var customerSearch = document.getElementById('customerSearch');
        var custNameSelect = document.getElementById('custName');
        var customerInfo = document.getElementById('customerInfo');
        var customerPhone = document.getElementById('customerPhone');
        var customerEmail = document.getElementById('customerEmail');
        if (customerSearch && custNameSelect) {
            customerSearch.addEventListener('input', function() {
                var filter = customerSearch.value.toLowerCase();
                for (var i = 0; i < custNameSelect.options.length; i++) {
                    var option = custNameSelect.options[i];
                    if (i === 0) continue; // skip placeholder
                    var text = option.text.toLowerCase();
                    var phone = option.getAttribute('data-phone') ? option.getAttribute('data-phone')
                        .toLowerCase() : '';
                    var email = option.getAttribute('data-email') ? option.getAttribute('data-email')
                        .toLowerCase() : '';
                    option.style.display = (text.includes(filter) || phone.includes(filter) || email
                        .includes(filter)) ? '' : 'none';
                }
            });
            custNameSelect.addEventListener('change', function() {
                var selected = custNameSelect.options[custNameSelect.selectedIndex];
                var id = selected.getAttribute('data-id');
                var name = selected.value; // This is the customer name
                $('#registeredCustomerID').val(id);
                $('#customerID').val(id);
                // Also update the visible customer_name input if present
                if ($('input[name="customer_name"]').length && !$('#walkinCustomerBlock').is(
                        ':visible')) {
                    $('input[name="customer_name"]').val(name);
                }
                var phone = selected.getAttribute('data-phone');
                var email = selected.getAttribute('data-email');
                var customerPhone = document.getElementById('customerPhone');
                var customerEmail = document.getElementById('customerEmail');
                var customerInfo = document.getElementById('customerInfo');
                if (id) {
                    $('#customerID').val(id);
                    $('#customerID').prop('readonly', true);
                    if (customerPhone) customerPhone.textContent = phone || '-';
                    if (customerEmail) customerEmail.textContent = email || '-';
                    if (customerInfo) customerInfo.style.display = 'block';
                }
            });
        }
        // Initialize select2 for customer select
        if (window.jQuery && $('#custName').length) {
            $('#custName').select2({
                width: '100%',
                placeholder: 'Select Customer Name',
                allowClear: true,
                matcher: function(params, data) {
                    // If there are no search terms, return all of the data
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    // Do not display the item if there is no 'text' property
                    if (typeof data.text === 'undefined') {
                        return null;
                    }
                    // Custom matcher for name, phone, or email
                    var term = params.term.toLowerCase();
                    var text = data.text.toLowerCase();
                    return text.includes(term) ? data : null;
                }
            });
            // Handle select2 select event to update customer ID and info
            $('#custName').on('select2:select', function(e) {
                var selected = e.params.data.element;
                var id = selected.getAttribute('data-id');
                var name = selected.value; // This is the customer name
                $('#registeredCustomerID').val(id);
                $('#customerID').val(id);
                // Also update the visible customer_name input if present
                if ($('input[name="customer_name"]').length && !$('#walkinCustomerBlock').is(
                        ':visible')) {
                    $('input[name="customer_name"]').val(name);
                }
                var phone = selected.getAttribute('data-phone');
                var email = selected.getAttribute('data-email');
                var customerPhone = document.getElementById('customerPhone');
                var customerEmail = document.getElementById('customerEmail');
                var customerInfo = document.getElementById('customerInfo');
                if (id) {
                    $('#customerID').val(id);
                    $('#customerID').prop('readonly', true);
                    if (customerPhone) customerPhone.textContent = phone || '-';
                    if (customerEmail) customerEmail.textContent = email || '-';
                    if (customerInfo) customerInfo.style.display = 'block';
                }
            });
            // Handle select2 clear event
            $('#custName').on('select2:clear', function(e) {
                $('#registeredCustomerID').val('');
                $('#customerID').val('');
                if ($('input[name="customer_name"]').length && !$('#walkinCustomerBlock').is(
                        ':visible')) {
                    $('input[name="customer_name"]').val('');
                }
                var customerInfo = document.getElementById('customerInfo');
                if (customerInfo) customerInfo.style.display = 'none';
            });
        }
    });
    </script>
</body>

</html>