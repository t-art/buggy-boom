<?php

class Class_Admin_Category extends Class_BaseCommon {

  protected $_commonObj;
  protected $_featureCommon;


  public function __construct() {
    parent::__construct();
    $this->_commonObj = new Class_Reference_CategoryCommon();
    $this->_featureCommon = new Class_Reference_FeatureCommon();
  }


  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'list':
        $id = isset($this->_getParams['id']) ? (int)$this->_getParams['id'] : 0;
        $return = $this->_showList($id);
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
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _showList($id) {

    $return = '';

    $categoryData = $this->_commonObj->Read($id);

    $items = array();
    if ($categoryData) {
      $items[] = array(
        'id' => $categoryData['parent_id'],
        'name' => 'Вверх',
        'parent_name' => $categoryData['name']
      );
    }
    $sql = "SELECT p.id, p.name, p.hide, p.sort, p.selection_on, p.selection_sizes, p.selection_brands,
                   COUNT(c.id) has_childs, cache.childs
            FROM " . $this->_commonObj->_tableName . " p
            LEFT JOIN " . $this->_commonObj->_tableName . " c ON p.id = c.parent_id
            LEFT JOIN " . Class_Config::DB_PREFIX . "cache_category cache ON p.id = cache.id
            WHERE p.parent_id = '{$id}'
            GROUP BY p.id
            ORDER BY p.sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $row['products'] = 0;
        $row['childs'] = $row['childs'] ? $row['childs'] : '0';
        $sql = "SELECT COUNT(p.id) products
                FROM " . Class_Config::DB_PREFIX . "ref_product p
                INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id
                WHERE (p2c.category_id IN({$row['childs']}) OR p2c.category_id = '{$row['id']}')
               ";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
          $row['products'] = (int)$this->_db->Row['products'];
        }
        $items[] = $row;
      }
    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'list', array('current_category' => $id, 'items' => $items));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _edit($id) {
    $return = '';
    $data = $this->_commonObj->LoadData($id);
    
    if (!$data) {
      $data = array(
        'id'               => 0,
        'parent_id'        => (isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0),
        'name'             => '',
        'url'              => '',
        'full_descr'       => '',
        'lower_text'       => '',
        'hide'             => 0,
        'h1'               => '',
        'meta_title'       => '',
        'meta_keywords'    => '',
        'meta_description' => '',
        'features'         => array(),
        'parent_features'  => array(),
      );
    } else {
      $data['features'] = $this->_commonObj->GetFeatures($id);
      $data['parent_features'] = $this->_commonObj->GetFeaturesForParents($id);
    }
    
    $data['all_features'] = $this->_featureCommon->Find("1", 'name', 'sort');
    
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
      'parent_id' => isset($this->_postParams['parent_id']) ? (int)$this->_postParams['parent_id'] : 0,
      'name' => isset($this->_postParams['name']) ? $this->_postParams['name'] : '',
      'full_descr' => isset($this->_postParams['full_descr']) ? $this->_postParams['full_descr'] : '',
      'lower_text' => isset($this->_postParams['lower_text']) ? $this->_postParams['lower_text'] : '',
      'hide' => isset($this->_postParams['hide']) ? 1 : 0,
      'h1' => isset($this->_postParams['h1']) ? $this->_postParams['h1'] : '',
      'meta_title' => isset($this->_postParams['meta_title']) ? $this->_postParams['meta_title'] : '',
      'meta_keywords' => isset($this->_postParams['meta_keywords']) ? $this->_postParams['meta_keywords'] : '',
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

    if ($id && $_FILES['image']) {
      $this->_commonObj->_imageCategoryCommon->ClearCache($id);
      if ($_FILES['image']['tmp_name']) {
        $r = getimagesize($_FILES['image']['tmp_name']);
        if ($r) {
          move_uploaded_file($_FILES['image']['tmp_name'], "{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}.jpg");
          chmod("{$_SERVER['DOCUMENT_ROOT']}/img/{$this->_commonObj->_objectName}/{$id}.jpg", 0644);
        }
      }
    }

    if ($id) {
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_category_vs_feature
              WHERE category_id = '{$id}'";
      $this->_db->Query($sql);
      if (isset($this->_postParams['feature']) && is_array($this->_postParams['feature']) && count($this->_postParams['feature']) > 0) {
        foreach (array_keys($this->_postParams['feature']) as $featureID) {
          $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_category_vs_feature
                  SET category_id = '{$id}',
                      feature_id = '{$featureID}'";
          $this->_db->Query($sql);
        }
      }
    }

    $this->_commonObj->UpdateCache();

    if (isset($this->_postParams['do_close']) && $this->_postParams['do_close']) {
      header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/list&id=' . $data['parent_id']);
    } else {
      header('Location: ./index.php?request=' . $this->_commonObj->_objectName . '/edit&id=' . $id);
    }
  }


  private function _saveBulk() {

    if (isset($this->_postParams['item']) && is_array($this->_postParams['item'])) {
      foreach ($this->_postParams['item'] as $itemID) {
        $itemID = (int)$itemID;
        if ($itemID) {
          $sort = isset($this->_postParams['sort'][$itemID]) ? (int)$this->_postParams['sort'][$itemID] : 0;
          $hide = isset($this->_postParams['hide'][$itemID]) ? 1 : 0;
          $selectionOn = isset($this->_postParams['selection_on'][$itemID]) ? 1 : 0;
          $selectionSizes = isset($this->_postParams['selection_sizes'][$itemID]) ? 1 : 0;
          $selectionBrands = isset($this->_postParams['selection_brands'][$itemID]) ? 1 : 0;
          $this->_commonObj->Update($itemID, array(
            'hide' => $hide,
            'sort' => $sort,
            'selection_on' => $selectionOn,
            'selection_sizes' => $selectionSizes,
            'selection_brands' => $selectionBrands,
          ));
        }
      }
    }

    header('Location:' . $_SERVER['HTTP_REFERER']);
  }


}

