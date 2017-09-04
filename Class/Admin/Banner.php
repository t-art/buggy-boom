<?php

class Class_Admin_Banner extends Class_BaseCommon {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_BannerCommon();
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
    $sql = "SELECT * FROM " . $this->_commonObj->_tableName;
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $items = $this->_db->Rows;
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 30));
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
        'link' => '',
        'hide' => 0
      );
    }
    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 30));
    $return .= $this->_renderTemplate('native', 'edit', $data);
    $return .= $this->_renderTemplate('common', 'admin_footer');
    return $return;
  }


  private function _delete($id) {
    $this->_commonObj->Delete($id);
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


  private function _save($id) {

    $data = array(
      'link' => isset($this->_postParams['link']) ? $this->_postParams['link'] : '',
      'hide' => isset($this->_postParams['hide']) ? 1 : 0
    );

    if ($id) {
      $this->_commonObj->Update($id, $data);
    } else {
      $id = $this->_commonObj->Create($data);
    }

    if ($id && $_FILES['image']) {
      $r = getimagesize($_FILES['image']['tmp_name']);
      if ($r) {
        move_uploaded_file($_FILES['image']['tmp_name'], "{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}.jpg");
        chmod("{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}.jpg", 0644);
      }
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
          $hide = isset($this->_postParams['hide'][$itemID]) ? 1 : 0;
          $sort = isset($this->_postParams['sort'][$itemID]) ? (int)$this->_postParams['sort'][$itemID] : 0;
          $this->_commonObj->Update($itemID, array('hide' => $hide, 'sort' => $sort));
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

