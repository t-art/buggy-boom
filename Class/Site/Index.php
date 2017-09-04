<?
final class Class_Site_Index extends Class_Site_Base
{
    public function Run() {
        $request = isset($this->_getParams['request']) ? trim($this->_getParams['request']) : '';
        $route = isset($this->_getParams['route']) ? $this->_getParams['route'] : '';
        $return = '';
        if ($request == '' && $route == '') {
            $return = $this->_index();
        } elseif ($route) {
            list($class, $act) = explode('/', $route);
            switch ($class) {
                case 'user':
                    $obj = new Class_Site_User();
                    $return = $obj->Run($act);
                    break;
                default:
                    $this->_render404();
            }
        } else {
            $sql = "SELECT *              
                    FROM " . Class_Config::DB_PREFIX . "ref_url_alias              
                    WHERE url = '" . $this->_db->Escape($request) . "'              
                    LIMIT 1";
            $r = $this->_db->QueryFetch($sql);
            if ($r) {
                $row = $this->_db->Row;
                switch ($row['item_type']) {
                    case 'page':
                        $id = $row['item_id'];
                        $obj = new Class_Site_Page();
                        $return = $obj->Run('show', array('id' => $id));
                        break;                    
                    case 'category':
                        $id = $row['item_id'];
                        $obj = new Class_Site_Category();
                        $return = $obj->Run('show', array('id' => $id));
                    break;                   
                    case 'brand':
                        $id = $row['item_id'];
                        $obj = new Class_Site_Brand();
                        $return = $obj->Run('show', array('id' => $id));
                    break;                   
                    case 'product':
                        $id = $row['item_id'];
                        $obj = new Class_Site_Product();
                        $return = $obj->Run('show', array('id' => $id));
                    break;                    
                    case 'action':
                        $id = $row['item_id'];
                        $obj = new Class_Site_Action();
                        $return = $obj->Run('show_item', array('id' => $id));
                    break;                    
                    case 'news':
                        $id = $row['item_id'];
                        $obj = new Class_Site_News();
                        $return = $obj->Run('show_item', array('id' => $id));
                    break;                    
                    default:
                        $this->_render404();
                }
            } else {
                $this->_render404();
            }
        }
        return $return;
    }
    private function _index() {
        $templateData = array();
        $pageCommon = new Class_Reference_PageCommon();
        $pageData = $pageCommon->Read(1);        unset($pageCommon);
        if (!$pageData) {
            $this->_render404();
        }
        $templateData['news'] = array();
        $max = $this->GetSetting('news_on_index');
        $sql = "SELECT n.id, n.name, DATE_FORMAT(n.date, '%d.%m.%Y') datef, n.short_descr, rua.url            
                FROM " . Class_Config::DB_PREFIX . "ref_news n            
                INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = 'news' AND n.id = rua.item_id            
                WHERE n.hide = 0            
                ORDER BY n.date DESC            
                LIMIT {$max}";
        $r = $this->_db->QueryFetch($sql);
        if ($r) {
            foreach ($this->_db->Rows as $row) {
                $templateData['news'][$row['id']] = $row;
            }
        }
        $templateData['foreword'] = $pageData['full_descr'];
        $templateData['categories'] = $this->_getCategories();
        $templateData['meta_title'] = $pageData['meta_title'];
        $templateData['meta_keywords'] = $pageData['meta_keywords'];
        $templateData['meta_description'] = $pageData['meta_description'];
        $templateData['left_menu'] = $this->_getCategories();
        $templateData['banners'] = $this->_getBanners();
        $templateData['p_on_index'] = $this->_getOnIndex();
        $return = $this->_renderHeader($templateData);
        $return .= $this->_renderTemplate('native', 'main', $templateData);
        $return .= $this->_renderFooter($templateData);
        return $return;
    }
}