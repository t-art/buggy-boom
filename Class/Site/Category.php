<?php



class Class_Site_Category extends Class_Site_Base {



  protected $_commonObj;





  public function __construct() {

    parent::__construct();

    $this->_commonObj = new Class_Reference_CategoryCommon();

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



    $return = '';



    $data = $this->_commonObj->LoadData($id);


    if (!$data || $data['hide'] == 1) {

      $this->_render404();

    } else {

      $templateData['h1'] = $data['h1'] ? $data['h1'] : $data['name'];

      $templateData['meta_title'] = $data['meta_title'] ? $data['meta_title'] : $data['name'];

      $templateData['meta_keywords'] = $data['meta_keywords'];

      $templateData['meta_description'] = $data['meta_description'];



      $templateData['root_cat'] = $this->_commonObj->GetRoot($id);

      if ($templateData['root_cat'] == 0) {

        $templateData['root_cat'] = $id;

      }



      $templateData['image'] = $this->_commonObj->GetPrimaryImagePath($id, 200, 200);



      $templateData['left_menu'] = $this->_getCategories();



      $templateData['products_viewed'] = $this->_getProductsViewed();



      $templateData['name'] = $data['name'];

      $templateData['full_descr'] = trim($data['full_descr']);

      $templateData['lower_text'] = trim($data['lower_text']);



      $templateData['breadcrumbs'] = $this->_getBreadcrumbs($id);



      $templateData['subcategories'] = $this->_getSubcategories($id);



      $cats = array($id);

      $cache = $this->_commonObj->GetCache($id);

      if (isset($cache['childs']) && $cache['childs']) {

        $childs = explode(',', $cache['childs']);

        $cats = array_merge($cats, $childs);

      }

      $cats = implode(',', $cats);



      $brands = array();

      $sql = "SELECT DISTINCT b.id,b.name

              FROM " . Class_Config::DB_PREFIX . "ref_brand b

              INNER JOIN " . Class_Config::DB_PREFIX . "ref_product p ON b.id = p.brand_id

              INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id

              WHERE p2c.category_id IN({$cats})

                AND p.hide = 0

                AND p.price > 0

              ORDER BY b.name";

      $r = $this->_db->QueryFetch($sql);

      if ($r) {

        foreach ($this->_db->Rows as $row) {

          $brands[$row['id']] = $row['name'];

        }

      }

      $templateData['brands'] = $brands;



      $templateData['brands_selected'] = isset($this->_getParams['b']) && is_array($this->_getParams['b']) ? $this->_getParams['b'] : array();



      $minPrice = '';

      $maxPrice = '';

      $sql = "SELECT MIN(p.price) min_price, MAX(p.price) max_price

              FROM " . Class_Config::DB_PREFIX . "ref_product p

              INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id

              WHERE p2c.category_id IN({$cats})

                AND p.hide = 0

                AND p.price > 0";

      $r = $this->_db->QueryFetch($sql);

      if ($r) {

        $minPrice = $this->_db->Row['min_price'];

        $maxPrice = $this->_db->Row['max_price'];

      }



      $templateData['price_from'] = isset($this->_getParams['price_from']) ? (int)$this->_getParams['price_from'] : 0;

      $templateData['price_from_placeholder'] = $minPrice;



      $templateData['price_to'] = isset($this->_getParams['price_to']) ? (int)$this->_getParams['price_to'] : 0;

      $templateData['price_to_placeholder'] = $maxPrice;


      $templateData['features']=array();
      $templateData['features_selected']=array();
      $templateData['features_pr']=array();
      
      $templateData['features'] = $this->_getFeatures($id);

      $templateData['features_selected'] = isset($this->_getParams['f']) && is_array($this->_getParams['f']) ? $this->_getParams['f'] : array();
      
      $templateData['features_pr'] = isset($this->_getParams['pr']) && is_array($this->_getParams['pr']) ? $this->_getParams['pr'] : array();
      
//      $ppp = isset($_SESSION['show_by']) ? (int)$_SESSION['show_by'] : $this->GetSetting('products_in_category');

      $ppp = 20;

      $page = isset($this->_getParams['page']) ? (int)$this->_getParams['page'] : 0;

      $startPos = $page * $ppp;

      $orderBy = 'p.name';

      $orderByPaginator = 'asc';

//      if (isset($this->_getParams['orderby'])) {

//        if ($this->_getParams['orderby'] == 'desc') {

//          $orderBy = 'p.price DESC, p.name';

//          $orderByPaginator = 'desc';

//        }

//      }
      
      

      $params = array(

        'brands'     => $templateData['brands_selected'],

        'price_from' => $templateData['price_from'],

        'price_to'   => $templateData['price_to'],

        'features'   => $templateData['features_selected']+$templateData['features_pr'] 

      );
      
      
      $productsCnt = $this->_commonObj->GetProductsQuant($id, true, $params);
      $templateData['count_products']=$productsCnt; 
      
      $paginator = new Class_Site_Paginator();

      $dop_link='';
      if (isset($params['brands']) && is_array($params['brands']) && count($params['brands']) > 0) {
        foreach ($params['brands'] as $i=>$br)
          $dop_link.= "&b[]={$br}";    
      }
      
      if (isset($params['price_from']) && (float)$params['price_from'] > 0) {
        $dop_link.="&price_from='".(float)$params['price_from']."'";
      }
      
      if (isset($params['price_to']) && (float)$params['price_to'] > 0) {
        $dop_link.="&price_to='".(float)$params['price_to']."'";
      }

      if (isset($params['features']) && is_array($params['features']) && count($params['features']) > 0) {
        foreach ($params['features'] as $featureId => $featureValues) {
          if (isset($featureValues['from']))
          {
           $dop_link.= "&pr[{$featureId}][from]={$featureValues['from']}"; 
           $dop_link.= "&pr[{$featureId}][to]={$featureValues['to']}"; 
          }
          else
          {
           foreach ($featureValues as $j=>$feat)
             $dop_link.= "&f[{$featureId}][]={$feat}";
          }       
        }
      }
      
      
   //   print_r($params);

      $templateData['paginator'] = $paginator->Show("/{$data['url']}.html{$dop_link}&orderby={$orderByPaginator}", $page, $ppp, $productsCnt, 10);

      unset($paginator);



      $templateData['url'] = "/{$data['url']}.html";



      $templateData['products'] = $this->_getProducts($id, $orderBy, "{$startPos}, {$ppp}", $params);



      $return = $this->_renderHeader($templateData);

      $return .= $this->_renderTemplate('native', 'main', $templateData);

      $return .= $this->_renderFooter($templateData);

    }



    return $return;



  }





