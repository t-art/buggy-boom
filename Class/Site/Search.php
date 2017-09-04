<?php

final class Class_Site_Search extends Class_Site_Base {

  private $_what;
  private $_whatSQL;


  public function __construct() {
    parent::__construct();
    $this->_what = isset($this->_getParams['term']) ? trim($this->_getParams['term']) : '';
    $this->_what = preg_replace('#\s+#', ' ', $this->_what);
    $this->_whatSQL = '%' . str_replace(' ', '%', $this->_what) . '%';
  }


  public function Show() {

    $templateData = array();
    $templateData['meta_title'] = 'Поиск';
    $templateData['meta_keywords'] = '';
    $templateData['meta_description'] = '';
    $templateData['name'] = 'Поиск';
    $templateData['page_current'] = 0;

    $templateData['left_menu'] = $this->_getCategories();

    $ppp = 20;
    $page = isset($this->_getParams['page']) ? (int)$this->_getParams['page'] : 0;
    $startPos = $page * $ppp;
    $orderBy = 'p.name';
    $orderByPaginator = 'asc';
    $productsCnt = $this->_getProductsQuant();

    $paginator = new Class_Site_Paginator();
    $templateData['paginator'] = $paginator->Show("/search.php?term={$this->_what}&orderby=asc", $page, $ppp, $productsCnt, 10);
    unset($paginator);

    $templateData['products'] = $this->_getProductsComplete("{$startPos}, {$ppp}");

    $return = $this->_renderHeader($templateData);

    $return .= $this->_renderTemplate('native', 'main', $templateData);
    $return .= $this->_renderFooter($templateData);

    return $return;
  }


  public function ShowAutocomplete($limit) {

    $return = array();

    if ($this->_what) {
      $return = $this->_getProducts($limit);
      foreach ($return as $retID => $retData) {
        $return[$retID]['value'] = "{$retData['name']} {$retData['article']} - " . Class_Shared::GetDealerPrice($retData['price']) . ' руб.';
      }
    }

    return $return;
  }


  private function _getProducts($limit) {

    $productCommon = new Class_Reference_ProductCommon();

    $return = array();

    $sql = "SELECT p.id, p.name, p.article, p.price, p.quantity, rua.url
            FROM " . Class_Config::DB_PREFIX . "ref_product p
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE p.hide = 0
              AND p.price > 0
              AND (name LIKE '{$this->_whatSQL}' OR article LIKE '{$this->_whatSQL}')
            ORDER BY IF(p.quantity > 0, 1, 0) DESC, p.name
            " . ($limit ? "LIMIT {$limit}" : '');
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['id']] = $row;
      }
    }

    unset($productCommon);

    return $return;
  }


  private function _getProductsQuant() {
    $return = 0;
    $sql = "SELECT COUNT(DISTINCT p.id) cnt
            FROM " . Class_Config::DB_PREFIX . "ref_product p
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE p.hide = 0
              AND p.price > 0
              AND (name LIKE '{$this->_whatSQL}' OR article LIKE '{$this->_whatSQL}')";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $return = $this->_db->Row['cnt'];
    }

    return $return;
  }


  private function _getProductsComplete($limit) {

    $productCommon = new Class_Reference_ProductCommon();

    $return = array();

    $products = $this->_getProducts($limit);
    if ($products) {
      foreach ($products as $row) {
        $return[$row['id']] = $row;
//        $return[$row['id']]['image'] = $productCommon->GetPrimaryImagePath($row['id'], 150, 120);
      }
    }

    unset($productCommon);

    return $return;
  }


}