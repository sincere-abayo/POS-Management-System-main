<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
//Add Staff
if (isset($_POST['addStaff'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["staff_number"]) || empty($_POST["staff_name"]) || empty($_POST['staff_email'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $staff_number = $_POST['staff_number'];
    $staff_name = $_POST['staff_name'];
    $staff_email = $_POST['staff_email'];
    $staff_password = sha1(md5('password123')); //Default password

    //Validate phone number
    if (!preg_match('/^07[0-9]{8}$/', $staff_number)) {
      $err = "Please enter a valid 10-digit phone number starting with 07";
    } else {
      //Insert Captured information to a database table
      $postQuery = "INSERT INTO rpos_staff (staff_number, staff_name, staff_email, staff_password) VALUES(?,?,?,?)";
      $postStmt = $mysqli->prepare($postQuery);
      //bind paramaters
      $rc = $postStmt->bind_param('ssss', $staff_number, $staff_name, $staff_email, $staff_password);
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
          $mail->addAddress($staff_email, $staff_name);
          $mail->isHTML(true);
          $mail->Subject = 'Welcome to Best Friend Supermarket - Your Staff Account Details';
          $mail->Body = "
          <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px;'>
              <h2 style='color: #333; margin: 0;'>BEST FRIEND SUPERMARKET</h2>
              <p style='margin: 5px 0;'>REMERA, GISEMENTI</p>
              <p style='margin: 5px 0;'>0785617132</p>
            </div>
            <div style='margin-bottom: 20px;'>
              <h3 style='color: #333;'>Welcome, " . htmlspecialchars($staff_name) . "!</h3>
              <p>Your staff account has been successfully created. Here are your login credentials:</p>
            </div>
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
              <p><strong>Phone Number:</strong> " . htmlspecialchars($staff_number) . "</p>
              <p><strong>Email:</strong> " . htmlspecialchars($staff_email) . "</p>
              <p><strong>Password:</strong> password123</p>
            </div>
            <div style='margin-bottom: 20px;'>
              <p><strong>Important:</strong></p>
              <ul>
                <li>Please change your password after your first login for security</li>
                <li>You can now access the POS system and manage orders</li>
                <li>For any questions, please contact the administrator</li>
              </ul>
            </div>
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
              <p style='color: #666; font-size: 14px;'>Welcome to the Best Friend Supermarket team!</p>
            </div>
          </div>";
          $mail->send();
          $success = "Staff Added Successfully! Welcome email sent to " . htmlspecialchars($staff_email);
        } catch (Exception $e) {
          $success = "Staff Added Successfully! (Email could not be sent: " . $mail->ErrorInfo . ")";
        }
        header("refresh:2; url=hrm.php");
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
              <h3>Add New Staff Member</h3>
              <p class="text-muted">Password will be automatically set to: <strong>password123</strong></p>
            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Staff Phone Number</label>
                    <input type="tel" name="staff_number" class="form-control" minlength="10" maxlength="10"
                      pattern="07[0-9]{8}" placeholder="e.g. 0781234567" required
                      oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    <small class="form-text text-muted">Must be 10 digits starting with 07</small>
                  </div>
                  <div class="col-md-6">
                    <label>Staff Name</label>
                    <input type="text" name="staff_name" class="form-control" required>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Staff Email</label>
                    <input type="email" name="staff_email" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label>Default Password</label>
                    <input type="text" class="form-control" value="password123" readonly
                      style="background-color: #f8f9fa;">
                    <small class="form-text text-muted">This will be sent to the staff member via email</small>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addStaff" value="Add Staff" class="btn btn-success">
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