<?php

final class Class_Site_Order extends Class_Site_Cart {

  public function __construct() {
    parent::__construct();

    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = array('products' => array(), 'totals' => false);
    }
  }


  public function Show() {
    $templateData['meta_title'] = 'Оформление заказа';
    $templateData['meta_keywords'] = '';
    $templateData['meta_description'] = '';

    $templateData['name'] = 'Оформление заказа';

    $templateData['left_menu'] = $this->_getCategories();

    $templateData['email'] = '';
    $templateData['fio'] = '';
    $templateData['phone'] = '';
    $templateData['jur_name'] = '';
    $templateData['jur_details'] = '';

    if ($_SESSION['dealer_id']) {
      $userCommon = new Class_Reference_UserCommon();
      $userData = $userCommon->Read($_SESSION['dealer_id']);
      unset($userCommon);
      if ($userData) {
        $templateData['email'] = $userData['email'];
        $templateData['fio'] = $userData['fio'];
        $templateData['phone'] = $userData['phone'];
        $templateData['jur_name'] = $userData['company_name'];
        $templateData['jur_details'] = $userData['company_details'];
      }
    }

    $templateData['items'] = array();
    $templateData['totals'] = array('quantity' => 0, 'amount' => 0, 'unit' => '');
    if (count($_SESSION['cart']['products']) > 0) {
      $productCommon = new Class_Reference_ProductCommon();
      foreach ($_SESSION['cart']['products'] as $productID => $quantity) {
        $productData = $productCommon->LoadData($productID);
        $dealerPrice = Class_Shared::GetDealerPrice($productData['price'], false);
        $templateData['items'][$productID] = array(
          'id' => $productID,
          'url' => $productData['url'],
          'name' => $productData['name'],
          'article' => $productData['article'],
          'quantity' => $quantity,
          'price' => $dealerPrice,
          'amount' => $dealerPrice * $quantity,
          'image' => $productCommon->GetPrimaryImagePath($productID, 43, 43)
        );
        $templateData['totals']['quantity'] += $quantity;
        $templateData['totals']['amount'] += $dealerPrice * $quantity;
      }
      $unitName = $this->GetUnitName($templateData['totals']['quantity']);
      $templateData['totals']['unit'] = $unitName;
      unset($productCommon);
    }

    $return = $this->_renderHeader($templateData);
    $return .= $this->_renderTemplate('native', 'main', $templateData);
    $return .= $this->_renderFooter($templateData);

    return $return;
  }


  public function Save() {
    if (count($_SESSION['cart']['products']) == 0) {
      return array('response' => 'fail', 'response_text' => 'Корзина пуста', 'response_field' => '');
    }
    $data = $this->_postParams;
    $fio = isset($data['fio']) ? trim(strip_tags($data['fio'])) : '';
    $fio = ($fio == 'Ф.И.О.*') ? '' : $fio;
    $phone = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
    $phone = ($phone == 'Телефон*') ? '' : $phone;
    $email = isset($data['email']) ? trim(strip_tags($data['email'])) : '';
    $email = ($email == 'e-mail*') ? '' : $email;
    $paymentType = isset($data['payment_type']) ? trim(strip_tags($data['payment_type'])) : '';
    $deliveryType = isset($data['delivery_type']) ? trim(strip_tags($data['delivery_type'])) : '';
    $clientType = isset($data['client_type']) ? trim(strip_tags($data['client_type'])) : '';
    $deliveryAddressIndex = isset($data['delivery_address_index']) ? trim(strip_tags($data['delivery_address_index'])) : '';
    $deliveryAddressIndex = ($deliveryAddressIndex == 'Индекс') ? '' : $deliveryAddressIndex;
    $deliveryAddressCity = isset($data['delivery_address_city']) ? trim(strip_tags($data['delivery_address_city'])) : '';
    $deliveryAddressCity = ($deliveryAddressCity == 'Населенный пункт') ? '' : $deliveryAddressCity;
    $deliveryAddressStreet = isset($data['delivery_address_street']) ? trim(strip_tags($data['delivery_address_street'])) : '';
    $deliveryAddressStreet = ($deliveryAddressStreet == 'Улица') ? '' : $deliveryAddressStreet;
    $deliveryAddressHome = isset($data['delivery_address_home']) ? trim(strip_tags($data['delivery_address_home'])) : '';
    $deliveryAddressHome = ($deliveryAddressHome == 'Дом') ? '' : $deliveryAddressHome;
    $deliveryAddressBuilding = isset($data['delivery_address_building']) ? trim(strip_tags($data['delivery_address_building'])) : '';
    $deliveryAddressBuilding = ($deliveryAddressBuilding == 'Корпус') ? '' : $deliveryAddressBuilding;
    $deliveryAddressFlat = isset($data['delivery_address_flat']) ? trim(strip_tags($data['delivery_address_flat'])) : '';
    $deliveryAddressFlat = ($deliveryAddressFlat == 'Квартира') ? '' : $deliveryAddressFlat;
    $jurName = isset($data['jur_name']) ? trim(strip_tags($data['jur_name'])) : '';
    $jurName = ($jurName == 'Название компании') ? '' : $jurName;
    $jurDetails = isset($data['jur_details']) ? trim(strip_tags($data['jur_details'])) : '';
    $jurDetails = ($jurDetails == 'Реквизиты компании') ? '' : $jurDetails;
    $comment = isset($data['comment']) ? trim(strip_tags($data['comment'])) : '';
    $comment = ($comment == 'Комментарии к заказу') ? '' : $comment;
    if ($fio == '') {
      return array('response' => 'fail', 'response_text' => 'Представьтесь, пожалуйста', 'response_field' => 'fio');
    }
    if ($email == '') {
      return array('response' => 'fail', 'response_text' => 'Укажите e-mail', 'response_field' => 'email');
    }
    if ($email != '' && !Class_Shared::ValidateEmail($email)) {
      return array('response' => 'fail', 'response_text' => 'Указан некорректный e-mail', 'response_field' => 'email');
    }
    if ($phone == '') {
      return array('response' => 'fail', 'response_text' => 'Укажите телефон', 'response_field' => 'phone');
    }
    if ($deliveryType == 'courier' || $deliveryType == 'transport_company') {
      if ($deliveryAddressCity == '') {
        return array('response' => 'fail', 'response_text' => 'Укажите адрес доставки', 'response_field' => 'delivery_address_city');
      }
      if ($deliveryAddressStreet == '') {
        return array('response' => 'fail', 'response_text' => 'Укажите адрес доставки', 'response_field' => 'delivery_address_street');
      }
      if ($deliveryAddressHome == '') {
        return array('response' => 'fail', 'response_text' => 'Укажите адрес доставки', 'response_field' => 'delivery_address_home');
      }
    }
    if ($clientType == 'jur') {
      if ($jurName == '') {
        return array('response' => 'fail', 'response_text' => 'Укажите название компании', 'response_field' => 'jur_name');
      }
      if ($jurDetails == '') {
        return array('response' => 'fail', 'response_text' => 'Укажите реквизиты компании', 'response_field' => 'jur_details');
      }
    }

    $orderCommon = new Class_AnonymousCommon('order');
    $orderContentCommon = new Class_AnonymousCommon('order_content');
    $productCommon = new Class_Reference_ProductCommon();
    $products = array();

    $orderData = array(
      'user_id' => isset($_SESSION['dealer_id']) ? (int)$_SESSION['dealer_id'] : 0,
      'append_date' => date('Y-m-d H:i:s'),
      'client_type' => $clientType,
      'payment_type' => $paymentType,
      'delivery_type' => $deliveryType,
      'fio' => $fio,
      'phone' => $phone,
      'email' => $email,
      'jur_name' => $jurName,
      'jur_details' => $jurDetails,
      'delivery_address_index' => $deliveryAddressIndex,
      'delivery_address_city' => $deliveryAddressCity,
      'delivery_address_street' => $deliveryAddressStreet,
      'delivery_address_home' => $deliveryAddressHome,
      'delivery_address_building' => $deliveryAddressBuilding,
      'delivery_address_flat' => $deliveryAddressFlat,
      'note' => $comment,
      'status' => 'new',
    );
    $orderID = $orderCommon->Create($orderData);
    if (!$orderID) {
      return array('response' => 'fail', 'response_text' => 'Не удалось сохранить заказ', 'response_field' => '');
    }
    foreach ($_SESSION['cart']['products'] as $productID => $quantity) {
      $productData = $productCommon->Read($productID);
      $orderContentData = array(
        'order_id' => $orderID,
        'product_id' => $productID,
        'price' => Class_Shared::GetDealerPrice($productData['price'], false),
        'quantity' => $quantity
      );
      $orderContentID = $orderContentCommon->Create($orderContentData);
      if ($orderContentID) {
        $products[$productID] = array(
          'article' => $productData['article'],
          'name' => $productData['name'],
          'price' => $orderContentData['price'],
          'quantity' => $quantity
        );
      }
    }

    $orderNumber = str_pad($orderID, 6, '0', STR_PAD_LEFT);

    // формируем текст мыла
    $subjAdmin = "Заказ {$orderNumber} на сайте " . $_SERVER['HTTP_HOST'];
    $subjClient = "Ваш заказ {$orderNumber} на сайте " . $_SERVER['HTTP_HOST'];
    $message = "Клиент: " . ($clientType == 'phys' ? 'Физ. лицо' : 'Юр. лицо') . "\n";
    if ($clientType == 'phys') {
      $message .= "Ф.И.О.: {$fio}\n";
    } else {
      $message .= "Организация: {$jurName}\n";
      $message .= "Контактное лицо: {$fio}\n";
      $message .= "Реквизиты: {$jurDetails}\n";
    }
    $message .= "Телефон: {$phone}\n";
    if ($email) {
      $message .= "E-mail: {$email}\n";
    }
    $message .= "Оплата: " . ($paymentType == 'cash' ? 'Наличными' : 'Банковский перевод') . "\n";
    $message .= "Доставка: " . ($deliveryType == 'pickup' ? 'Самовывоз' : ($deliveryType == 'courier' ? 'Курьером' : 'Транспортной компанией')) . "\n";
    if ($deliveryType == 'courier' || $deliveryType == 'transport_company') {
      $address = "";
      if ($deliveryAddressIndex) {
        $address .= "{$deliveryAddressIndex} ";
      }
      $address .= "{$deliveryAddressCity} ";
      $address .= "ул. {$deliveryAddressStreet} ";
      $address .= "д. {$deliveryAddressHome} ";
      if ($deliveryAddressBuilding) {
        $address .= "корп. {$deliveryAddressBuilding} ";
      }
      if ($deliveryAddressFlat) {
        $address .= "кв. {$deliveryAddressFlat} ";
      }
      $message .= "Адрес доставки: {$address}\n";
    }
    if ($comment) {
      $message .= "Комментарий: {$comment}\n";
    }
    $goods = '';
    $totalAmount = 0;
    foreach ($products as $productData) {
      $totalAmount += $productData['price'] * $productData['quantity'];
      $goods .= "{$productData['quantity']} x {$productData['article']} {$productData['name']} = " . ($productData['quantity'] * $productData['price']) . " р.\n";
    }
    $message .= "\nСумма заказа: {$totalAmount} р.\n";
    $message .= "\nСписок заказанных позиций:\n" . $goods;

    $adminEmail = $this->GetSetting('order_email');
    $duplicateEmail = $this->GetSetting('duplicate_email');
    if ($adminEmail) {
      @mail($adminEmail, $subjAdmin, $message, "Content-type: text/plain; charset=utf-8 \r\nFrom: {$adminEmail}");
    }
    if ($email) {
      @mail($email, $subjClient, $message, "Content-type: text/plain; charset=utf-8 \r\nFrom: {$adminEmail}");
    }
    if ($duplicateEmail) {
      @mail($duplicateEmail, $subjAdmin, $message, "Content-type: text/plain; charset=utf-8 \r\nFrom: {$duplicateEmail}");
    }
    $_SESSION['cart'] = array('products' => array(), 'totals' => false);

    return array('response' => 'ok', 'order_number' => $orderNumber);
  }

}
