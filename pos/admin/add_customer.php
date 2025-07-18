<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

//Add Customer
if (isset($_POST['addCustomer'])) {
  //Prevent Posting Blank Values

  if (empty($_POST["customer_phoneno"]) || empty($_POST["customer_name"]) || empty($_POST['customer_email'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $customer_name = $_POST['customer_name'];
    $customer_phoneno = $_POST['customer_phoneno'];
    $customer_email = $_POST['customer_email'];
    $customer_password = sha1(md5('password123')); //Default password
    $customer_id = $_POST['customer_id'];

    //Validate phone number
    if (!preg_match('/^07[0-9]{8}$/', $customer_phoneno)) {
      $err = "Please enter a valid 10-digit phone number starting with 07";
    } else {
      //Insert Captured information to a database table
      $postQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno, customer_email, customer_password) VALUES(?,?,?,?,?)";
      $postStmt = $mysqli->prepare($postQuery);

      //bind paramaters
      $rc = $postStmt->bind_param('sssss', $customer_id, $customer_name, $customer_phoneno, $customer_email, $customer_password);
      $postStmt->execute();

      //declare a varible which will be passed to alert function
      if ($postStmt) {
        // Send welcome email
        require_once __DIR__ . '/../../vendor/autoload.php';
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
          $mail->Subject = 'Welcome to Best Friend Supermarket - Your Account Details';
          $mail->Body = "
          <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;'>
              <h2 style='color: #333; margin: 0;'>BEST FRIEND SUPERMARKET</h2>
              <p style='margin: 5px 0;'>KIGALI, Kimironko</p>
              <p style='margin: 5px 0;'>0785617132</p>
            </div>
            <div style='margin-bottom: 20px;'>
              <h3 style='color: #333;'>Welcome, " . htmlspecialchars($customer_name) . "!</h3>
              <p>Your account has been successfully created. Here are your login credentials:</p>
            </div>
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
              <p><strong>Email:</strong> " . htmlspecialchars($customer_email) . "</p>
              <p><strong>Password:</strong> password123</p>
              <p><strong>Customer ID:</strong> " . htmlspecialchars($customer_id) . "</p>
            </div>
            <div style='margin-bottom: 20px;'>
              <p><strong>Important:</strong></p>
              <ul>
                <li>Please change your password after your first login for security</li>
                <li>You can now place orders and track them online</li>
                <li>For any questions, please contact us at 0785617132</li>
              </ul>
            </div>
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
              <p style='color: #666; font-size: 14px;'>Thank you for choosing Best Friend Supermarket!</p>
            </div>
          </div>";
          $mail->send();
          $success = "Customer Added Successfully! Welcome email sent to " . htmlspecialchars($customer_email);
        } catch (Exception $e) {
          $success = "Customer Added Successfully! (Email could not be sent: " . $mail->ErrorInfo . ")";
        }
        header("refresh:2; url=customes.php");
      } else {
        $err = "Please Try Again Or Try Later";
      }
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
              <h3>Add New Customer</h3>
              <p class="text-muted">Password will be automatically set to: <strong>password123</strong>
              </p>
            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" required>
                    <input type="hidden" name="customer_id" value="<?php echo $cus_id; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Customer Phone Number</label>
                    <input type="tel" name="customer_phoneno" class="form-control" minlength="10" maxlength="10"
                      pattern="07[0-9]{8}" placeholder="e.g. 0781234567" required
                      oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    <small class="form-text text-muted">Must be 10 digits starting with 07</small>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Customer Email</label>
                    <input type="email" name="customer_email" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label>Default Password</label>
                    <input type="text" class="form-control" value="password123" readonly
                      style="background-color: #f8f9fa;">
                    <small class="form-text text-muted">This will be sent to the customer via
                      email</small>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addCustomer" value="Add Customer" class="btn btn-success">
                  </div>
                </div>
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
</body>

</html>