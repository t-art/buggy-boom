<?php

class Class_Site_Page extends Class_Site_Base {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_PageCommon();
  }


  public function Run($act, $params) {
    $return = '';
    switch ($act) {
      case 'show':
        $id = isset($params['id']) ? (int)$params['id'] : 0;
        $return = $this->_show($id);
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _show($id) {

    $return = '';

    $data = $this->_commonObj->Read($id);

    if (!$data || $data['hide'] == 1) {
      $this->_render404();
    } else {
      $templateData['meta_title'] = $data['meta_title'] ? $data['meta_title'] : $data['name'];
      $templateData['meta_keywords'] = $data['meta_keywords'];
      $templateData['meta_description'] = $data['meta_description'];

      $templateData['name'] = $data['name'];
      $templateData['text'] = $data['full_descr'];

      $templateData['left_menu'] = $this->_getCategories();

//      $templateData['page_current'] = $id;

      $templateData['breadcrumbs'] = $this->_getBreadcrumbs($id);

//      $templateData['subitems'] = $this->_getSubitems($id);

      $return = $this->_renderHeader($templateData);
      $return .= $this->_renderTemplate('native', 'main', $templateData);
      $return .= $this->_renderFooter($templateData);
    }

    return $return;

  }


  private function _getSubitems($id) {
    $return = array();
    $sql = "SELECT p.id, p.name, rua.url
              FROM " . Class_Config::DB_PREFIX . "ref_page p
              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'page' AND p.id = rua.item_id
              WHERE p.hide = 0
                AND p.parent_id = '{$id}'
              ORDER BY p.sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['id']] = array(
          'name' => $row['name'],
          'url' => $row['url'],
        );
      }
    }
    return $return;
  }


  private function _getBreadcrumbs($id) {
    $return = '';
    $id = (int)$id;
    if ($id) {
      $cache = $this->_commonObj->GetCache($id);
      if (isset($cache['parents']) && $cache['parents']) {
        $parents = explode(',', $cache['parents']);
        if (is_array($parents) && count($parents) > 0) {
          $parents = array_reverse($parents);
          foreach ($parents as $parentID) {
            $data = $this->_commonObj->LoadData($parentID);
            $return .= " - <a href='/{$data['url']}.html'>{$data['name']}</a> ";
          }
        }
      }
    }
    return $return;
  }


}

