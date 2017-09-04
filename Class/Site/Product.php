<?php



class Class_Site_Product extends Class_Site_Base {



  protected $_commonObj;

  protected $_categoryCommon;

  protected $_mainCategoryId;





  public function __construct() {

    parent::__construct();

    $this->_commonObj = new Class_Reference_ProductCommon();

    $this->_categoryCommon = new Class_Reference_CategoryCommon();

  }





  public function Run($act, $params) {

    $return = '';

    switch ($act) {

      case 'show':

        $id = isset($params['id']) ? (int)$params['id'] : 0;

        $return = $this->_show($id);

        break;

      default:

        die(get_class($this) . ': unknown action');

    }

    return $return;

  }





  private function _show($id) {



    $imageProductCommon = new Class_ImageProductCommon();



    $return = '';



    $data = $this->_commonObj->LoadData($id);



    if (!$data || $data['hide'] == 1) {

      $this->_render404();

    } else {

      $templateData['meta_title'] = $data['meta_title'] ? $data['meta_title'] : $data['name'];

      $templateData['meta_keywords'] = $data['meta_keywords'];

      $templateData['meta_description'] = $data['meta_description'];



      $templateData['id'] = $data['id'];

      $templateData['name'] = $data['name'];

      $templateData['article'] = $data['article'];

      $templateData['short_descr'] = $data['short_descr'];

      $templateData['full_descr'] = $data['full_descr'];

      $templateData['price'] = $data['price'];

      $templateData['quantity'] = $data['quantity'];

      $templateData['brand_id'] = $data['brand_id'];

      $templateData['brand_name'] = $data['brand_name'];

      $templateData['brand_country'] = $data['brand_country'];

      $templateData['brand_attention'] = $data['brand_attention'];

      $templateData['brand_attention_url'] = $data['brand_attention_url'];



      $templateData['top_menu_current'] = 0;



      $templateData['breadcrumbs'] = $this->_getBreadcrumbs($id);



      $templateData['root_cat'] = $this->_categoryCommon->GetRoot($this->_mainCategoryId);

      if ($templateData['root_cat'] == 0) {

        $templateData['root_cat'] = $id;

      }



      $templateData['left_menu'] = $this->_getCategories();



      $templateData['products_viewed'] = $this->_getProductsViewed();



      $templateData['products_recommend'] = $this->_getProductsRecommend($id);



      $templateData['primary_image'] = false;

      $templateData['images'] = false;

      $piID = $this->_commonObj->GetPrimaryImageID($id);

      if ($piID) {

        $templateData['primary_image'] = array(

          'id' => $piID,

          'path_middle' => $this->_commonObj->GetPrimaryImagePath($id, 320, 270),

          'path_big' => $imageProductCommon->GetPathToFullSize($piID)

        );

      }

      $images = $this->_commonObj->GetImages($id);

      if (count($images) > 0) {

        $templateData['images'] = array();

        foreach (array_keys($images) as $imageID) {

          $templateData['images'][$imageID] = array(

            'id' => $imageID,

            'path_small' => $imageProductCommon->GetPathToThumb($imageID, 90, 83),

            'path_middle' => $imageProductCommon->GetPathToThumb($imageID, 330, 270),

            'path_big' => $imageProductCommon->GetPathToFullSize($imageID)

          );

        }

      }



      $templateData['features'] = $this->_commonObj->GetFeatures($id);

      $templateData['additionals'] = array();

      $tmp = $this->_commonObj->GetAdditionalProducts($id);

      if ($tmp && is_array($tmp) && count($tmp) > 0) {

        foreach ($tmp as $additionalID => $additionalData) {

          $templateData['additionals'][$additionalID] = array_merge($additionalData, array(

            'image' => $this->_commonObj->GetPrimaryImagePath($additionalID, 148, 120)

          ));

        }

      }



      $templateData['similars'] = array();

      $tmp = $this->_commonObj->GetSimilarProducts($id);

      if ($tmp && is_array($tmp) && count($tmp) > 0) {

        foreach ($tmp as $similarID => $similarData) {

          $templateData['similars'][$similarID] = array_merge($similarData, array(

            'image' => $this->_commonObj->GetPrimaryImagePath($similarID, 148, 120)

          ));

        }

      }



      $templateData['complects'] = array();

      $tmp = $this->_commonObj->GetComplectProducts($id);

      if ($tmp && is_array($tmp) && count($tmp) > 0) {

        foreach ($tmp as $complectID => $complectData) {

          $templateData['complects'][$complectID] = array_merge($complectData, array(

            'image' => $this->_commonObj->GetPrimaryImagePath($complectID, 148, 120)

          ));

        }

      }





      $return = $this->_renderHeader($templateData);

      $return .= $this->_renderTemplate('native', 'main', $templateData);

      $return .= $this->_renderFooter($templateData);



      if (!isset($_SESSION['products_viewed'])) {

        $_SESSION['products_viewed'] = array();

      } else {

        $swp = array_search($id, $_SESSION['products_viewed']);

        if ($swp !== false) {

          unset($_SESSION['products_viewed'][$swp]);

        }

      }

      $_SESSION['products_viewed'][] = $id;

    }



    return $return;



  }





