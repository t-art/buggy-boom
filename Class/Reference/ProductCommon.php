<?php

class Class_Reference_ProductCommon extends Class_BaseCommon {

  protected $_productImageCommon;

  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_product';
    $this->_objectName = 'product';
    $this->_productImageCommon = new Class_Reference_ProductImageCommon();
  }


  public function LoadData($id) {
    $return = false;
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT t.*, rua.url, p2c.category_id,
              IF (pi.product_id IS NULL, 0, 1) is_on_index,
              b.id brand_id,
              b.name brand_name,
              b.country brand_country,
              b.attention brand_attention,
              b.attention_url brand_attention_url
              FROM {$this->_tableName} t
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = '{$this->_objectName}' AND t.id = rua.item_id
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_brand b ON b.id = t.brand_id
              LEFT JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON t.id = p2c.product_id AND p2c.is_primary = 1
              LEFT JOIN " . Class_Config::DB_PREFIX . "misc_product_on_index pi ON t.id = pi.product_id
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
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_additional_product
              WHERE product_id = '{$id}'
                OR additional_product_id = '{$id}'";
      $this->_db->Query($sql);

      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_similar_product
              WHERE product_id = '{$id}'
                OR similar_product_id = '{$id}'";
      $this->_db->Query($sql);

      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_complect_product
              WHERE product_id = '{$id}'
                OR complect_product_id = '{$id}'";
      $this->_db->Query($sql);

      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_category
              WHERE product_id = '{$id}'";
      $this->_db->Query($sql);

      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "link_product_vs_feature
              WHERE product_id = '{$id}'";
      $this->_db->Query($sql);

      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "misc_product_on_index
              WHERE product_id = '{$id}'";
      $this->_db->Query($sql);

      $images = $this->_productImageCommon->Find("product_id = '{$id}'", 'id');
      if (is_array($images)) {
        foreach ($images as $imageID) {
          $this->_productImageCommon->Delete($imageID);
        }
      }
      return true;
    } else {
      return false;
    }
  }


  public function Update($id, $newData) {
    if (parent::Update($id, $newData)) {
      $images = $this->_productImageCommon->Find("product_id = '{$id}'", 'id');
      if (is_array($images)) {
        foreach ($images as $imageID) {
          $this->_productImageCommon->Update($imageID, array());
        }
      }
    }
  }


  public function GetPrimaryImageID($id) {
    $id = (int)$id;
    if (!$id) {
      return false;
    }
    $imageID = $this->_productImageCommon->FindFirst("product_id = '{$id}'", 'id', 'sort');
    return $imageID;
  }


  public function GetPrimaryImagePath($id, $width, $height, $trimmed = false) {
    $imageID = $this->GetPrimaryImageID($id);
    if (!$imageID) {
      return '/img/no_photo.jpg';
    }
    return $this->_productImageCommon->GetPath($imageID, $width, $height, $trimmed);
  }


  public function GetFeatures($id, $onlyForListing = false) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $return = array();
    $sql = "SELECT f.name, lpf.feature_id, lpf.value_id, lpf.value_manual, fv.value value_name
            FROM " . Class_Config::DB_PREFIX . "link_product_vs_feature lpf
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_feature f ON f.id = lpf.feature_id
            LEFT JOIN " . Class_Config::DB_PREFIX . "ref_feature_value fv ON fv.id = lpf.value_id
            WHERE product_id = '{$id}'
            " . ($onlyForListing ? "AND f.in_listing = 1" : '') . "
           ";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['feature_id']] = array(
          'feature_name' => $row['name'],
          'value_id' => $row['value_id'],
          'value_name' => $row['value_name'],
          'value_manual' => $row['value_manual']
        );
      }
    }
    return $return;
  }


  public function GetImages($id) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $return = $this->_productImageCommon->Find("product_id = '{$id}'", 'sort', 'sort');
    if (!$return) {
      $return = array();
    }
    return $return;
  }


  public function GetAdditionalProducts($id) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $return = array();
    $sql = "SELECT p.id, p.name, p.short_descr, p.price, p.quantity, rua.url, lpap.sort
            FROM " . $this->_tableName . " p
            INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_additional_product lpap ON p.id = lpap.additional_product_id
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE lpap.product_id = '{$id}'
            ORDER BY sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['id']] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'short_descr' => $row['short_descr'],
          'price' => $row['price'],
          'quantity' => $row['quantity'],
          'url' => $row['url'],
          'sort' => $row['sort']
        );
      }
    }
    return $return;
  }


  public function GetSimilarProducts($id) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $return = array();
    $sql = "SELECT p.id, p.name, p.short_descr, p.price, p.quantity, rua.url, lpap.sort
            FROM " . $this->_tableName . " p
            INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_similar_product lpap ON p.id = lpap.similar_product_id
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE lpap.product_id = '{$id}'
            ORDER BY sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['id']] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'short_descr' => $row['short_descr'],
          'price' => $row['price'],
          'quantity' => $row['quantity'],
          'url' => $row['url'],
          'sort' => $row['sort']
        );
      }
    }
    return $return;
  }


  public function GetComplectProducts($id) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $return = array();
    $sql = "SELECT p.id, p.name, p.short_descr, p.price, p.quantity, rua.url, lpap.sort
            FROM " . $this->_tableName . " p
            INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_complect_product lpap ON p.id = lpap.complect_product_id
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE lpap.product_id = '{$id}'
            ORDER BY sort";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['id']] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'short_descr' => $row['short_descr'],
          'price' => $row['price'],
          'quantity' => $row['quantity'],
          'url' => $row['url'],
          'sort' => $row['sort']
        );
      }
    }
    return $return;
  }


  public function GetSecondaryCategories($id) {
    $id = (int)$id;
    if (!$id) {
      return array();
    }
    $return = array();
    $sql = "SELECT DISTINCT category_id
            FROM " . Class_Config::DB_PREFIX . "link_product_vs_category p2c
            WHERE p2c.product_id = '{$id}'
              AND p2c.is_primary = 0";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $return[$row['category_id']] = $row['category_id'];
      }
    }
    return $return;
  }

}