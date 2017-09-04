<?php

class Class_DB {

  private $_dbLink;

  protected static $_instance;

  public $Row;
  public $Rows;

  private function __construct() {
    $dbHost = Class_Config::DB_HOST;
    $dbUser = Class_Config::DB_USER;
    $dbPass = Class_Config::DB_PASS;
    $dbName = Class_Config::DB_NAME;
    $this->_dbLink = mysql_connect($dbHost, $dbUser, $dbPass);
    mysql_select_db($dbName, $this->_dbLink);
    mysql_query('SET NAMES UTF8', $this->_dbLink);
  }


  private function __clone() {
  }


  public static function getInstance() {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }


  public function Query($sql) {
    return mysql_query($sql, $this->_dbLink);
  }


  public function QueryFetch($sql) {
    $this->Row = array();
    $this->Rows = array();
    $r =  $this->Query($sql);
    if ($r) {
      while ($a = mysql_fetch_assoc($r)) {
        $this->Rows[] = $a;
      }
    }
    if (count($this->Rows) > 0) {
      $this->Row = $this->Rows[0];
      $return = true;
    } else {
      $return = false;
    }
    return $return;
  }


  public function GetLastInsertID() {
    return mysql_insert_id($this->_dbLink);
  }


  public function Escape($str) {
    return mysql_real_escape_string($str);
  }


}