  private function _getBreadcrumbs($id) {

    $return = '';

    $id = (int)$id;

    if ($id) {

      $cache = $this->_commonObj->GetCache($id);

      if (isset($cache['parents']) && $cache['parents']) {

        $parents = explode(',', $cache['parents']);

        if (is_array($parents) && count($parents) > 0) {

          $parents = array_reverse($parents);

          foreach ($parents as $parentID) {

            $data = $this->_commonObj->LoadData($parentID);

            $return .= " <div class='item'><a href='/{$data['url']}.html'>{$data['name']}</a></div>";

          }

        }

      }

    }

    return $return;

  }





  private function _getSubcategories($id) {

    $return = $this->_commonObj->GetSubcategories($id);

    if ($return && is_array($return) && count($return) > 0) {

      foreach (array_keys($return) as $categoryID) {

        $return[$categoryID]['image'] = $this->_commonObj->GetPrimaryImagePath($categoryID, 150, 120);

      }

    }

    return $return;

  }





  private function _getProducts($id, $orderBy = 'p.name', $limit = '', $params = array()) {

    $return = $this->_commonObj->GetProducts($id, true, $orderBy, $limit, $params);

    if ($return && is_array($return) && count($return) > 0) {

      $productCommon = new Class_Reference_ProductCommon();

      foreach ($return as $productID => $productData) {

        $return[$productID]['image'] = $productCommon->GetPrimaryImagePath($productID, 200, 200);

//        $return[$productID]['in_comparison'] = isset($_SESSION['comparison'][$productID]) ? 1 : 0;

//        $return[$productID]['features'] = $productCommon->GetFeatures($productID, true);

      }

      unset($productCommon);

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





  private function _getFeatures($id) {

    $return = array();

    $featuresAll = $this->_commonObj->GetFeatures($id, true);
    
    foreach ($featuresAll as $featureId => $featureData) {

      if ($featureData['type'] == 'list') {

        $sql = "SELECT DISTINCT p2f.value_manual

                FROM " . Class_Config::DB_PREFIX . "ref_product p

                INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id

                INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f ON p.id = p2f.product_id AND p2f.feature_id = '{$featureId}'

                WHERE p2c.category_id = '{$id}'

                  AND p.hide = 0

                  AND p.price > 0

                  AND p2f.value_manual <> ''

                ORDER BY p2f.value_manual";
                
        $r = $this->_db->QueryFetch($sql);

        if ($r) {

          $return[$featureId] = array('name' => $featureData['name'], 'values' => array());

          foreach ($this->_db->Rows as $row) {

            $return[$featureId]['values'][] = $row['value_manual'];

          }

        }

      } else {

          $sql = "SELECT MIN(CAST(p2f.value_manual AS UNSIGNED)) min_1, MAX(CAST(p2f.value_manual AS UNSIGNED)) max_1
                  FROM " . Class_Config::DB_PREFIX . "ref_product p
                  INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c ON p.id = p2c.product_id
                  INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_feature p2f ON p.id = p2f.product_id AND p2f.feature_id = '{$featureId}'
                  WHERE p2c.category_id='{$id}'
                    AND p.hide = 0
                    AND p.price > 0
                    AND p2f.value_manual <> ''
                    ";
           
          $r = $this->_db->QueryFetch($sql);
          if ($r) {
            $return[$featureId] = array('name' => $featureData['name'], 'values' => array(), 'from'=>'0', 'to'=>'0');
            
            $return[$featureId]['from'] = $this->_db->Row['min_1'];
            $return[$featureId]['to'] = $this->_db->Row['max_1'];
            
            /*
            if ((float)$return[$featureId]['from']>(float)$return[$featureId]['to'])
            {
             $return[$featureId]['from'] = $this->_db->Row['max_1'];
             $return[$featureId]['to'] = $this->_db->Row['min_1'];
            }*/
            
          }
          
      }

    }

    return $return;

  }



}



