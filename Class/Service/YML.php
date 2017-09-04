<?php

final class Class_Service_YML extends Class_Site_Base {


  public function GenerateYML($useExportToMarket = true) {

    $hostName = $_SERVER['SERVER_NAME'];

    $str = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">
<yml_catalog date=\"" . date('Y-m-d H:i') . "\">

<shop>
<name>" . $this->GetSetting('ym_shop_name') . "</name>
<company>" . $this->GetSetting('ym_org_name') . "</company>
<url>http://{$hostName}/</url>

<currencies>
    <currency id=\"RUR\" rate=\"1\" />
</currencies>

<categories>";

    $sql = "SELECT id, parent_id, name
        FROM " . Class_Config::DB_PREFIX . "ref_category
        WHERE hide = 0";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      foreach ($this->_db->Rows as $row) {
        $str .= "<category id=\"{$row['id']}\"".($row['parent_id'] ? " parentId=\"{$row['parent_id']}\"" : '').">" . htmlspecialchars($row['name'],ENT_QUOTES)."</category>\n";
      }
    }
    $str .= "</categories>
<offers>\n";
    $sql = "SELECT p.id, p.name, p.price, p.market_bid, p.quantity, rua.url, p2c.category_id
              FROM " . Class_Config::DB_PREFIX . "ref_product p
              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
              INNER JOIN " . Class_Config::DB_PREFIX . "link_product_vs_category p2c on p.id = p2c.product_id
              WHERE p.hide = 0
                " . ($useExportToMarket ? " AND p.export_to_market = 1 " : '') . "
                -- AND p.quantity > 0
                AND p.price > 0
                AND p2c.is_primary = 1";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $productCommon = new Class_Reference_ProductCommon();
      $imageProductCommon = new Class_ImageProductCommon();
      foreach ($this->_db->Rows as $row) {
        if ($imageID = $productCommon->GetPrimaryImageID($row['id'])) {
          $image = $imageProductCommon->GetPathToFullSize($imageID);
        } else {
          $image = '/img/no_photo.jpg';
        }
        $available = $row['quantity'] > 0 ? 'true' : 'false';
        $str .= "<offer id=\"{$row['id']}\" bid=\"{$row['market_bid']}\" available=\"{$available}\">
<url>http://{$hostName}/{$row['url']}.html?yandex_market</url>
<price>" . number_format($row['price'], 2, '.', '') . "</price>
<currencyId>RUR</currencyId>
<categoryId>{$row['category_id']}</categoryId>
<picture>http://{$hostName}{$image}</picture>
<name>{$row['name']}</name>
</offer>\n";
      }
    }

    $str .= "</offers>
</shop>
</yml_catalog>";

    return $str;
  }

}

