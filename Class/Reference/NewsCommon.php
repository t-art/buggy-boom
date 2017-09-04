<?php

class Class_Reference_NewsCommon extends Class_BaseCommon {


  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_news';
    $this->_objectName = 'news';
  }


  public function LoadData($id) {
    $return = false;
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT t.*, DATE_FORMAT(t.date, '%d.%m.%Y') datef, rua.url
              FROM {$this->_tableName} t
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = '{$this->_objectName}' AND t.id = rua.item_id
              WHERE t.id = '{$id}'
              LIMIT 1";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        $return = $this->_db->Row;
      }
    }
    return $return;
  }


}