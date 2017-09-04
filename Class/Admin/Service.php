<?php

class Class_Admin_Service extends Class_BaseCommon {

  public function Run($act) {
    $return = '';
    switch ($act) {
      case 'import_price':
        $return = $this->_importPriceShow();
        break;
      case 'import_price_import':
        $return = $this->_importPriceImport();
        break;
      default:
        die(get_class($this) . ': unknown action');
    }
    return $return;
  }


  private function _importPriceShow() {

    $return = '';

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'import_price');
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


  private function _importPriceImport() {
    $return = '';

    $appended = $updated = $ignored = 0;

    $fileName = $_FILES['price']['tmp_name'];

    $onlyUpdatePrice = isset($_POST['only_update_price']) && $_POST['only_update_price'] ? 1 : 0;

    $hideIfNotInPrice = isset($_POST['hide_if_not_in_price']) && $_POST['hide_if_not_in_price'] ? 1 : 0;

    try {
      $fileType = PHPExcel_IOFactory::identify($fileName);
      $objReader = PHPExcel_IOFactory::createReader($fileType);

      $objReader->setReadDataOnly(true);
      $objPHPExcel = $objReader->load($fileName);

      $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

      unset($objReader);
      unset($objPHPExcel);

      if (is_array($sheetData) && count($sheetData) > 0) {
        $productsInPrice = array();
        $productCommon = new Class_Reference_ProductCommon();
        $categoryCommon = new Class_Reference_CategoryCommon();
        $brandCommon = new Class_Reference_BrandCommon();
        foreach ($sheetData as $productData) {
          $productData = array_values($productData);
          $article = trim($productData[0]);
          $name = trim($productData[1]);
          $price = (float)$productData[2];
          $categoryName = trim($productData[3]);
          $brandName = trim($productData[4]);
          if ($article) {
            $productID = $productCommon->FindFirst("article = '" . $this->_db->Escape($article) . "'");
            if ($productID) {
              $productCommon->Update($productID, array('price' => $price));
              ++$updated;
              $productsInPrice[$productID] = $productID;
            } elseif (!$onlyUpdatePrice) {
              $brandID = 0;
              if ($brandName) {
                $brandID = $brandCommon->FindFirst("name = '" . $this->_db->Escape($brandName) . "'");
                if (!$brandID) {
                  $brandID = $brandCommon->Create(array('name' => $brandName));
                }
              }
              $productID = $productCommon->Create(array(
                'article'  => $article,
                'name'     => $name,
                'price'    => $price,
                'brand_id' => $brandID
              ));
              if ($productID) {
                $categoryID = $categoryCommon->FindFirst("name = '" . $this->_db->Escape($categoryName) . "'");
                if ($categoryID) {
                  $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "link_product_vs_category
                          SET product_id = '{$productID}',
                              category_id = '{$categoryID}',
                              is_primary = 1";
                  $this->_db->Query($sql);
                }
                $productsInPrice[$productID] = $productID;
              }
              ++$appended;
            }
          } else {
            ++$ignored;
          }
        }
        if ($hideIfNotInPrice && count($productsInPrice) > 0) {
          $sql = "UPDATE " . Class_Config::DB_PREFIX . "ref_product
                  SET hide = 1
                  WHERE id NOT IN(" . implode($productsInPrice) . ")";
          $this->_db->Query($sql);
        }
      }

    } catch (Exception $e) {

    }

    $return .= $this->_renderTemplate('common', 'admin_header', array('cur_menu' => 10));
    $return .= $this->_renderTemplate('native', 'import_price_results', array(
        'appended' => $appended,
        'updated' => $updated,
        'ignored' => $ignored
    ));
    $return .= $this->_renderTemplate('common', 'admin_footer');

    return $return;
  }


}

