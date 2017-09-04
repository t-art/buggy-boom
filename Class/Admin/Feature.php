<?php

class Class_Admin_Feature extends Class_BaseCommon {

  protected $_commonObj;
  private $_featureValueCommon;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_FeatureCommon();
    $this->_featureValueCommon = new Class_AnonymousCommon('ref_feature_value');
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $return = $this->_showList();
        break;
      case 'edit':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $return = $this->_edit($id);
        break;
      case 'delete':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $this->_delete($id);
        break;
      case 'save':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $this->_save($id);
        break;
      case 'save_bulk':
        $this->_saveBulk();
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _showList() {

    $return = '';

    $items = array();
    $sql = "SELECT f.id, f.name, f.type, f.in_listing, f.sort
            FROM " . $this->_commonObj->_tableName . " f
            ORDER BY f.sort
           ";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $items = $this->_db->Rows;
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'list', array('items' => $items));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _edit($id) {
    $return = '';
    $data = $this->_commonObj->LoadData($id);
    if (!$data) {
      $data = array(
        'id' => 0,
        'type' => 'range',
        'name' => '',
      );
    }
    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'edit', $data);
    $return .= $this->_renderTemplate('common', 'admin_footer');
    return $return;
  }


  private function _delete($id) {
    if ($id > 1) {
      $this->_commonObj->Delete($id);
    }
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


  private function _save($id) {

    $data = array(
      'name' => isset($this->_postParams['name']) ? $this->_postParams['name'] : '',
      'type' => isset($this->_postParams['type']) ? $this->_postParams['type'] : '',
      'in_listing' => isset($this->_postParams['in_listing']) ? 1 : 0,
    );

    if ($id) {
      $this->_commonObj->Update($id, $data);
    } else {
      $id = $this->_commonObj->Create($data);
    }

    if (isset($this->_postParams['do_close']) && $this->_postParams['do_close']) {
      header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/list');
    } else {
      header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/edit&id=' . $id);
    }
  }


  private function _saveBulk() {

    if (isset($this->_postParams['item']) && is_array($this->_postParams['item'])) {
      foreach ($this->_postParams['item'] as $itemID) {
        $itemID = (int)$itemID;
        if ($itemID) {
          $sort = isset($this->_postParams['sort'][$itemID]) ? (int)$this->_postParams['sort'][$itemID] : 0;
          $inListing = isset($this->_postParams['in_listing'][$itemID]) ? 1 : 0;
          $this->_commonObj->Update($itemID, array('sort' => $sort, 'in_listing' => $inListing));
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

