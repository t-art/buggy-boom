<?php

class Class_Admin_Order extends Class_BaseCommon {

  protected $_commonObj;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_AnonymousCommon('order');
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $return = $this->_showList();
        break;
      case 'save_bulk':
        $this->_saveBulk();
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _showList() {

    $return = '';

    $items = array();
    $sql = "SELECT o.*, DATE_FORMAT(o.append_date, '%d.%m.%Y %H:%i') datef,
                   SUM(oc.quantity * oc.price) amount
            FROM " . $this->_commonObj->_tableName . " o
            LEFT JOIN " . $this->_commonObj->_tableName . "_content oc ON o.id = oc.order_id
            GROUP BY o.id
            ORDER BY o.append_date DESC";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $items = $this->_db->Rows;
      foreach ($items as $itemID => $item) {
        $items[$itemID]['content'] = array();
        $sql = "SELECT p.article product_article, p.name product_name, oc.price, oc.quantity
                FROM " . Class_Config::DB_PREFIX . "order_content oc
                LEFT JOIN " . Class_Config::DB_PREFIX . "ref_product p ON p.id = oc.product_id
                WHERE oc.order_id = '{$item['id']}'
               ";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
          foreach ($this->_db->Rows as $row) {
            $str = "{$row['quantity']} x {$row['product_article']} {$row['product_name']} = " . ($row['quantity'] * $row['price']) . ' р.';
            $items[$itemID]['content'][] = $str;
          }
        }

      }
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 36));
    $return .= $this->_renderTemplate('native', 'list', array('items' => $items));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _saveBulk() {

    if (isset($this->_postParams['item']) && is_array($this->_postParams['item'])) {
      foreach ($this->_postParams['item'] as $itemID) {
        $itemID = (int)$itemID;
        if ($itemID) {
          $status = isset($this->_postParams['status'][$itemID]) ? $this->_postParams['status'][$itemID] : '';
          if ($status) {
            $this->_commonObj->Update($itemID, array('status' => $status));
          }
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

