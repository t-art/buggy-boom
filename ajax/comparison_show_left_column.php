<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$comparison = new Class_Site_Comparison();
echo $comparison->ShowLeftColumn();
unset($comparison);
