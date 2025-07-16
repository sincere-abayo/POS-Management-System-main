<?php
include('config/config.php');
include('config/checklogin.php');

// Fetch a single order_id and its related product details
$query = "
    SELECT 
        o.order_id, 
        o.order_code, 
        o.prod_id, 
        p.prod_name, 
        p.prod_price, 
        o.prod_qty 
    FROM 
        rpos_orders o 
    INNER JOIN 
        rpos_products p 
    ON 
        o.prod_id = p.prod_id 
    ORDER BY 
        o.order_id DESC";
$result = mysqli_query($mysqli, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($mysqli));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Order Details</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Code</th>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['prod_name']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['prod_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['prod_qty']); ?></td>
                        <td>
                            <a href="view_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($mysqli); ?>