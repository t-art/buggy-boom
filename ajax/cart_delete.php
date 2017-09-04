<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$productID = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$sizeID = isset($_GET['size_id']) ? (int)$_GET['size_id'] : 0;

$cart = new Class_Site_Cart();
$cart->DeleteProduct($productID, $sizeID);
unset($cart);

//echo $_SESSION['cart']['totals']['quant'] . ' ' . $_SESSION['cart']['totals']['quantUnit'] . '<br>';
//echo 'на ' . number_format($_SESSION['cart']['totals']['amount'], 0, ',', ' ') . ' р.';

echo '{"quant":"' . $_SESSION['cart']['totals']['quant'] . '","quant_unit":"' . $_SESSION['cart']['totals']['quantUnit'] . '","amount":"' . number_format($_SESSION['cart']['totals']['amount'], 0, ',', ' ') . '"}';
