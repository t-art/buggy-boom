<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/Config.php';

$search = new Class_Site_Search();
$result = $search->ShowAutocomplete(10);
unset($search);

$result = array_values($result);

echo json_encode($result);