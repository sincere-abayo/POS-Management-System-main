<?php
//Global variables
$customer_id = $_SESSION['customer_id'];

//1. My Orders
$query = "SELECT COUNT(*) FROM `rpos_orders` WHERE customer_id =  '$customer_id' ";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($orders);
$stmt->fetch();
$stmt->close();

//3. Available Products
$query = "SELECT COUNT(*) FROM `rpos_products` ";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($products);
$stmt->fetch();
$stmt->close();

//4.My Payments
$query = "SELECT SUM(p.amount) FROM rpos_payments p JOIN rpos_orders o ON p.order_id = o.order_id WHERE o.customer_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $customer_id);
$stmt->execute();
$stmt->bind_result($sales);
$stmt->fetch();
$stmt->close();
