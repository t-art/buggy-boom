<?php

class Class_Admin_Setting extends Class_BaseCommon {

  private $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_AnonymousCommon('misc_setting');
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $return = $this->_showList();
        break;
      case 'save':
        $this->_save();
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _showList() {

    $return = '';

    $settings = array();
    $sql = "SELECT * FROM " . Class_Config::DB_PREFIX . "misc_setting ORDER BY sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $settings = $this->_db->Rows;
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 40));
    $return .= $this->_renderTemplate('native', 'main', array('settings' => $settings));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _save() {

    foreach ($this->_postParams as $k => $v) {
      $k = $this->_db->Escape($k);
      $this->_commonObj->Update($k, array('value' => $v));
    }

    if (isset($_FILES['file']['tmp_name']['header_logo']) && $_FILES['file']['tmp_name']['header_logo']) {
      move_uploaded_file($_FILES['file']['tmp_name']['header_logo'], $_SERVER['DOCUMENT_ROOT'] . '/img/logo.gif');
      chmod($_SERVER['DOCUMENT_ROOT'] . '/img/logo.gif', 0644);
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

