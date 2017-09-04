<?php

class Class_Admin_Feedback extends Class_BaseCommon {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_AnonymousCommon('feedback');
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
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _showList() {

    $return = '';

    $items = array();
    $sql = "SELECT p.*, DATE_FORMAT(p.append_date, '%d.%m.%Y<br>%H:%i') datef
            FROM " . Class_Config::DB_PREFIX . "feedback p
            ORDER BY p.append_date DESC";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $items[] = $row;
      }
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 34));
    $return .= $this->_renderTemplate('native', 'list', array('items' => $items));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _delete($id) {
    $this->_commonObj->Delete($id);
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

