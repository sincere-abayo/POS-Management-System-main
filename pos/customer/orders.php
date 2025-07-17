<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  $prod_id = $_POST['prod_id'];
  $prod_code = $_POST['prod_code'];
  $prod_name = $_POST['prod_name'];
  $prod_price = $_POST['prod_price'];
  $prod_img = $_POST['prod_img'];
  $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
  // Fetch current stock from DB
  $stock_stmt = $mysqli->prepare("SELECT quantity FROM rpos_products WHERE prod_id = ?");
  $stock_stmt->bind_param('s', $prod_id);
  $stock_stmt->execute();
  $stock_stmt->bind_result($available_stock);
  $stock_stmt->fetch();
  $stock_stmt->close();
  if ($quantity > $available_stock) {
    $error = 'Cannot add more than available stock!';
  } else if ($available_stock <= 0) {
    $error = 'Product is out of stock!';
  } else {
    if (!isset($_SESSION['cart']))
      $_SESSION['cart'] = [];
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
      if ($item['prod_id'] === $prod_id) {
        if ($item['quantity'] + $quantity > $available_stock) {
          $error = 'Cannot add more than available stock!';
          $found = true;
          break;
        }
        $item['quantity'] += $quantity;
        $found = true;
        break;
      }
    }
    if (!$found && empty($error)) {
      $_SESSION['cart'][] = [
        'prod_id' => $prod_id,
        'prod_code' => $prod_code,
        'prod_name' => $prod_name,
        'prod_price' => $prod_price,
        'prod_img' => $prod_img,
        'quantity' => $quantity
      ];
    }
    if (empty($error)) {
      $success = 'Added to cart!';
    }
  }
}
require_once('partials/_head.php');
?>

<body>
    <?php require_once('partials/_sidebar.php'); ?>
    <div class="main-content">
        <?php require_once('partials/_topnav.php'); ?>
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>
        <div class="container-fluid mt--8">
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="get" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Search products..."
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <select name="category" class="form-control mr-2">
                            <option value="">All Categories</option>
                            <?php
              $catres = $mysqli->query("SELECT DISTINCT category FROM rpos_products WHERE category IS NOT NULL AND category != ''");
              while ($cat = $catres->fetch_object()) {
                $selected = (isset($_GET['category']) && $_GET['category'] == $cat->category) ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($cat->category) . "\" $selected>" . htmlspecialchars($cat->category) . "</option>";
              }
              ?>
                        </select>
                        <input type="number" name="min_price" class="form-control mr-2" placeholder="Min Price"
                            value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                        <input type="number" name="max_price" class="form-control mr-2" placeholder="Max Price"
                            value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                        <input type="number" name="min_stock" class="form-control mr-2" placeholder="Min Stock"
                            value="<?php echo isset($_GET['min_stock']) ? htmlspecialchars($_GET['min_stock']) : ''; ?>">
                        <input type="date" name="date_from" class="form-control mr-2"
                            value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                        <input type="date" name="date_to" class="form-control mr-2"
                            value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
                        <button type="submit" class="btn btn-info">Filter</button>
                    </form>
                </div>
                <div class="col-md-4 text-right">
                    <a href="cart.php" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> View Cart
                        <?php if (!empty($_SESSION['cart']))
              echo '(' . array_sum(array_column($_SESSION['cart'], 'quantity')) . ')'; ?></a>
                </div>
            </div>
            <div class="row">
                <?php
        $search = isset($_GET['search']) ? '%' . $mysqli->real_escape_string($_GET['search']) . '%' : null;
        $filters = ["status = 'active'"];
        if ($search)
          $filters[] = "(prod_name LIKE '$search' OR prod_code LIKE '$search')";
        if (!empty($_GET['category']))
          $filters[] = "category = '" . $mysqli->real_escape_string($_GET['category']) . "'";
        if (!empty($_GET['min_price']))
          $filters[] = "CAST(prod_price AS DECIMAL(18,2)) >= " . floatval($_GET['min_price']);
        if (!empty($_GET['max_price']))
          $filters[] = "CAST(prod_price AS DECIMAL(18,2)) <= " . floatval($_GET['max_price']);
        if (!empty($_GET['min_stock']))
          $filters[] = "quantity >= " . intval($_GET['min_stock']);
        if (!empty($_GET['date_from']))
          $filters[] = "DATE(created_at) >= '" . $mysqli->real_escape_string($_GET['date_from']) . "'";
        if (!empty($_GET['date_to']))
          $filters[] = "DATE(created_at) <= '" . $mysqli->real_escape_string($_GET['date_to']) . "'";
        $ret = "SELECT * FROM rpos_products";
        if ($filters)
          $ret .= " WHERE " . implode(' AND ', $filters);
        $ret .= " ORDER BY created_at DESC";
        $stmt = $mysqli->prepare($ret);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($prod = $res->fetch_object()) {
          $out_of_stock = ($prod->quantity <= 0);
          ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="../admin/assets/img/products/<?php echo htmlspecialchars($prod->prod_img); ?>"
                            class="card-img-top" style="height:200px;object-fit:cover;"
                            alt="<?php echo htmlspecialchars($prod->prod_name); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($prod->prod_name); ?></h5>
                            <p class="card-text">Code: <?php echo htmlspecialchars($prod->prod_code); ?></p>
                            <p class="card-text font-weight-bold">RWF <?php echo htmlspecialchars($prod->prod_price); ?>
                            </p>
                            <?php if ($out_of_stock) { ?>
                            <span class="badge badge-danger">Out of Stock</span>
                            <?php } ?>
                            <form method="post" class="form-inline product-form">
                                <input type="hidden" name="prod_id"
                                    value="<?php echo htmlspecialchars($prod->prod_id); ?>">
                                <input type="hidden" name="prod_code"
                                    value="<?php echo htmlspecialchars($prod->prod_code); ?>">
                                <input type="hidden" name="prod_name"
                                    value="<?php echo htmlspecialchars($prod->prod_name); ?>">
                                <input type="hidden" name="prod_price"
                                    value="<?php echo htmlspecialchars($prod->prod_price); ?>">
                                <input type="hidden" name="prod_img"
                                    value="<?php echo htmlspecialchars($prod->prod_img); ?>">
                                <input type="number" name="quantity" value="1" min="1"
                                    max="<?php echo $prod->quantity; ?>" class="form-control mr-2 prod-qty-input"
                                    style="width:80px;" <?php if ($out_of_stock)
                      echo 'disabled'; ?> data-max="<?php echo $prod->quantity; ?>">
                                <div class="invalid-qty-msg text-danger small" style="display:none;">Cannot exceed
                                    available stock.
                                </div>
                                <button type="submit" name="add_to_cart" class="btn btn-success" <?php if ($out_of_stock)
                    echo 'disabled'; ?>><i class="fas fa-cart-plus"></i> Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php require_once('partials/_footer.php'); ?>
    </div>
    <?php require_once('partials/_scripts.php'); ?>
    <script>
    // Prevent entering quantity greater than available stock in product grid
    document.querySelectorAll('.prod-qty-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var max = parseInt(this.getAttribute('data-max'));
            var msg = this.parentElement.querySelector('.invalid-qty-msg');
            if (parseInt(this.value) > max) {
                this.value = max;
                if (msg) msg.style.display = 'block';
            } else {
                if (msg) msg.style.display = 'none';
            }
        });
    });
    </script>
</body>

</html>