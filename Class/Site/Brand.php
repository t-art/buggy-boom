<?php

class Class_Site_Brand extends Class_Site_Base {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_BrandCommon();
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

      $return = $this->_renderHeader($templateData);
      $return .= $this->_renderTemplate('native', 'main', $templateData);
      $return .= $this->_renderFooter($templateData);
    }

    return $return;

  }


}

