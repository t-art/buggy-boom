<?php

class Class_Admin_Page extends Class_BaseCommon {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_PageCommon();
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $return = $this->_showList($id);
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


  private function _showList($id) {

    $return = '';

    $pageData = $this->_commonObj->Read($id);

    $items = array();
    if ($pageData) {
      $items[] = array(
        'id' => $pageData['parent_id'],
        'name' => 'Вверх',
        'parent_name' => $pageData['name']
      );
    }
    $sql = "SELECT p.id, p.name, p.hide, p.in_header, p.sort, COUNT(c.id) has_childs
            FROM " . $this->_commonObj->_tableName . " p
            LEFT JOIN " . $this->_commonObj->_tableName . " c ON p.id = c.parent_id
            WHERE p.parent_id = '{$id}'
            GROUP BY p.id
            ORDER BY p.sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $items[] = $row;
      }
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 20));
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
        'parent_id' => 0,
        'name' => '',
        'url' => '',
        'external_url' => '',
        'full_descr' => '',
        'lower_text' => '',
        'hide' => 0,
        'in_header' => 0,
        'meta_title' => '',
        'meta_keywords' => '',
        'meta_description' => ''
      );
    }
    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 20));
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
      'parent_id' => isset($this->_postParams['parent_id']) ? (int)$this->_postParams['parent_id'] : 0,
      'name' => isset($this->_postParams['name']) ? $this->_postParams['name'] : '',
      'external_url' => isset($this->_postParams['external_url']) ? $this->_postParams['external_url'] : '',
      'full_descr' => isset($this->_postParams['full_descr']) ? $this->_postParams['full_descr'] : '',
      'lower_text' => isset($this->_postParams['lower_text']) ? $this->_postParams['lower_text'] : '',
      'hide' => isset($this->_postParams['hide']) ? 1 : 0,
      'in_header' => isset($this->_postParams['in_header']) ? 1 : 0,
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

    $this->_commonObj->UpdateCache();

    if (isset($this->_postParams['do_close']) && $this->_postParams['do_close']) {
      header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/list&id=' . $data['parent_id']);
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
          $inHeader = isset($this->_postParams['in_header'][$itemID]) ? 1 : 0;
          $this->_commonObj->Update($itemID, array(
            'hide' => $hide,
            'in_header' => $inHeader,
            'sort' => $sort,
          ));
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

