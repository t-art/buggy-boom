<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$cart = new Class_Site_Cart();
if (count($_POST) == 0) {
  echo $cart->Show();
} else {
  $cart->DelSelected();
}
unset($cart);
