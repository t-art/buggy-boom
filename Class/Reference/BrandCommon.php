<?php

class Class_Reference_BrandCommon extends Class_BaseCommon {

  public $_imageBrandCommon;

  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_brand';
    $this->_objectName = 'brand';
    $this->_imageBrandCommon = new Class_ImageBrandCommon();
  }


  public function LoadData($id) {
    $return = false;
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT t.*, rua.url
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


  public function Delete($id) {
    if (parent::Delete($id)) {
      unlink($_SERVER['DOCUMENT_ROOT'] . '/img/' . $this->_objectName . '/' . $id . '.jpg');
      $this->_imageBrandCommon->ClearCache($id);
      return true;
    } else {
      return false;
    }
  }


  public function GetPrimaryImagePath($id, $width, $height, $trimmed = false) {
    $id = (int)$id;
    if (!$id) {
      return false;
    }
    return $this->_imageBrandCommon->GetPathToThumb($id, $width, $height, $trimmed);
  }


  public function GetProducts($id) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT p.id, p.name, p.price, rua.url
              FROM " . Class_Config::DB_PREFIX . "ref_product p
              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
              WHERE p.brand_id = '{$id}'
                AND p.hide = 0
              ORDER BY p.name";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        foreach ($this->_db->Rows as $row) {
          $return[$row['id']] = array(
            'name' => $row['name'],
            'price' => $row['price'],
            'url' => $row['url'],
          );
        }
      }
    }
    if (count($return) == 0) {
      $return = false;
    }
    return $return;
  }


}