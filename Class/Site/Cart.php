<?php

class Class_Site_Cart extends Class_Site_Base {

  public function __construct() {
    parent::__construct();

    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = array('products' => array(), 'totals' => false);
    }
  }


  public function AddProduct($productID, $quant = 1) {
    $productID = (int)$productID;
    if ($productID) {
      if (!isset($_SESSION['cart']['products'][$productID])) {
        $_SESSION['cart']['products'][$productID] = $quant;
      } else {
        $_SESSION['cart']['products'][$productID] += $quant;
      }
      $this->FillTotals();
    }
  }


  public function DeleteProduct($productID, $sizeID = 0) {
    $productID = (int)$productID;
    $sizeID = (int)$sizeID;
    if ($productID) {
      unset($_SESSION['cart']['products'][$productID][$sizeID]);
      if (!is_array($_SESSION['cart']['products'][$productID]) || count($_SESSION['cart']['products'][$productID]) == 0) {
        unset($_SESSION['cart']['products'][$productID]);
      }
      $this->FillTotals();
    }
  }


  public function Recount($productID, $quant = 1) {
    $productID = (int)$productID;
    if ($productID) {
      $_SESSION['cart']['products'][$productID] = $quant;
    }
    $this->FillTotals();
    $productCommon = new Class_Reference_ProductCommon();
    $price = Class_Shared::GetDealerPrice($productCommon->Read($productID, 'price'), false);
    unset($productCommon);
    return array(
      'unit' => $this->GetUnitName($_SESSION['cart']['totals']['quant']),
      'item_amount' => number_format($quant * $price, 0, ',', ' ')
    );
  }


  public function FillTotals() {
    $totalQuant = 0;
    $totalAmount = 0;
    $productCommon = new Class_Reference_ProductCommon();
    foreach ($_SESSION['cart']['products'] as $productID => $quant) {
      $totalQuant += $quant;
      $totalAmount += $quant * Class_Shared::GetDealerPrice($productCommon->Read($productID, 'price'), false);
    }
    unset($productCommon);
    if ($totalQuant == 0) {
      $_SESSION['cart']['totals'] = false;
    } else {
      $_SESSION['cart']['totals'] = array(
        'quant' => $totalQuant,
        'amount' => $totalAmount
      );
    }
  }


  public function Show() {
    $templateData['meta_title'] = 'Корзина';
    $templateData['meta_keywords'] = '';
    $templateData['meta_description'] = '';

    $templateData['name'] = 'Корзина';

    $templateData['left_menu'] = $this->_getCategories();

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


  public function DelSelected() {
    if (isset($this->_postParams['delete']) && is_array($this->_postParams['delete'])) {
      foreach (array_keys($this->_postParams['delete']) as $productID) {
        unset($_SESSION['cart']['products'][$productID]);
      }
    }
    $this->FillTotals();
    header('Location: /cart.php');
    die();
  }


  public function GetUnitName($quantity) {
    $totalQuantEnding = substr($quantity, -1, 1);
    $quantUnit = '';
    switch ($totalQuantEnding) {
      case 1:
        $quantUnit = 'товар';
        break;
      case 2:
      case 3:
      case 4:
        $quantUnit = 'товара';
        break;
      case 5:
      case 6:
      case 7:
      case 8:
      case 9:
      case 0:
        $quantUnit = 'товаров';
        break;
    }
    if ($quantity >= 11 && $quantity <= 19) {
      $quantUnit = 'товаров';
    }
    return $quantUnit;
  }

}
