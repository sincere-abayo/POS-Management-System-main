<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['UpdateProduct'])) {
  $missing_fields = [];
  if (empty($_POST["prod_code"]))
    $missing_fields[] = 'prod_code';
  if (empty($_POST["prod_name"]))
    $missing_fields[] = 'prod_name';
  if (empty($_POST['prod_desc']))
    $missing_fields[] = 'prod_desc';
  if (empty($_POST['prod_price']))
    $missing_fields[] = 'prod_price';
  if (!empty($missing_fields)) {
    $err = "Please fill in all required fields.";
  } else {
    $update = $_GET['update'];
    $prod_code = $_POST['prod_code'];
    $prod_name = $_POST['prod_name'];
    $prod_img = $_FILES['prod_img']['name'];
    move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/products/" . $_FILES["prod_img"]["name"]);
    $prod_desc = $_POST['prod_desc'];
    $prod_price = $_POST['prod_price'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    $min_stocks = $_POST['min_stocks'];
    $quantity = $_POST['quantity'];
    $images = [];
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
      foreach ($_FILES['images']['name'] as $key => $name) {
        if (!empty($name)) {
          $tmp_name = $_FILES['images']['tmp_name'][$key];
          $target_path = "assets/img/products/" . basename($name);
          move_uploaded_file($tmp_name, $target_path);
          $images[] = $name;
        }
      }
    }
    $images_json = !empty($images) ? json_encode($images) : null;
    $qr_code = null; // Will be set after update

    //Insert Captured information to a database table
    $postQuery = "UPDATE rpos_products SET prod_code=?, prod_name=?, prod_img=?, prod_desc=?, prod_price=?, category=?, status=?, min_stocks=?, quantity=?, images=?, qr_code=? WHERE prod_id = ?";
    $postStmt = $mysqli->prepare($postQuery);
    $rc = $postStmt->bind_param('ssssssssssss', $prod_code, $prod_name, $prod_img, $prod_desc, $prod_price, $category, $status, $min_stocks, $quantity, $images_json, $qr_code, $update);
    $postStmt->execute();
    if ($postStmt) {
      // Generate QR code SVG and update DB
      $qr_svg = base64_encode('<svg id="barcode"></svg>');
      $updateQr = $mysqli->prepare("UPDATE rpos_products SET qr_code=? WHERE prod_id=?");
      $updateQr->bind_param('ss', $qr_svg, $update);
      $updateQr->execute();
      $success = "Product Updated" && header("refresh:1; url=products.php");
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
    $update = $_GET['update'];
    $ret = "SELECT * FROM  rpos_products WHERE prod_id = '$update' ";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($prod = $res->fetch_object()) {
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
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($err)) { ?>
                            <div class="alert alert-danger">
                                <?php echo $err; ?>
                            </div>
                            <?php } ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Product Name</label>
                                        <input type="text" value="<?php echo $prod->prod_name; ?>" name="prod_name"
                                            class="form-control<?php if (isset($missing_fields) && in_array('prod_name', $missing_fields))
                           echo ' is-invalid'; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Product Code</label>
                                        <input type="text" name="prod_code" value="<?php echo $prod->prod_code; ?>"
                                            class="form-control<?php if (isset($missing_fields) && in_array('prod_code', $missing_fields))
                           echo ' is-invalid'; ?>" value="">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Category</label>
                                        <?php $cat = $prod->category; ?>
                                        <select name="category" class="form-control">
                                            <option value="">Select Category</option>
                                            <option value="Fruits" <?php if($cat=="Fruits") echo 'selected'; ?>>Fruits
                                            </option>
                                            <option value="Vegetables" <?php if($cat=="Vegetables") echo 'selected'; ?>>
                                                Vegetables</option>
                                            <option value="Dairy" <?php if($cat=="Dairy") echo 'selected'; ?>>Dairy
                                            </option>
                                            <option value="Meat" <?php if($cat=="Meat") echo 'selected'; ?>>Meat
                                            </option>
                                            <option value="Bakery" <?php if($cat=="Bakery") echo 'selected'; ?>>Bakery
                                            </option>
                                            <option value="Beverages" <?php if($cat=="Beverages") echo 'selected'; ?>>
                                                Beverages</option>
                                            <option value="Snacks" <?php if($cat=="Snacks") echo 'selected'; ?>>Snacks
                                            </option>
                                            <option value="Frozen" <?php if($cat=="Frozen") echo 'selected'; ?>>Frozen
                                            </option>
                                            <option value="Household" <?php if($cat=="Household") echo 'selected'; ?>>
                                                Household</option>
                                            <option value="Personal Care"
                                                <?php if($cat=="Personal Care") echo 'selected'; ?>>Personal Care
                                            </option>
                                            <option value="Other" <?php if($cat=="Other") echo 'selected'; ?>>Other
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" <?php if ($prod->status == 'active')
                          echo 'selected'; ?>>Active</option>
                                            <option value="inactive" <?php if ($prod->status == 'inactive')
                          echo 'selected'; ?>>Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Minimum Stocks</label>
                                        <input type="number" name="min_stocks" class="form-control"
                                            value="<?php echo $prod->min_stocks; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Quantity</label>
                                        <input type="number" name="quantity" class="form-control"
                                            value="<?php echo $prod->quantity; ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label>Additional Images</label>
                                        <input type="file" name="images[]" class="form-control" multiple>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Product Image</label>
                                        <input type="file" name="prod_img" class="btn btn-outline-success form-control"
                                            value="<?php echo $prod_img; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Product Price</label>
                                        <input type="text" name="prod_price" class="form-control<?php if (isset($missing_fields) && in_array('prod_price', $missing_fields))
                        echo ' is-invalid'; ?>" value="<?php echo $prod->prod_price; ?>">
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label>Product Description</label>
                                        <textarea rows="5" name="prod_desc" class="form-control<?php if (isset($missing_fields) && in_array('prod_desc', $missing_fields))
                        echo ' is-invalid'; ?>" value=""><?php echo $prod->prod_desc; ?></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="submit" name="UpdateProduct" value="Update Product"
                                            class="btn btn-success" value="">
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
    }
    ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
  require_once('partials/_scripts.php');
  ?>
    <style>
    .is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, .25);
    }
    </style>
</body>

</html>