  private function _getBreadcrumbs($id) {

    $return = '';

    $id = (int)$id;

    if ($id) {

      $categoryID = 0;

      $sql = "SELECT category_id

              FROM " . Class_Config::DB_PREFIX . "link_product_vs_category

              WHERE product_id = '{$id}'

                AND is_primary = 1";

      $r = $this->_db->QueryFetch($sql);

      if ($r) {

        $categoryID = $this->_db->Row['category_id'];

        $this->_mainCategoryId = $categoryID;

      }

      if ($categoryID) {

        $cache = $this->_categoryCommon->GetCache($categoryID);

        if (isset($cache['parents'])) {

          if (!$cache['parents']) {

            $cache['parents'] = $categoryID;

          } else {

            $cache['parents'] = $categoryID . ',' . $cache['parents'];

          }

          if ($cache['parents']) {

            $parents = explode(',', $cache['parents']);

            if (is_array($parents) && count($parents) > 0) {

              $parents = array_reverse($parents);

              foreach ($parents as $parentID) {

                $data = $this->_categoryCommon->LoadData($parentID);

                $return .= "<div class='item'><a href='/{$data['url']}.html'>{$data['name']}</a></div>";

              }

            }

          }

        }

      }

    }

    return $return;

  }





  private function _getProductsViewed($limit = 10) {

    $return = array();

    if (isset($_SESSION['products_viewed']) && is_array($_SESSION['products_viewed']) && count($_SESSION['products_viewed']) > 0) {

      $productCommon = new Class_Reference_ProductCommon();

      $swp = array_reverse($_SESSION['products_viewed']);

      $cur = 0;

      foreach ($swp as $id) {

        $data = $productCommon->LoadData($id);

        if ($data) {

          $return[] = array(

            'id'    => $id,

            'name'  => $data['name'],

            'url'   => $data['url'],

            'price' => $data['price'],

          );

          ++$cur;

        }

        if ($cur >= $limit) {

          break;

        }

      }

    }

    return $return;

  }





  private function _getProductsRecommend($id) {

    $return = array();

    $sql = "SELECT DISTINCT p.id, p.name, p.article, p.price, rua.url

              FROM " . Class_Config::DB_PREFIX . "ref_product p

              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id

              WHERE p.id <> '{$id}'

                AND p.hide = 0

                AND p.price > 0

                AND p.recommend = 1

              ORDER BY RAND()

              LIMIT 7";

    $r = $this->_db->QueryFetch($sql);

    if ($r) {

      foreach ($this->_db->Rows as $row) {

        $return[$row['id']] = array(

          'id'      => $row['id'],

          'name'    => $row['name'],

          'article' => $row['article'],

          'price'   => $row['price'],

          'url'     => $row['url'],

          'image'   => $this->_commonObj->GetPrimaryImagePath($row['id'], 80, 80),

        );

      }

    }

    return $return;

  }



}



