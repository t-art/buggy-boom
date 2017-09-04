<?php

class Class_Admin_User extends Class_BaseCommon {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_UserCommon();
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $return = $this->_showList();
        break;
      case 'delete':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $this->_delete($id);
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
    $sql = "SELECT u.*, DATE_FORMAT(u.append_date, '%d.%m.%Y %H:%i') datef
            FROM " . $this->_commonObj->_tableName . " u
            ORDER BY u.append_date DESC";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $items = $this->_db->Rows;
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 38));
    $return .= $this->_renderTemplate('native', 'list', array('items' => $items));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _saveBulk() {

    if (isset($this->_postParams['item']) && is_array($this->_postParams['item'])) {
      foreach ($this->_postParams['item'] as $itemID) {
        $itemID = (int)$itemID;
        if ($itemID) {
          $approved = isset($this->_postParams['approved'][$itemID]) ? 1 : 0;
          $discount = isset($this->_postParams['discount'][$itemID]) ? (int)$this->_postParams['discount'][$itemID] : 0;
          $this->_commonObj->Update($itemID, array(
            'approved' => $approved,
            'discount' => $discount,
          ));
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


  private function _delete($id) {
    $this->_commonObj->Delete($id);
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

