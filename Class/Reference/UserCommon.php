<?php

class Class_Reference_UserCommon extends Class_BaseCommon {


  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_user';
    $this->_objectName = 'user';
  }


}