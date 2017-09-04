<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$productID = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$quant = isset($_GET['quant']) ? (int)$_GET['quant'] : 1;

$cart = new Class_Site_Cart();
$cart->AddProduct($productID, $quant);
unset($cart);

echo json_encode(array(
  'quant' => $_SESSION['cart']['totals']['quant'],
  'amount' => number_format($_SESSION['cart']['totals']['amount'], 0, ',', ' ')
));