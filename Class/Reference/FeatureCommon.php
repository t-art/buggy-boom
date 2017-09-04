<?php

class Class_Reference_FeatureCommon extends Class_BaseCommon {

  private $_featureValueCommon;

  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_feature';
    $this->_objectName = 'feature';
    $this->_featureValueCommon = new Class_AnonymousCommon('ref_feature_value');
  }


  public function Update($id, $newData = array()) {
    if (parent::Update($id, $newData)) {
      if (isset($newData['type']) && $newData['type'] == 'string') {
        $sql = "DELETE FROM " . $this->_tableName . "_value
                WHERE feature_id = '{$id}'";
        $this->_db->Query($sql);
        $sql = "UPDATE " . Class_Config::DB_PREFIX . "link_product_vs_feature
                SET value_id = 0
                WHERE feature_id = '{$id}'";
        $this->_db->Query($sql);
      }
      return true;
    } else {
      return false;
    }
  }


  public function Delete($id) {
    if (parent::Delete($id)) {
      $sql = "DELETE FROM " . $this->_tableName . "_value
              WHERE feature_id = '{$id}'";
      $this->_db->Query($sql);
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_feature
                WHERE feature_id = '{$id}'";
      $this->_db->Query($sql);
      return true;
    } else {
      return false;
    }
  }


  public function GetValues($id) {
    $id = (int)$id;
    if (!$id) {
      return false;
    }

    $return = $this->_featureValueCommon->Find("feature_id = '{$id}'", 'value', 'id');
    return $return;
  }

}