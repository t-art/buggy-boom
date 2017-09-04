<?php

class Class_Admin_Index extends Class_BaseCommon {


  public function Run() {

    $request = isset($this->_getParams['request']) ? $this->_getParams['request'] : 'index/index';

    if ((!isset($_SESSION['is_admin_logged']) || !$_SESSION['is_admin_logged'])
        && $request != 'login/login') {
      header('Location: ./index.php?request=login/login');
      die();
    }

    list($class, $act) = explode('/', $request);

    $return = '';

    switch ($class) {
      case 'login':
        $obj = new Class_Admin_Login();
        $return = $obj->Run($act);
        break;
      case 'setting':
        $obj = new Class_Admin_Setting();
        $return = $obj->Run($act);
        break;
      case 'page':
        $obj = new Class_Admin_Page();
        $return = $obj->Run($act);
        break;
      case 'brand':
        $obj = new Class_Admin_Brand();
        $return = $obj->Run($act);
        break;
      case 'banner':
        $obj = new Class_Admin_Banner();
        $return = $obj->Run($act);
        break;
      case 'feature':
        $obj = new Class_Admin_Feature();
        $return = $obj->Run($act);
        break;
      case 'category':
        $obj = new Class_Admin_Category();
        $return = $obj->Run($act);
        break;
      case 'product':
        $obj = new Class_Admin_Product();
        $return = $obj->Run($act);
        break;
      case 'order':
        $obj = new Class_Admin_Order();
        $return = $obj->Run($act);
        break;
      case 'article':
        $obj = new Class_Admin_Article();
        $return = $obj->Run($act);
        break;
      case 'action':
        $obj = new Class_Admin_Action();
        $return = $obj->Run($act);
        break;
      case 'news':
        $obj = new Class_Admin_News();
        $return = $obj->Run($act);
        break;

      case 'import':
        $obj = new Class_Admin_Import();
        $return = $obj->Run($act);
        break;

      case 'feedback':
        $obj = new Class_Admin_Feedback();
        $return = $obj->Run($act);
        break;
      case 'user':
        $obj = new Class_Admin_User();
        $return = $obj->Run($act);
        break;
      case 'service':
        $obj = new Class_Admin_Service();
        $return = $obj->Run($act);
        break;
      case 'index':
        switch ($act) {
          case 'index':
            $return = $this->_index();
            break;
          default:
            die(get_class($this) . ': unknown action');
        }
        break;
      default:
        $return = 'Unknown controller';
    }

    return $return;

  }


  private function _index() {
    $return = $this->_renderTemplate('common', 'admin_header');
    $return .= $this->_renderTemplate('common', 'admin_footer');
    return $return;
  }

}

