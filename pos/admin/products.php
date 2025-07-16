<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $adn = "DELETE FROM  rpos_products  WHERE  prod_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=products.php");
  } else {
    $err = "Try Again Later";
  }
}
if (isset($_POST['toggleStatus'])) {
  $toggle_id = $_POST['toggle_id'];
  $new_status = $_POST['new_status'];
  $stmt = $mysqli->prepare("UPDATE rpos_products SET status=? WHERE prod_id=?");
  $stmt->bind_param('ss', $new_status, $toggle_id);
  $stmt->execute();
  $stmt->close();
  header("Location: products.php");
  exit();
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
              <a href="add_product.php" class="btn btn-outline-success">
                <i class="fas fa-utensils"></i>
                Add New Product
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Image / QR</th>
                    <th scope="col">Product Code</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM  rpos_products ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($prod = $res->fetch_object()) {
                    ?>
                    <tr>
                      <td>
                        <?php
                        if ($prod->prod_img) {
                          echo "<img src='assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                        } else {
                          echo "<img src='assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                        }
                        ?>
                        <br>
                        <div id="qr_<?php echo $prod->prod_id; ?>" class="qr-div"></div>
                        <button type="button" class="btn btn-sm btn-info mt-1"
                          onclick="downloadQR('<?php echo $prod->prod_id; ?>')">Download QR</button>
                      </td>
                      <td><?php echo $prod->prod_code; ?></td>
                      <td><?php echo $prod->prod_name; ?></td>
                      <td><?php echo $prod->category; ?></td>
                      <td>$ <?php echo $prod->prod_price; ?></td>
                      <td>
                        <span class="badge badge-<?php echo $prod->status == 'active' ? 'success' : 'danger'; ?> p-2"
                          style="font-size: 1em;">
                          <?php echo ucfirst($prod->status); ?>
                        </span>
                        <form method="POST" style="display:inline;">
                          <input type="hidden" name="toggle_id" value="<?php echo $prod->prod_id; ?>">
                          <input type="hidden" name="new_status"
                            value="<?php echo $prod->status == 'active' ? 'inactive' : 'active'; ?>">
                          <button type="submit" name="toggleStatus"
                            class="btn btn-sm btn-outline-<?php echo $prod->status == 'active' ? 'danger' : 'success'; ?> ml-2">
                            <?php echo $prod->status == 'active' ? 'Deactivate' : 'Activate'; ?>
                          </button>
                        </form>
                      </td>
                      <td>
                        <span class="badge badge-info p-2" style="font-size: 1em;">
                          <?php echo $prod->quantity; ?> in stock
                        </span>
                        <?php if ($prod->quantity <= $prod->min_stocks) { ?>
                          <span class="badge badge-warning p-2 ml-1" style="font-size: 1em;">Low Stock!</span>
                        <?php } ?>
                      </td>
                      <td>
                        <a href="products.php?delete=<?php echo $prod->prod_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                          </button>
                        </a>

                        <a href="update_product.php?update=<?php echo $prod->prod_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                            Update
                          </button>
                        </a>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.qr-div').forEach(function (div) {
        var prodId = div.id.replace('qr_', '');
        var prodCode = div.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        new QRCode(div, {
          text: prodCode,
          width: 60,
          height: 60,
          correctLevel: QRCode.CorrectLevel.H
        });
      });
    });
    function downloadQR(prodId) {
      var qrDiv = document.getElementById('qr_' + prodId);
      var img = qrDiv.querySelector('img');
      if (img) {
        var a = document.createElement('a');
        a.href = img.src;
        a.download = 'qr_' + prodId + '.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      }
    }
  </script>
</body>

</html>