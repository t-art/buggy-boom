<?php

final class Class_Site_Comparison extends Class_Site_Base {


  public function __construct() {
    parent::__construct();

    if (!isset($_SESSION['comparison'])) {
      $_SESSION['comparison'] = array();
    }
  }


  public function AddProduct($id) {
    $id = (int)$id;
    if ($id) {
      $_SESSION['comparison'][$id] = $id;
    }
  }


  public function DeleteProduct($id) {
    $id = (int)$id;
    if ($id) {
      unset($_SESSION['comparison'][$id]);
    }
  }


  public function Show() {

    $templateData = array();
    $templateData['meta_title'] = 'Сравнение товаров';
    $templateData['meta_keywords'] = '';
    $templateData['meta_description'] = '';
    $templateData['name'] = 'Сравнение товаров';
    $templateData['page_current'] = 0;

    $templateData['products'] = $this->_getProducts();

    $return = $this->_renderHeader($templateData);

    $return .= $this->_renderTemplate('native', 'main', $templateData);
    $return .= $this->_renderFooter($templateData);

    return $return;
  }


  public function ShowLeftColumn() {

    if (count($_SESSION['comparison']) > 0) {
      $comparison = array();
      $productCommon = new Class_Reference_ProductCommon();
      foreach ($_SESSION['comparison'] as $productID) {
        $productData = $productCommon->LoadData($productID);
        $comparison[$productID] = array(
          'name' => $productData['name'],
          'url' => $productData['url'],
          'price' => Class_Shared::GetDealerPrice($productData['price'])
        );
      }
      unset($productCommon);
      $templateData['comparison'] = $comparison;
      unset($comparison);

      $return = $this->_renderTemplate('native', 'left_column', $templateData);
    } else {
      $return = '';
    }

    return $return;
  }


  private function _getProducts() {

    $productCommon = new Class_Reference_ProductCommon();

    $return = array();

    $sql = "SELECT p.id, p.name, p.article, p.price, p.quantity, rua.url
            FROM " . Class_Config::DB_PREFIX . "ref_product p
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE p.hide = 0
              AND p.id IN(" . implode(',', $_SESSION['comparison']) . ")
            ORDER BY p.name
           ";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['id']] = $row;
        $return[$row['id']]['image'] = $productCommon->GetPrimaryImagePath($row['id'], 150, 120);
        $return[$row['id']]['features'] = $productCommon->GetFeatures($row['id']);
      }
    }

    unset($productCommon);

    return $return;
  }


}