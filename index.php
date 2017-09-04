<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$main = new Class_Site_Index();
echo $main->Run($_GET, $_POST);