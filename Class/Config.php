<?php

final class Class_Config {

  const DB_HOST = 'localhost';
  const DB_USER = 'root';
  const DB_PASS = '';
  const DB_NAME = 'svetinstrument';
  const DB_PREFIX = 'teksvet_';


}

session_start();

if (!isset($_SESSION['dealer_id'])) {
  $_SESSION['dealer_id'] = 0;
}
if (!isset($_SESSION['dealer_discount'])) {
  $_SESSION['dealer_discount'] = 0;
}

/**
 * Процедура для автоматической загрузки файлов с описанием искомого класса
 * @param string $className название класса
 */
function __autoload($className) {
  $file = str_replace('\\', '/', str_replace('_', '/', ltrim($className, '\\')));

  // Если файл не найден, пытаемся найти его в ThirdParty
  if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/{$file}.php")) {
    $file = 'Class/ThirdParty/' . $file;
  }

  if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/{$file}.php")) {
    include_once $_SERVER['DOCUMENT_ROOT'] . "/{$file}.php";
  }
}