<?php

class Class_Site_User extends Class_Site_Base {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_UserCommon();
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'show_signin':
        $return = $this->_showSignIn();
        break;
      case 'show_pcab':
        $return = $this->_showPCab();
        break;
      case 'signup':
        $return = $this->_signUp();
        break;
      case 'signin':
        $return = $this->_signIn();
        break;
      case 'signout':
        $return = $this->_signOut();
        break;
      case 'save_info':
        $return = $this->_saveInfo();
        break;
      default:
        $this->_render404();
    }
    return $return;
  }


  private function _showSignIn() {

    if ($_SESSION['dealer_id']) {
      header("Location: /?route=user/show_pcab");
      die();
    }

    $templateData['meta_title'] = 'Вход для дилеров';
    $templateData['meta_keywords'] = '';
    $templateData['meta_description'] = '';

    $templateData['name'] = 'Вход для дилеров';

    $templateData['signup_caption'] = 'Стать дилером';
    $templateData['signin_caption'] = 'Вход';


    $return = $this->_renderHeader($templateData);
    $return .= $this->_renderTemplate('native', 'show_signin', $templateData);
    $return .= $this->_renderFooter($templateData);

    return $return;
  }


  private function _showPCab() {

    if (!$_SESSION['dealer_id']) {
      header("Location: /?route=user/show_signin");
      die();
    }

    $templateData['meta_title'] = 'Кабинет дилера';
    $templateData['meta_keywords'] = '';
    $templateData['meta_description'] = '';

    $templateData['name'] = 'Кабинет дилера';

    $templateData = array_merge($templateData, $this->_commonObj->Read($_SESSION['dealer_id']));


    $return = $this->_renderHeader($templateData);
    $return .= $this->_renderTemplate('native', 'show_pcab', $templateData);
    $return .= $this->_renderFooter($templateData);

    return $return;
  }


  private function _signUp() {
    $data = $this->_postParams;
    $email = isset($data['email']) ? trim(strip_tags($data['email'])) : '';
    $email = ($email == 'e-mail') ? '' : $email;
    $password = isset($data['password']) ? trim(strip_tags($data['password'])) : '';
    $password = ($password == 'пароль') ? '' : $password;
    $passwordRepeat = isset($data['password_repeat']) ? trim(strip_tags($data['password_repeat'])) : '';
    $passwordRepeat = ($passwordRepeat == 'повторите пароль') ? '' : $passwordRepeat;
    $fio = isset($data['fio']) ? trim(strip_tags($data['fio'])) : '';
    $fio = ($fio == 'Ф.И.О.') ? '' : $fio;
    $phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
    $phone = ($phone == 'Телефон') ? '' : $phone;
    $companyName = isset($data['company_name']) ? trim(strip_tags($data['company_name'])) : '';
    $companyName = ($companyName == 'Наименование организации') ? '' : $companyName;
    $companyDetails = isset($data['company_details']) ? trim(strip_tags($data['company_details'])) : '';
    $companyDetails = ($companyDetails == 'Реквизиты организации') ? '' : $companyDetails;
    $notes = isset($data['notes']) ? trim(strip_tags($data['notes'])) : '';
    $notes = ($notes == 'Примечание') ? '' : $notes;

    $exists = $this->_commonObj->FindFirst("email = '{$email}'", 'id');
    if ($exists) {
      return json_encode(array('response' => 'fail', 'response_text' => 'Заявка с таким e-mail уже отправлена', 'response_field' => 'email'));
    }

    if ($email == '') {
      return json_encode(array('response' => 'fail', 'response_text' => 'Укажите e-mail', 'response_field' => 'email'));
    }
    if ($email != '' && !Class_Shared::ValidateEmail($email)) {
      return json_encode(array('response' => 'fail', 'response_text' => 'Указан некорректный e-mail', 'response_field' => 'email'));
    }
    if ($password == '' && $passwordRepeat == '') {
      return json_encode(array('response' => 'fail', 'response_text' => 'Укажите пароль', 'response_field' => 'password'));
    }
    if ($password != $passwordRepeat) {
      return json_encode(array('response' => 'fail', 'response_text' => 'Пароли не совпадают', 'response_field' => 'password'));
    }

    $userData = array(
      'email' => $email,
      'password' => $password,
      'fio' => $fio,
      'phone' => $phone,
      'company_name' => $companyName,
      'company_details' => $companyDetails,
      'notes' => $notes,
      'discount' => 0,
      'approved' => 0,
      'append_date' => date('Y-m-d H:i:s')
    );
    $res = $this->_commonObj->Create($userData);
    if (!$res) {
      return json_encode(array('response' => 'fail', 'response_text' => 'Не удалось отправить заявку', 'response_field' => ''));
    }

    $message = "E-mail: {$email}\n";
    $message .= "Пароль: {$password}\n";
    $message .= "Ф.И.О.: {$fio}\n";
    $message .= "Телефон: {$phone}\n";
    $message .= "Организация: {$companyName}\n";
    $message .= "Реквизиты: {$companyDetails}\n";
    $message .= "Примечание: {$notes}\n";

    $adminEmail = $this->GetSetting('admin_email');
    if ($adminEmail) {
      $subjAdmin = "Новая заявка на дилерство на сайте http://{$_SERVER['HTTP_HOST']}";
      @mail($adminEmail, $subjAdmin, $message, "Content-type: text/plain; charset=utf-8 \r\nFrom: {$adminEmail}");
    }
    if ($email) {
      $subjClient = "Вы оставили заявку на дилерство на сайте http://{$_SERVER['HTTP_HOST']}";
      @mail($email, $subjClient, $message, "Content-type: text/plain; charset=utf-8 \r\nFrom: {$adminEmail}");
    }

    return json_encode(array('response' => 'ok', 'response_text' => "Ваша заявка отправлена, спасибо!"));
  }


  private function _signIn() {
    $return = false;

    $data = $this->_postParams;
    $email = isset($data['email']) ? trim(strip_tags($data['email'])) : '';
    $email = ($email == 'e-mail') ? '' : $email;
    $password = isset($data['password']) ? trim(strip_tags($data['password'])) : '';
    $password = ($password == 'пароль') ? '' : $password;

    if ($email && $password) {
      $userData = $this->_commonObj->FindFirst("email = '" . $this->_db->Escape($email) . "' AND email <> '' AND approved = 1", array('id', 'password', 'discount'));
      if (isset($userData['id']) && $password == $userData['password']) {
        $return = true;
        $_SESSION['dealer_id'] = $userData['id'];
        $_SESSION['dealer_discount'] = $userData['discount'];
      }
    }

    return json_encode(array('response' => ($return ? 'ok' : 'fail'), 'response_text' => (!$return ? 'Неверный e-mail/пароль или учетная запись еще не активирована' : '')));

  }


  private function _signOut() {
    $_SESSION['dealer_id'] = 0;
    $_SESSION['dealer_discount'] = 0;
    header("Location: /?route=user/show_pcab");
    die();
  }


  private function _saveInfo() {
    if (!$_SESSION['dealer_id']) {
      header("Location: /?route=user/show_signin");
      die();
    }

    $data = $this->_postParams;
    $password = isset($data['password']) ? trim(strip_tags($data['password'])) : '';
    $password = ($password == 'пароль') ? '' : $password;
    $passwordRepeat = isset($data['password_repeat']) ? trim(strip_tags($data['password_repeat'])) : '';
    $passwordRepeat = ($passwordRepeat == 'повторите пароль') ? '' : $passwordRepeat;
    $fio = isset($data['fio']) ? trim(strip_tags($data['fio'])) : '';
    $fio = ($fio == 'Ф.И.О.') ? '' : $fio;
    $phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
    $phone = ($phone == 'Телефон') ? '' : $phone;
    $companyName = isset($data['company_name']) ? trim(strip_tags($data['company_name'])) : '';
    $companyName = ($companyName == 'Наименование организации') ? '' : $companyName;
    $companyDetails = isset($data['company_details']) ? trim(strip_tags($data['company_details'])) : '';
    $companyDetails = ($companyDetails == 'Реквизиты организации') ? '' : $companyDetails;
    $notes = isset($data['notes']) ? trim(strip_tags($data['notes'])) : '';
    $notes = ($notes == 'Примечание') ? '' : $notes;

    if ($password == '' && $passwordRepeat == '') {
      return json_encode(array('response' => 'fail', 'response_text' => 'Укажите пароль', 'response_field' => 'password'));
    }
    if ($password != $passwordRepeat) {
      return json_encode(array('response' => 'fail', 'response_text' => 'Пароли не совпадают', 'response_field' => 'password'));
    }

    $userData = array(
      'password' => $password,
      'fio' => $fio,
      'phone' => $phone,
      'company_name' => $companyName,
      'company_details' => $companyDetails,
      'notes' => $notes,
    );
    $res = $this->_commonObj->Update($_SESSION['dealer_id'], $userData);
    if (!$res) {
      return json_encode(array('response' => 'fail', 'response_text' => 'Не удалось сохранить данные', 'response_field' => ''));
    }

    return json_encode(array('response' => 'ok', 'response_text' => "Данные сохранены"));
  }


}

