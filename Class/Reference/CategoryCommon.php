<?php

class Class_Reference_CategoryCommon extends Class_BaseCommon {

  public $_imageCategoryCommon;


  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_category';
    $this->_objectName = 'category';
    $this->_imageCategoryCommon = new Class_ImageCategoryCommon();
  }


  public function LoadData($id) {
    $return = false;
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT t.*, rua.url
              FROM {$this->_tableName} t
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = '{$this->_objectName}' AND t.id = rua.item_id
              WHERE t.id = '{$id}'
              LIMIT 1";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        $return = $this->_db->Row;
      }
    }
    return $return;
  }


  public function Delete($id) {
    if (parent::Delete($id)) {
      if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img/' . $this->_objectName . '/' . $id . '.jpg')) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/img/' . $this->_objectName . '/' . $id . '.jpg');
      }
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "cache_category
              WHERE id = '{$id}'";
      $this->_db->Query($sql);
      $this->UpdateCache();
      $this->_imageCategoryCommon->ClearCache($id);
      return true;
    } else {
      return false;
    }
  }


  public function UpdateCache() {
    $ids = $this->Find("1", 'id');
    if ($ids) {
      foreach ($ids as $id) {
        $childs = $this->GetChildIDs($id);
        $childs = implode(',', $childs);
        $parents = $this->GetParentIDs($id);
        $parents = implode(',', $parents);
        $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "cache_category
              SET id = '{$id}',
                  parents = '{$parents}',
                  childs = '{$childs}'
              ON DUPLICATE KEY UPDATE
                  parents = '{$parents}',
                  childs = '{$childs}'
             ";
        $this->_db->Query($sql);
      }
    }
  }


  public function GetCache($id) {
    $id = (int)$id;

    $return = array('childs' => '', 'parents' => '');

    if ($id) {
      $anonCommon = new Class_AnonymousCommon('cache_category');
      $cache = $anonCommon->Read($id);
      unset($anonCommon);
      if (is_array($cache)) {
        $return['childs'] = $cache['childs'];
        $return['parents'] = $cache['parents'];
      }
    }
    return $return;
  }


  public function GetRoot($id, $parents = null) {
    $return = 0;
    $id = (int)$id;
    if ($id) {
      if (!isset($parents)) {
        $cache = $this->GetCache($id);
        $parents = $cache['parents'];
      }
      if ($parents) {
        $parents = explode(',', $parents);
        $parents = array_reverse($parents);
        $return = $parents[0];
      }
    }
    return $return;
  }


  public function GetParentIDs($id, $return = array()) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $parent = $this->Read($id, 'parent_id');
    if ($parent) {
      $return[] = $parent;
      return $this->GetParentIDs($parent, $return);
    } else {
      return $return;
    }
  }


  public function GetChildIDs($id) {
    $return = array();
    $id = (int)$id;
    $childs = $this->Find("parent_id = '{$id}'", 'id');
    if ($childs) {
      foreach ($childs as $child) {
        $return[] = $child;
        $return = array_merge($return, $this->GetChildIDs($child));
      }
    } else {
      return $return;
    }
    return $return;
  }


  public function GetChildOptionsList($id, $selectedID = 0, $excludeIDs = array(), $level = 0, $withNoCat = false, $additionalField="") {
    if ($withNoCat) {
      $return = array('<option value="-1"' . ($selectedID == -1 ? ' selected ' : '') . '>-Без рубрики-</option>');
    } else {
      $return = array();
    }
    $excludeIDsStr = implode(',', $excludeIDs);
    $id = (int)$id;
    $childs = $this->Find("parent_id = '{$id}'" . ($excludeIDsStr ? " AND id NOT IN({$excludeIDsStr})" : ''), $additionalField?array("name",$additionalField):"name");//$additionalField?'concat(name,if(`'.$additionalField.'`>0,": NEW","")) as name':'name'
    if ($childs) {
      foreach ($childs as $childID => $childName) {
          if(is_array($childName))
          {
              $data = "";//$childName[$additionalField]?": [NEW]":"";
              $childName = $childName["name"];
          }
        $return[] = "<option value='{$childID}'" . ($childID == $selectedID ? ' SELECTED' : '') . ">" . str_repeat('&nbsp;', $level * 5) . "{$childName}{$data}</option>";
        $return = array_merge($return, $this->GetChildOptionsList($childID, $selectedID, $excludeIDs, $level + 1,false,$additionalField));
      }
    } else {
      return $return;
    }
    return $return;
  }


  public function GetChildCheckboxesList($id, $selectedID = array(), $excludeIDs = array(), $level = 0) {
    $return = array();
    $excludeIDsStr = implode(',', $excludeIDs);
    $id = (int)$id;
    $childs = $this->Find("parent_id = '{$id}'" . ($excludeIDsStr ? " AND id NOT IN({$excludeIDsStr})" : ''), 'name');
    if ($childs) {
      foreach ($childs as $childID => $childName) {
        $return[] = str_repeat('&nbsp;', $level * 5) . "<label><input type='checkbox' name='category[{$childID}]' value='1'" . (in_array($childID, $selectedID) ? ' checked' : '') . " style='width:auto;'> {$childName}</label><br>";
        $return = array_merge($return, $this->GetChildCheckboxesList($childID, $selectedID, $excludeIDs, $level + 1));
      }
    } else {
      return $return;
    }
    return $return;
  }


  public function GetPrimaryImagePath($id, $width, $height, $trimmed = false) {
    $id = (int)$id;
    if (!$id) {
      return false;
    }
    return $this->_imageCategoryCommon->GetPathToThumb($id, $width, $height, $trimmed);
  }


  public function GetSubcategories($id) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT t.id, t.name, t.full_descr, rua.url
              FROM {$this->_tableName} t
              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = '{$this->_objectName}' AND t.id = rua.item_id
              WHERE t.parent_id = '{$id}'
                AND t.hide = 0
              ORDER BY t.sort, t.name";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        foreach ($this->_db->Rows as $row) {
          $return[$row['id']] = array(
            'name' => $row['name'],
            'full_descr' => $row['full_descr'],
            'url' => $row['url'],
          );
        }
      }
    }
    if (count($return) == 0) {
      $return = false;
    }
    return $return;
  }


  public function GetProductsQuant($id, $includeSubs = true, $params = array()) {
    $return = 0;
    $id = (int)$id;
    if ($id) {
      $cats = array($id);
      if ($includeSubs) {
        $cache = $this->GetCache($id);
        if (isset($cache['childs']) && $cache['childs']) {
          $childs = explode(',', $cache['childs']);
          $cats = array_merge($cats, $childs);
        }
      }
      
      $cats = implode(',', $cats);

     $where = $whereJoin = array();
      if (isset($params['brands']) && is_array($params['brands']) && count($params['brands']) > 0) {
        $where[] = "AND p.brand_id IN(" . implode(',', $params['brands']) . ")";
      }
      if (isset($params['price_from']) && (float)$params['price_from'] > 0) {
        $where[] = "AND p.price >= '" . (float)$params['price_from'] . "'";
      }
      if (isset($params['price_to']) && (float)$params['price_to'] > 0) {
        $where[] = "AND p.price <= '" . (float)$params['price_to'] . "'";
      }
      if (isset($params['features']) && is_array($params['features']) && count($params['features']) > 0) {
        foreach ($params['features'] as $featureId => $featureValues) {
           if (isset($featureValues['from']))
           {
            $whereJoin[] = "INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f{$featureId} ON p.id = p2f{$featureId}.product_id AND p2f{$featureId}.feature_id = '{$featureId}'";
            $where[] = "AND CAST(p2f{$featureId}.value_manual AS UNSIGNED)>='{$featureValues['from']}' AND CAST(p2f{$featureId}.value_manual AS UNSIGNED)<='{$featureValues['to']}' ";
           }
           else
          {  
           $whereJoin[] = "INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f{$featureId} ON p.id = p2f{$featureId}.product_id AND p2f{$featureId}.feature_id = '{$featureId}'";
           $where[] = "AND p2f{$featureId}.value_manual IN('" . implode("','", $featureValues) . "')";
          }          
         /*
          $whereJoin[] = "INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f{$featureId} ON p.id = p2f{$featureId}.product_id AND p2f{$featureId}.feature_id = '{$featureId}'";
          $where[] = "AND p2f{$featureId}.value_manual IN(" . implode(',', $featureValues) . ")";
         */ 
        }
      }

      $sql = "SELECT COUNT(DISTINCT p.id) cnt
              FROM " . Class_Config::DB_PREFIX . "ref_product p
              INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c on p.id = p2c.product_id
              " . implode(' ', $whereJoin) . "
              WHERE p2c.category_id IN({$cats})
                AND p.hide = 0
                " . implode(' ', $where);
      
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        $return = $this->_db->Row['cnt'];
      }
    }
    return $return;
  }


  public function GetProducts($id, $includeSubs = true, $orderBy = 'p.price, p.name', $limit = '', $params = array()) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $cats = array($id);
      if ($includeSubs) {
        $cache = $this->GetCache($id);
        if (isset($cache['childs']) && $cache['childs']) {
          $childs = explode(',', $cache['childs']);
          $cats = array_merge($cats, $childs);
        }
      }
      $cats = implode(',', $cats);
      $where = $whereJoin = array();
      if (isset($params['brands']) && is_array($params['brands']) && count($params['brands']) > 0) {
        $where[] = "AND p.brand_id IN(" . implode(',', $params['brands']) . ")";
      }
      if (isset($params['price_from']) && (float)$params['price_from'] > 0) {
        $where[] = "AND p.price >= '" . (float)$params['price_from'] . "'";
      }
      if (isset($params['price_to']) && (float)$params['price_to'] > 0) {
        $where[] = "AND p.price <= '" . (float)$params['price_to'] . "'";
      }
      if (isset($params['features']) && is_array($params['features']) && count($params['features']) > 0) {
        
        foreach ($params['features'] as $featureId => $featureValues) {
           if (isset($featureValues['from']))
           {
            $whereJoin[] = "INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f{$featureId} ON p.id = p2f{$featureId}.product_id AND p2f{$featureId}.feature_id = '{$featureId}'";
            $where[] = "AND CAST(p2f{$featureId}.value_manual AS UNSIGNED)>='{$featureValues['from']}' AND CAST(p2f{$featureId}.value_manual AS UNSIGNED)<='{$featureValues['to']}' ";
           }
           else
          {  
           $whereJoin[] = "INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f{$featureId} ON p.id = p2f{$featureId}.product_id AND p2f{$featureId}.feature_id = '{$featureId}'";
           $where[] = "AND p2f{$featureId}.value_manual IN('" . implode("','", $featureValues) . "')";
          } 
        }
      }
      
      $sql = "SELECT DISTINCT p.id, p.name, p.article, p.price, rua.url
              FROM " . Class_Config::DB_PREFIX . "ref_product p
              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
              INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c on p.id = p2c.product_id
              " . implode(' ', $whereJoin) . "
              WHERE p2c.category_id IN({$cats})
                AND p.hide = 0
                AND p.price > 0
                " . implode(' ', $where) . "
              ORDER BY {$orderBy}
              " . ($limit ? "LIMIT {$limit}" : '');
          
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        foreach ($this->_db->Rows as $row) {
          $return[$row['id']] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'article' => $row['article'],
            'price' => $row['price'],
            'url' => $row['url'],
          );
        }
      }
    }
    if (count($return) == 0) {
      $return = false;
    }
    return $return;
  }


  public function GetFeatures($id, $forSelection = false) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT f.id, f.name, f.type
              FROM " . Class_Config::DB_PREFIX . "ref_feature f
              INNER JOIN " . Class_Config::DB_PREFIX . "link_category_vs_feature lcf ON f.id = lcf.feature_id
              WHERE lcf.category_id = '{$id}'
                " . ($forSelection ? "AND f.in_listing = 1" : '') . "
              ORDER BY f.sort
             ";
          
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        foreach ($this->_db->Rows as $row) {
          $return[$row['id']] = array(
            'name' => $row['name'],
            'type' => $row['type'],
          );
        }
      }
    }
    return $return;
  }


  public function GetFeaturesWithParents($id, $forSelection = false) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $r1 = $this->GetFeaturesForParents($id, $forSelection);
      $r2 = $this->GetFeatures($id, $forSelection);
      foreach ($r1 as $fId => $fData) {
        $return[$fId] = $fData;
      }
      foreach ($r2 as $fId => $fData) {
        $return[$fId] = $fData;
      }
    }
    return $return;
  }


  public function GetFeaturesForParents($id, $forSelection = false) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $parents = $this->GetParentIDs($id);
      if (count($parents) > 0) {
        $parents = implode(',', $parents);
        $sql = "SELECT f.id, f.name, f.type
                FROM " . Class_Config::DB_PREFIX . "ref_feature f
                INNER JOIN " . Class_Config::DB_PREFIX . "link_category_vs_feature lcf ON f.id = lcf.feature_id
                WHERE lcf.category_id IN({$parents})
                  " . ($forSelection ? "AND f.in_selection = 1" : '') . "
                ORDER BY f.sort";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
          foreach ($this->_db->Rows as $row) {
            $return[$row['id']] = array(
              'name'   => $row['name'],
              'type'   => $row['type'],
            );
          }
        }
      }
    }
    return $return;
  }

}