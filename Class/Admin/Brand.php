<?php

class Class_Admin_Brand extends Class_BaseCommon {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_BrandCommon();
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
    $sql = "SELECT id, name, hide, country, attention FROM " . $this->_commonObj->_tableName;
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
        'name' => '',
        'url' => '',
        'full_descr' => '',
        'hide' => 0,
        'country' => '',
        'attention' => '',
        'attention_url' => '',
        'meta_title' => '',
        'meta_keywords' => '',
        'meta_description' => ''
      );
    }
    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
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
      'name' => isset($this->_postParams['name']) ? $this->_postParams['name'] : '',
      'full_descr' => isset($this->_postParams['full_descr']) ? $this->_postParams['full_descr'] : '',
      'hide' => isset($this->_postParams['hide']) ? 1 : 0,
      'country' => isset($this->_postParams['country']) ? $this->_postParams['country'] : '',
      'attention' => isset($this->_postParams['attention']) ? $this->_postParams['attention'] : '',
      'attention_url' => isset($this->_postParams['attention_url']) ? $this->_postParams['attention_url'] : '',
      'meta_title' => isset($this->_postParams['meta_title']) ? $this->_postParams['meta_title'] : '',
      'meta_keywords' => isset($this->_postParams['meta_keywords']) ? $this->_postParams['meta_keywords'] : '',
      'meta_description' => isset($this->_postParams['meta_description']) ? $this->_postParams['meta_description'] : ''
    );

    if ($id) {
      $this->_commonObj->Update($id, $data);
    } else {
      $id = $this->_commonObj->Create($data);
    }

    if ($id) {
      if (isset($this->_postParams['url']) && $this->_postParams['url']) {
        $url = trim($this->_postParams['url']);
      } else {
        $url = Class_Shared::Transliterate(trim($data['name']));
      }
      $this->_commonObj->_updateUrl($url, $id);
    }

    if ($id && $_FILES['image']['tmp_name']) {
      $this->_commonObj->_imageBrandCommon->ClearCache($id);
      $r = getimagesize($_FILES['image']['tmp_name']);
      if ($r) {
        move_uploaded_file($_FILES['image']['tmp_name'], "{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}.jpg");
        chmod("{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}.jpg", 0644);
      }
    }

    if ($id && $_FILES['flag']['tmp_name']) {
      $r = getimagesize($_FILES['flag']['tmp_name']);
      if ($r) {
        move_uploaded_file($_FILES['flag']['tmp_name'], "{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}_flag.jpg");
        chmod("{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}_flag.jpg", 0644);
      }
    }

    if ($id && isset($this->_postParams['flag_delete'])) {
      unlink("{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}_flag.jpg");
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
          $this->_commonObj->Update($itemID, array('hide' => $hide));
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

