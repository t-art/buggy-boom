<?php

class Class_Admin_Product extends Class_BaseCommon {

  protected $_commonObj;
  protected $_categoryCommon;
  protected $_brandCommon;
  protected $_featureCommon;
  protected $_productSizeCommon;
  protected $_productImageCommon;

  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_ProductCommon();
    $this->_categoryCommon = new Class_Reference_CategoryCommon();
    $this->_brandCommon = new Class_Reference_BrandCommon();
    $this->_featureCommon = new Class_Reference_FeatureCommon();
    $this->_productSizeCommon = new Class_AnonymousCommon('ref_product_size');
    $this->_productImageCommon = new Class_Reference_ProductImageCommon();
  }

  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $return = $this->_showList();
        break;
      case 'edit':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $return = $this->_edit($id);
        break;
      case 'delete':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $this->_delete($id);
        break;
      case 'save':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $this->_save($id);
        break;
      case 'save_bulk':
        $this->_saveBulk();
        break;
      case 'move_bulk':
        $this->_moveBulk();
        break;
      case 'get_goods_from_category':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $what = isset($this->_getParams['what']) ? trim($this->_getParams['what']) : '';
        $categoryID = isset($this->_getParams['category_id']) ? (int)$this->_getParams['category_id'] : 0;
        $return = $this->_getGoodsFromCategory($id, $categoryID, $what);
        break;
      case 'get_good_name':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $return = $this->_getGoodName($id);
        break;
      case 'load_features':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $categoryID = isset($this->_getParams['category_id']) ? (int)$this->_getParams['category_id'] : 0;
        $return = $this->_loadFeatures($id, $categoryID);
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }

  private function _showList() {
    $return = '';
    $items = array();
    if (!isset($this->_getParams['filter_name'])) {
      $this->_getParams['filter_name'] = '';
    }
    if (!isset($this->_getParams['filter_article'])) {
      $this->_getParams['filter_article'] = '';
    }
    if (!isset($this->_getParams['filter_category'])) {
      $this->_getParams['filter_category'] = 0;
    }
    if (!isset($this->_getParams['filter_brand'])) {
      $this->_getParams['filter_brand'] = 0;
    }
    if (!isset($this->_getParams['filter_hide'])) {
      $this->_getParams['filter_hide'] = -1;
    }
    if (!isset($this->_getParams['filter_on_index'])) {
      $this->_getParams['filter_on_index'] = -1;
    }
    if (!isset($this->_getParams['filter_export_to_market'])) {
      $this->_getParams['filter_export_to_market'] = -1;
    }
    $where = '1';
    $filterName = trim(preg_replace('#\s+#', ' ', $this->_getParams['filter_name']));
    $filterNameSQL = $filterName ? '%' . str_replace(' ', '%', $filterName) . '%' : '';
    if ($filterNameSQL) {
      $where .= " AND p.name LIKE '{$filterNameSQL}' ";
    }
    $filterArticle = trim(preg_replace('#\s+#', ' ', $this->_getParams['filter_article']));
    $filterArticleSQL = $filterArticle ? '%' . str_replace(' ', '%', $filterArticle) . '%' : '';
    if ($filterArticleSQL) {
      $where .= " AND p.article LIKE '{$filterArticleSQL}' ";
    }
    $filterCategories = implode('', $this->_categoryCommon->GetChildOptionsList(0, (int)$this->_getParams['filter_category'], array(), 0, true, "new"));
    $brands = $this->_brandCommon->Find('1', 'name', 'name');
    $filterBrands = Class_Shared::GetHtmlOptionsList($brands, (int)$this->_getParams['filter_brand']);
    $filterHide = Class_Shared::GetHtmlOptionsList(array(
      '-1' => '',
      '0'  => 'Не скрытые',
      '1'  => 'Скрытые'
    ), (int)$this->_getParams['filter_hide'], false);
    $filterExportToMarket = Class_Shared::GetHtmlOptionsList(array(
      '-1' => '',
      '1'  => 'Да',
      '0'  => 'Нет',
    ), (int)$this->_getParams['filter_export_to_market'], false);
    $filterOnIndex = Class_Shared::GetHtmlOptionsList(array(
      '-1' => '',
      '1'  => 'Да',
      '0'  => 'Нет',
    ), (int)$this->_getParams['filter_on_index'], false);
    $categoryID = (int)$this->_getParams['filter_category'];
    if ($categoryID != 0 && $categoryID != -1) {
      $categoryCommon = new Class_Reference_CategoryCommon();
      $cache = $categoryCommon->GetCache($categoryID);
      unset($categoryCommon);
      if ($cache['childs']) {
        $cache['childs'] .= ',' . $categoryID;
      } else {
        $cache['childs'] = $categoryID;
      }
      $where .= " AND c.id IN({$cache['childs']}) ";
    } elseif ($categoryID == -1) {
      $where .= " AND c.id IS NULL ";
    }
    $brandID = (int)$this->_getParams['filter_brand'];
    if ($brandID) {
      $where .= " AND b.id = '{$brandID}' ";
    }
    $hide = (int)$this->_getParams['filter_hide'];
    if ($hide != -1) {
      $where .= " AND p.hide = '{$hide}' ";
    }
    $onIndex = (int)$this->_getParams['filter_on_index'];
    if ($onIndex != -1) {
      $where .= " AND p.is_on_index = '{$onIndex}' ";
    }
    $exportToMarket = (int)$this->_getParams['filter_export_to_market'];
    if ($exportToMarket != -1) {
      $where .= " AND p.export_to_market = '{$exportToMarket}' ";
    }
    if ($where == '1') {
      $where = '0';
    }
    $productsCnt = 0;
    $sql = "SELECT COUNT(*) cnt
            FROM " . $this->_commonObj->_tableName . " p
            LEFT JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id AND p2c.is_primary = 1
            LEFT JOIN " . Class_Config::DB_PREFIX . "ref_category c ON c.id = p2c.category_id
            LEFT JOIN " . Class_Config::DB_PREFIX . "ref_brand b ON b.id = p.brand_id
            WHERE {$where}";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $productsCnt = $this->_db->Row['cnt'];
    }
    $ppp = 100;
    $page = isset($this->_getParams['page']) ? (int)$this->_getParams['page'] : 0;
    $startPos = $page * $ppp;
    $sql = "SELECT p.id, p.name, p.article, p.hide, p.price, p.quantity, c.name category_name, b.name brand_name, p.sort, p.export_to_market, p.market_bid, p.recommend,p.is_on_index
            FROM " . $this->_commonObj->_tableName . " p
            LEFT JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id AND p2c.is_primary = 1
            LEFT JOIN " . Class_Config::DB_PREFIX . "ref_category c ON c.id = p2c.category_id
            LEFT JOIN " . Class_Config::DB_PREFIX . "ref_brand b ON b.id = p.brand_id
            WHERE {$where}
            GROUP BY p.id
            ORDER BY p.sort, p.name
            LIMIT {$startPos}, {$ppp}";
    //echo $sql;
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $items = $this->_db->Rows;
    }
    $moveCategories = implode('', $this->_categoryCommon->GetChildOptionsList(0, 0, array(), 0, false, "new"));
    $paginator = new Class_Site_Paginator();
    $ru = $_SERVER['REQUEST_URI'];
    $paginatorHtml = $paginator->Show($ru, $page, $ppp, $productsCnt, 10);
    unset($paginator);
    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'list', array(
      'filter_name'             => $filterName,
      'filter_article'          => $filterArticle,
      'filter_categories'       => $filterCategories,
      'filter_brands'           => $filterBrands,
      'filter_hide'             => $filterHide,
      'filter_on_index'         => $filterOnIndex,
      'filter_export_to_market' => $filterExportToMarket,
      'move_categories'         => $moveCategories,
      'items'                   => $items,
      'paginator'               => $paginatorHtml,
    ));
    $return .= $this->_renderTemplate('common', 'admin_footer');
    return $return;
  }

  private function _edit($id) {
    $return = '';
    $data = $this->_commonObj->LoadData($id);
    if (!$data) {
      $data = array(
        'id'               => 0,
        'category_id'      => 0,
        'brand_id'         => 0,
        'name'             => '',
        'article'          => '',
        'url'              => '',
        'short_descr'      => '',
        'full_descr'       => '',
        'price'            => '',
        'quantity'         => '',
        'is_action'        => 0,
        'discount'         => '',
        'is_on_index'      => 0,
        'hide'             => 0,
        'meta_title'       => '',
        'meta_keywords'    => '',
        'meta_description' => '',
        'export_to_market' => '',
        'market_bid'       => '',
      );
      $data['images'] = array();
      $data['additional_products'] = array();
      $data['similar_products'] = array();
      $data['complect_products'] = array();
      $data['other_categories'] = array();
    } else {
      $data['images'] = $this->_commonObj->GetImages($id);
      $data['additional_products'] = $this->_commonObj->GetAdditionalProducts($id);
      $data['similar_products'] = $this->_commonObj->GetSimilarProducts($id);
      $data['complect_products'] = $this->_commonObj->GetComplectProducts($id);
      $data['other_categories'] = $this->_commonObj->GetSecondaryCategories($id);
    }
    $brands = $this->_brandCommon->Find('1', 'name', 'name');
    $data['select_boxes']['brands'] = Class_Shared::GetHtmlOptionsList($brands, $data['brand_id']);
    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'edit', $data);
    $return .= $this->_renderTemplate('common', 'admin_footer');
    return $return;
  }

  private function _delete($id) {
    $this->_commonObj->Delete($id);
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }

  private function _save($id) {
    $data = array(
      'brand_id'         => isset($this->_postParams['brand_id']) ? (int)$this->_postParams['brand_id'] : 0,
      'name'             => isset($this->_postParams['name']) ? $this->_postParams['name'] : '',
      'article'          => isset($this->_postParams['article']) ? $this->_postParams['article'] : '',
      'short_descr'      => isset($this->_postParams['short_descr']) ? $this->_postParams['short_descr'] : '',
      'full_descr'       => isset($this->_postParams['full_descr']) ? $this->_postParams['full_descr'] : '',
      'price'            => isset($this->_postParams['price']) ? (float)str_replace(' ', '', str_replace(',', '.', $this->_postParams['price'])) : 0,
      'quantity'         => isset($this->_postParams['quantity']) ? (int)$this->_postParams['quantity'] : 0,
      'is_action'        => isset($this->_postParams['is_action']) ? 1 : 0,
      'discount'         => isset($this->_postParams['price']) ? (int)$this->_postParams['discount'] : 0,
      'hide'             => isset($this->_postParams['hide']) ? 1 : 0,
      'recommend'        => isset($this->_postParams['recommend']) ? 1 : 0,
      'is_on_index'      => isset($this->_postParams['is_on_index']) ? 1 : 0,
      'export_to_market' => isset($this->_postParams['export_to_market']) ? 1 : 0,
      'market_bid'       => isset($this->_postParams['market_bid']) ? (int)$this->_postParams['market_bid'] : 0,
      'meta_title'       => isset($this->_postParams['meta_title']) ? $this->_postParams['meta_title'] : '',
      'meta_keywords'    => isset($this->_postParams['meta_keywords']) ? $this->_postParams['meta_keywords'] : '',
      'meta_description' => isset($this->_postParams['meta_description']) ? $this->_postParams['meta_description'] : ''
    );
    if ($id) {
      $this->_commonObj->Update($id, $data);
    } else {
      $id = $this->_commonObj->Create($data);
    }
    if ($id) {
      if (isset($this->_postParams['url']) && $this->_postParams['url']) {
        $url = trim($this->_postParams['url']);
      } else {
        $url = Class_Shared::Transliterate(trim($data['name']));
      }
      $this->_commonObj->_updateUrl($url, $id);
    }
    if ($id) {
      $categoryID = isset($this->_postParams['category_id']) ? (int)$this->_postParams['category_id'] : 0;
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_category
              WHERE product_id = '{$id}'";
      $this->_db->Query($sql);
      $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_product_vs_category
              SET product_id = '{$id}',
                  category_id = '{$categoryID}',
                  is_primary = 1";
      $this->_db->Query($sql);
      if (isset($this->_postParams['category']) && is_array($this->_postParams['category'])) {
        foreach (array_keys($this->_postParams['category']) as $cid) {
          $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_product_vs_category
                  SET product_id = '{$id}',
                    category_id = '{$cid}',
                    is_primary = 0";
          $this->_db->Query($sql);
        }
      }
      if (isset($this->_postParams['is_on_index'])) {
        $sql = "INSERT IGNORE INTO " . Class_Config::DB_PREFIX . "misc_product_on_index
                SET product_id = '{$id}'
               ";
      } else {
        $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "misc_product_on_index
                WHERE product_id = '{$id}'
               ";
      }
      $this->_db->Query($sql);
    }
    if ($id) {
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_feature
              WHERE product_id = '{$id}'";
      $this->_db->Query($sql);
      if (isset($this->_postParams['feature']) && is_array($this->_postParams['feature'])) {
        foreach ($this->_postParams['feature'] as $featureID => $featureValue) {
          $featureID = (int)$featureID;
          $featureValue = (int)$featureValue;
          if ($featureID && $featureValue) {
            $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_product_vs_feature
                    SET product_id = '{$id}',
                        feature_id = '{$featureID}',
                        value_id = '{$featureValue}'";
            $this->_db->Query($sql);
          }
        }
      }
      if (isset($this->_postParams['feature_manual']) && is_array($this->_postParams['feature_manual'])) {
        foreach ($this->_postParams['feature_manual'] as $featureID => $featureValue) {
          $featureID = (int)$featureID;
          $featureValue = $this->_db->Escape(trim($featureValue));
          if ($featureID && $featureValue) {
            $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_product_vs_feature
                    SET product_id = '{$id}',
                        feature_id = '{$featureID}',
                        value_manual = '{$featureValue}'";
            $this->_db->Query($sql);
          }
        }
      }
    }
    if ($id && isset($this->_postParams['image']) && is_array($this->_postParams['image'])) {
      foreach ($this->_postParams['image'] as $imageIDtmp => $image) {
        foreach ($image as $imageID => $doNotDel) {
          if (!$doNotDel) {
            $this->_productImageCommon->Delete($imageID);
          } elseif (isset($this->_postParams['image_sort'][$imageIDtmp][$imageID])) {
            $data = array('sort' => (int)$this->_postParams['image_sort'][$imageIDtmp][$imageID]);
            $this->_productImageCommon->Update($imageID, $data);
          }
        }
      }
    }
    if ($id && isset($this->_postParams['additional']) && is_array($this->_postParams['additional'])) {
      foreach ($this->_postParams['additional'] as $additionalIDtmp => $additional) {
        foreach ($additional as $additionalID => $doNotDel) {
          if (!$doNotDel) {
            $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_additional_product
                    WHERE product_id = '{$id}'
                      AND additional_product_id = '{$additionalID}'";
            $this->_db->Query($sql);
          } else {
            $sort = isset($this->_postParams['additional_sort'][$additionalIDtmp][$additionalID]) ? (int)$this->_postParams['additional_sort'][$additionalIDtmp][$additionalID] : 0;
            $sql = "INSERT IGNORE INTO " . Class_Config::DB_PREFIX . "link_product_vs_additional_product
                    SET product_id = '{$id}',
                        additional_product_id = '{$additionalID}',
                        sort = '{$sort}'
                    ON DUPLICATE KEY UPDATE
                        sort = '{$sort}'";
            $this->_db->Query($sql);
          }
        }
      }
    }
    if ($id && isset($this->_postParams['similar']) && is_array($this->_postParams['similar'])) {
      foreach ($this->_postParams['similar'] as $similarIDtmp => $similar) {
        foreach ($similar as $similarID => $doNotDel) {
          if (!$doNotDel) {
            $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_similar_product
                    WHERE product_id = '{$id}'
                      AND similar_product_id = '{$similarID}'";
            $this->_db->Query($sql);
          } else {
            $sort = isset($this->_postParams['similar_sort'][$similarIDtmp][$similarID]) ? (int)$this->_postParams['similar_sort'][$similarIDtmp][$similarID] : 0;
            $sql = "INSERT IGNORE INTO " . Class_Config::DB_PREFIX . "link_product_vs_similar_product
                    SET product_id = '{$id}',
                        similar_product_id = '{$similarID}',
                        sort = '{$sort}'
                    ON DUPLICATE KEY UPDATE
                        sort = '{$sort}'";
            $this->_db->Query($sql);
          }
        }
      }
    }
    if ($id && isset($this->_postParams['complect']) && is_array($this->_postParams['complect'])) {
      foreach ($this->_postParams['complect'] as $complectIDtmp => $complect) {
        foreach ($complect as $complectID => $doNotDel) {
          if (!$doNotDel) {
            $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_complect_product
                    WHERE product_id = '{$id}'
                      AND complect_product_id = '{$complectID}'";
            $this->_db->Query($sql);
          } else {
            $sort = isset($this->_postParams['complect_sort'][$complectIDtmp][$complectID]) ? (int)$this->_postParams['complect_sort'][$complectIDtmp][$complectID] : 0;
            $sql = "INSERT IGNORE INTO " . Class_Config::DB_PREFIX . "link_product_vs_complect_product
                    SET product_id = '{$id}',
                        complect_product_id = '{$complectID}',
                        sort = '{$sort}'
                    ON DUPLICATE KEY UPDATE
                        sort = '{$sort}'";
            $this->_db->Query($sql);
          }
        }
      }
    }
    if ($id && isset($_FILES['image']['tmp_name']) && is_array($_FILES['image']['tmp_name'])) {
      foreach ($_FILES['image']['tmp_name'] as $tmpName) {
        $tmpName = trim($tmpName);
        if ($tmpName) {
          $data = array(
            'product_id' => $id,
            'sort'       => 99
          );
          $imageID = $this->_productImageCommon->Create($data);
          if ($imageID) {
            $r = getimagesize($tmpName);
            if ($r) {
              move_uploaded_file($tmpName, "{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$imageID}.jpg");
              chmod("{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$imageID}.jpg", 0644);
            }
          }
        }
      }
    }
    $redirectURL = isset($this->_postParams['redirect_url']) ? $this->_postParams['redirect_url'] : '';
    if (isset($this->_postParams['do_close']) && $this->_postParams['do_close']) {
      if ($redirectURL) {
        header('Location: ' . $redirectURL);
      } else {
        header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/list');
      }
    } else {
      header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/edit&id=' . $id . ($redirectURL ? '&redirect_url=' . urlencode($redirectURL) : ''));
    }
  }

  private function _saveBulk() {
    if (isset($this->_postParams['item']) && is_array($this->_postParams['item'])) {
      foreach ($this->_postParams['item'] as $itemID) {
        $itemID = (int)$itemID;
        if ($itemID) {
          $recommend = isset($this->_postParams['recommend'][$itemID]) ? 1 : 0;
          $is_on_index = isset($this->_postParams['is_on_index'][$itemID]) ? 1 : 0;
          $hide = isset($this->_postParams['hide'][$itemID]) ? 1 : 0;
          $exportToMarket = isset($this->_postParams['export_to_market'][$itemID]) ? 1 : 0;
          $sort = isset($this->_postParams['sort'][$itemID]) ? (int)$this->_postParams['sort'][$itemID] : 0;
          $price = isset($this->_postParams['price'][$itemID]) ? (float)str_replace(' ', '', str_replace(',', '.', $this->_postParams['price'][$itemID])) : 0;
          $quantity = isset($this->_postParams['quantity'][$itemID]) ? (int)$this->_postParams['quantity'][$itemID] : 0;
          $marketBid = isset($this->_postParams['market_bid'][$itemID]) ? (int)$this->_postParams['market_bid'][$itemID] : 0;
          $this->_commonObj->Update($itemID, array(
            'sort'             => $sort,
            'hide'             => $hide,
            'price'            => $price,
            'quantity'         => $quantity,
            'export_to_market' => $exportToMarket,
            'market_bid'       => $marketBid,
            'recommend'        => $recommend,
            'is_on_index'      => $is_on_index,
          ));
        }
      }
    }
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }

  private function _moveBulk() {
    if (isset($this->_postParams['selected']) && is_array($this->_postParams['selected'])
      && isset($this->_postParams['move_to']) && (int)$this->_postParams['move_to'] > 0
    ) {
      $moveTo = (int)$this->_postParams['move_to'];
      foreach ($this->_postParams['selected'] as $itemID) {
        $itemID = (int)$itemID;
        if ($itemID) {
          $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_category
                  WHERE product_id = '{$itemID}'
                    AND is_primary = 1";
          $this->_db->Query($sql);
          $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_product_vs_category
              SET product_id = '{$itemID}',
                  category_id = '{$moveTo}',
                  is_primary = 1";
          $this->_db->Query($sql);
        }
      }
    }
    header('Location:' . $_SERVER['HTTP_REFERER']);
  }

  private function _getGoodsFromCategory($id, $categoryID, $what) {
    $return = '';
    if (in_array($what, array('similar', 'additional', 'complect'))) {
      $id = (int)$id;
      $categoryID = (int)$categoryID;
      if ($categoryID) {
        $cache = $this->_categoryCommon->GetCache($categoryID);
        $childs = isset($cache['childs']) ? $cache['childs'] : '';
        if ($childs) {
          $childs .= ',' . $categoryID;
        } else {
          $childs = $categoryID;
        }
        $sql = "SELECT p.id, p.name
                FROM " . $this->_commonObj->_tableName . " p
                INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id
                WHERE p2c.category_id IN({$childs})
                  AND p2c.is_primary = 1
                  " . ($id ? "AND p.id <> '{$id}'" : '') . "
                ORDER BY p.name";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
          foreach ($this->_db->Rows as $row) {
            $return .= "<div style='margin-top:5px;'><a href='javascript:{$what}AddPre({$row['id']})' title='Добавить'>{$row['name']}</a></div>";
          }
        }
      }
    }
    $return = $return ? $return : 'Не найдено';
    return $return;
  }

  private function _getGoodName($id) {
    $return = '';
    $id = (int)$id;
    if ($id) {
      $return = $this->_commonObj->Read($id, 'name');
    }
    return $return;
  }

  private function _loadFeatures($id, $categoryID) {
    $return = '';
    $id = (int)$id;
    $categoryID = (int)$categoryID;
    if ($categoryID) {
//      $root = $this->_categoryCommon->GetRoot($categoryID);
//      if ($root) {
//        $categoryID = $root;
//      }
      ob_start();
      $allFeatures = $this->_categoryCommon->GetFeaturesWithParents($categoryID);
      $productFeatures = $this->_commonObj->GetFeatures($id);
      foreach ($allFeatures as $featureID => $featureData) {
        ?>
        <div class="product_feature_item">
          <div class="product_feature_name"><?= $featureData['name'] ?>:</div>
          <div class="product_feature_value">
            <?php
            $productFeatureValue = isset($productFeatures[$featureID]['value_manual']) ? $productFeatures[$featureID]['value_manual'] : '';
            ?>
            <input type="text" name="feature_manual[<?= $featureID ?>]"
                   value="<?= htmlspecialchars($productFeatureValue, ENT_QUOTES) ?>">
            <?
            ?>
          </div>
        </div>
        <?php
      }
      $return = ob_get_clean();
    }
    return $return;
  }


}
