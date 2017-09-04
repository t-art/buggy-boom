<?php

class Class_Site_Base extends Class_BaseCommon
{


    protected function _render404()
    {
        header("HTTP/1.1 404 Not Found");
        die();
    }


    protected function _renderHeader($templateData = array())
    {
        $classCart = new Class_Site_Cart();
        $classCart->FillTotals();
        unset($classCart);

        $templateData['pages'] = array();
        $sql = "SELECT p.id, p.name, IF(p.id = 1, '/', IF(p.external_url <> '', p.external_url, CONCAT('/', rua.url, '.html'))) url
              FROM " . Class_Config::DB_PREFIX . "ref_page p
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'page' AND p.id = rua.item_id
              WHERE p.hide = 0
                AND p.in_header = 1
              ORDER BY p.sort";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            foreach ($this->_db->Rows as $row) {
                $templateData['pages'][$row['id']] = $row;
            }
        }

        return $this->_renderTemplate('common', 'site_header', $templateData);
    }


    protected function _renderFooter($templateData = array())
    {
        $templateData['pages'] = array();
        $sql = "SELECT p.id, p.name, IF(p.id = 1, '/', IF(p.external_url <> '', p.external_url, CONCAT('/', rua.url, '.html'))) url
              FROM " . Class_Config::DB_PREFIX . "ref_page p
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'page' AND p.id = rua.item_id
              WHERE p.hide = 0
                AND p.in_header = 1
              ORDER BY p.sort";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            foreach ($this->_db->Rows as $row) {
                $templateData['pages'][$row['id']] = $row;
            }
        }
        $templateData['pages2'] = array();
        $sql = "SELECT p.id, p.name, IF(p.id = 1, '/', IF(p.external_url <> '', p.external_url, CONCAT('/', rua.url, '.html'))) url
              FROM " . Class_Config::DB_PREFIX . "ref_page p
              LEFT JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'page' AND p.id = rua.item_id
              WHERE p.hide = 0
                AND p.in_footer = 1
              ORDER BY p.sort";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            foreach ($this->_db->Rows as $row) {
                $templateData['pages2'][$row['id']] = $row;
            }
        }
        $templateData['counters'] = $this->GetSetting('counters');
        return $this->_renderTemplate('common', 'site_footer', $templateData);
    }


    protected function _getCategories()
    {
        $return = array();
        $sql = "SELECT c.id, c.name, rua.url
            FROM " . Class_Config::DB_PREFIX . "ref_category c
             INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'category' AND c.id = rua.item_id
            WHERE parent_id = 0
              AND hide = 0
            ORDER BY sort";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            $r_ = $this->_db->Rows;
            foreach ($r_ as $row) {
                $_commonObj = new Class_Reference_CategoryCommon();
                $image = $_commonObj->GetPrimaryImagePath($row['id'], 30, 30);
                $return[$row['id']] = array('id' => $row['id'], 'name' => $row['name'], 'url' => $row['url'], 'subitems' => array(),'image'=>$image);
                $sql = "SELECT c.id, c.name, rua.url
            FROM " . Class_Config::DB_PREFIX . "ref_category c
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'category' AND c.id = rua.item_id
            WHERE c.parent_id = '{$row['id']}'
              AND c.hide = 0
            ORDER BY c.sort";
                $r2 = $this->_db->QueryFetch($sql);
                if ($r2) {
                    $r2_ = $this->_db->Rows;
                    foreach ($r2_ as $row2) {
                        $return[$row['id']]['subitems'][] = $row2;
                    }
                }
            }
        }
        return $return;
    }

    protected function _getBanners()
    {
        $return = array();
        $sql = "SELECT id, concat('/img/banner/',id,'.jpg') as image, link
            FROM " . Class_Config::DB_PREFIX . "ref_banner
            WHERE hide = 0
            ORDER BY sort";

        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            $r_ = $this->_db->Rows;
            foreach ($r_ as $row) {
                $return[$row['id']] = $row;
            }
        }
        return $return;
    }

    protected function _getOnIndex()
    {
        $return = array();
        $sql = "SELECT p.*,rua.url
            FROM " . Class_Config::DB_PREFIX . "ref_product p
            INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'product' AND p.id = rua.item_id
            WHERE hide = 0 AND p.price > 0 and is_on_index=1
            ORDER BY rand() limit 8";

        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            $r_ = $this->_db->Rows;
            foreach ($r_ as $row) {
                $productCommon = new Class_Reference_ProductCommon();
                $row['image'] = $productCommon->GetPrimaryImagePath($row['id'], 200, 200);
                $return[$row['id']] = $row;
            }
        }
        return $return;
    }


}

