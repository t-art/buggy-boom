<?php

class Class_Admin_Login extends Class_BaseCommon {


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'login':
        $return = $this->_login($this->_postParams);
        break;
      case 'logout':
        $this->_logout();
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _login($params) {
    $login = $this->GetSetting('admin_login');
    $password = $this->GetSetting('admin_password');
    $errText = '';
    if (isset($params['login']) || isset($params['password'])) {
      if ($login != $params['login'] || $password != $params['password']) {
        $errText = 'Неверный логин или пароль!';
        $_SESSION['is_admin_logged'] = 0;
      } else {
        $_SESSION['is_admin_logged'] = 1;
        header('Location: ./index.php');
        die();
      }
    }
    return $this->_renderTemplate('native', 'main', array('errText' => $errText));
  }


  private function _logout() {
    $_SESSION['is_admin_logged'] = 0;
    header('Location: ./index.php');
    die();
  }

}

