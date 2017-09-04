<?php

class Class_Reference_BannerCommon extends Class_BaseCommon {


  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_banner';
    $this->_objectName = 'banner';
  }


  public function Delete($id) {
    if (parent::Delete($id)) {
      unlink($_SERVER['DOCUMENT_ROOT'] . '/img/' . $this->_objectName . '/' . $id . '.jpg');
      return true;
    } else {
      return false;
    }
  }

}