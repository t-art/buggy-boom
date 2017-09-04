<?php

class Class_Reference_ProductImageCommon extends Class_BaseCommon {

  private $_imageCommon;

  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_product_image';
    $this->_imageCommon = new Class_ImageProductCommon();
  }


  public function Delete($id) {
    if (parent::Delete($id)) {
      $this->_imageCommon->Delete($id);
      return true;
    } else {
      return false;
    }
  }


  public function Update($id, $newData) {
    if (parent::Update($id, $newData)) {
      $this->_imageCommon->ClearCache($id);
      return true;
    } else {
      return false;
    }
  }


  public function GetPath($id, $width, $height, $trimmed = false) {
    return $this->_imageCommon->GetPathToThumb($id, $width, $height, $trimmed);
  }

}