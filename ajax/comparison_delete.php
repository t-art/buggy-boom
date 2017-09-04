<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$productID = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

$comparison = new Class_Site_Comparison();
$comparison->DeleteProduct($productID);
unset($comparison);
