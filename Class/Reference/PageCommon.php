<?php

class Class_Reference_PageCommon extends Class_BaseCommon {


  public function __construct() {
    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . 'ref_page';
    $this->_objectName = 'page';
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
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "cache_page
              WHERE id = '{$id}'";
      $this->_db->Query($sql);
      $this->UpdateCache();
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
        $sql = "INSERT INTO " . Class_Config::DB_PREFIX . "cache_page
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
      $anonCommon = new Class_AnonymousCommon('cache_page');
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


  public function GetChildOptionsList($id, $selectedID = 0, $excludeIDs = array(), $level = 0) {
    $return = array();
    $excludeIDsStr = implode(',', $excludeIDs);
    $id = (int)$id;
    $childs = $this->Find("parent_id = '{$id}'" . ($excludeIDsStr ? " AND id NOT IN({$excludeIDsStr})" : ''), 'name');
    if ($childs) {
      foreach ($childs as $childID => $childName) {
        $return[] = "<option value='{$childID}'" . ($childID == $selectedID ? ' SELECTED' : '') . ">" . str_repeat('&nbsp;', $level * 5) . "{$childName}</option>";
        $return = array_merge($return, $this->GetChildOptionsList($childID, $selectedID, $excludeIDs, $level + 1));
      }
    } else {
      return $return;
    }
    return $return;
  }


  public function GetSubpages($id) {
    $return = array();
    $id = (int)$id;
    if ($id) {
      $sql = "SELECT t.id, t.name, rua.url
              FROM {$this->_tableName} t
              INNER JOIN " . Class_Config::DB_PREFIX . "ref_url_alias rua ON rua.item_type = '{$this->_objectName}' AND t.id = rua.item_id
              WHERE t.parent_id = '{$id}'
                AND t.hide = 0
              ORDER BY t.name";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        foreach ($this->_db->Rows as $row) {
          $return[$row['id']] = array(
            'name' => $row['name'],
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


}