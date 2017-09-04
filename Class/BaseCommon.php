<?php

abstract class Class_BaseCommon {

  protected $_db;

  protected $_tableName;
  protected $_objectName;

  protected $_tplPath;
  protected $_tplPathCommon;

  protected $_getParams;
  protected $_postParams;


  public function __construct() {

    $this->_db = Class_DB::getInstance();

    // Путь к папке с шаблонами
    $path = str_replace('\\', '/', str_replace('_', '/', ltrim(get_class($this), '\\')));
    $this->_tplPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $path . '/tpl';
    unset($path);
    $this->_tplPathCommon = $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl';

    $this->_getParams = $_GET;
    $this->_postParams = $_POST;

  }


  public function __destruct() {
    unset($this->_db);
  }


  /**
   * Поиск объектов по условию
   * @param string $where - условие поиска
   * @param string $fieldName - поле, значение которого нужно получить (или массив для нескольких полей)
   * @param string $sort - порядок сортировки
   * @param int $limit - порция выборки
   * @return array|bool ассоциативный массив найденных объектов ([ID] => значение поля/полей) (false в случае ошибки)
   */
  public function Find($where, $fieldName = '', $sort = '', $limit = 0) {

    if (!$where) {
      return false;
    }

    $fieldName = $fieldName ? $fieldName : 'id';

    $sort = $sort ? 'ORDER BY ' . $sort : '';

    $limit = $limit ? 'LIMIT ' . $limit : '';

    $sql = 'SELECT id FROM ' . $this->_tableName . ' WHERE ' . $where . ' ' . $sort . ' ' . $limit;
    $r = $this->_db->QueryFetch($sql);

    if ($r === false) {
      return false;
    }

    $result = array();
    foreach ($this->_db->Rows as $val) {
      if (is_array($fieldName)) {
        $resultValue = array();
        $data = $this->Read($val['id']);
        foreach ((array)$fieldName as $field) {
          if (isset($data[$field])) {
            $resultValue[$field] = $data[$field];
          }
        }
      } else {
        $resultValue = $this->Read($val['id'], $fieldName);
      }
      $result[$val['id']] = $resultValue;
    }

    return $result;

  }

  /**
   * Получение реквизита первого объекта, удовлетворяющего условиям поиска
   * @param string $where - условие поиска
   * @param string|array $fieldName - поле, значение которого нужно получить (или массив для нескольких полей)
   * @param string $sort - порядок сортировки
   * @return mixed значение реквизита найденного объекта (или ассоциативный массив реквизитов) (false в случае ошибки)
   */
  public function FindFirst($where, $fieldName = '', $sort = '') {

    $result = $this->Find($where, $fieldName, $sort, 1);

    if (is_array($result) && count($result)) {
      $result = array_values($result);
      $result = $result[0];
    }
    else {
      $result = false;
    }

    return $result;

  }

  /**
   * Создание нового объекта
   * @param array $newData - ассоциативный массив данных
   * @return int|bool ID нового объекта (false в случае ошибки)
   */
  public function Create($newData) {

    if (!is_array($newData) || !count($newData)) {
      return false;
    }

    $result = false;

    $fieldsString = '';
    foreach ($newData as $field => $value) {
      $value = $this->_db->Escape(stripslashes(trim($value)));
      $fieldsString .= "`{$field}` = '{$value}', ";
    }
    if ($fieldsString) {
      $fieldsString = substr($fieldsString, 0, -2);
      $sql = 'INSERT INTO ' . $this->_tableName . ' SET ' . $fieldsString;
      $r = $this->_db->Query($sql);
      if ($r) {
        $result = $this->_db->GetLastInsertID();
      }
    }

    return $result;

  }


  /**
   * Получение всех реквизитов объекта
   * @param int $id
   * @param string $fieldName - имя поля, которое необходимо получить (если не указано, то выбираются все поля)
   * @return string|array|bool значение указанного поля или ассоциативный массив полей объекта (false в случае ошибки)
   */
  public function Read($id, $fieldName = '') {

    $result = false;

    $sql = "SELECT " . ($fieldName ? '`'.$fieldName.'`' : '*') . "
      FROM {$this->_tableName}
      WHERE id = '{$id}'
      LIMIT 1
    ";
    $r = $this->_db->QueryFetch($sql);
    if ($r) {
      $result = $fieldName ? $this->_db->Row[$fieldName] : $this->_db->Row;
    }

    return $result;

  }


  /**
   * Обновление существующего объекта
   * @param int $id
   * @param array $newData - ассоциативный массив данных для обновления
   * @return bool
   */
  public function Update($id, $newData) {

    if (!is_array($newData)) {
      return false;
    }

    if (count($newData) == 0) {
      return true;
    }

    $result = false;

    $fieldsString = '';
    foreach ($newData as $field => $value) {
      $value = $this->_db->Escape(stripslashes(trim($value)));
      $fieldsString .= "`{$field}` = '{$value}', ";
    }
    if ($fieldsString) {
      $fieldsString = substr($fieldsString, 0, -2);
      $sql = "UPDATE {$this->_tableName} SET " . $fieldsString . " WHERE id = '{$id}' LIMIT 1";
      $result = $this->_db->Query($sql);
    }

    return $result;

  }


  /**
   * Удаление существующего объекта
   * @param int $id
   * @return bool
   */
  public function Delete($id) {

    $sql = "DELETE FROM {$this->_tableName}
      WHERE id = '{$id}'
      LIMIT 1";

    $r = $this->_db->Query($sql);

    if ($r) {
      $sql = "DELETE FROM " . Class_Config::DB_PREFIX . "ref_url_alias WHERE item_type = '{$this->_objectName}' AND item_id = '{$id}'";
      $this->_db->Query($sql);
    }

    return $r;

  }


  /**
   * Создание копии существующего объекта
   * @param int $id
   * @param array $targetData - ассоциативный массив изменяемых реквизитов объекта-копии
   * @return int|bool ID объекта-копии (false в случае ошибки)
   */
  public function Copy($id, $targetData = array()) {

    $copyData = $this->Read($id);

    unset($copyData['id']);

    return $this->Create(array_merge($copyData, $targetData));

  }


  /**
   * Получение значения настройки
   * @param string $id
   * @return string|bool
   */
  public function GetSetting($id) {
    $return = false;

    if ($id) {
      $sql = "SELECT `value` FROM " . Class_Config::DB_PREFIX . "misc_setting WHERE id = '{$id}'";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        $return = $this->_db->Row['value'];
      }
    }

    return $return;
  }


  /**
   * Отрисовка шаблона
   * @param string $templateType
   * @param string $templateName
   * @param array $data
   * @return string
   */
  protected function _renderTemplate($templateType, $templateName, $data = array()) {
    if ($templateType == 'native') {
      $tplPath = $this->_tplPath;
    } else {
      $tplPath = $this->_tplPathCommon;
    }
    $tplPath .= '/' . $templateName . '.tpl';
    if (file_exists($tplPath)) {
      ob_start();
      include $tplPath;
      $return = ob_get_clean();
    } else {
      $return = '';
    }
    return $return;
  }


  /**
   * Загрузка данных объекта
   * @param int $id
   * @return array|bool
   */
  public function LoadData($id) {
    return $this->Read($id);
  }


  /**
   * Обновление SEO-URL'а
   */
  protected function _updateUrl($url, $id) {
    $id = (int)$id;

    if ($id && $this->_objectName) {
      $sql = "SELECT item_type, item_id FROM " . Class_Config::DB_PREFIX . "ref_url_alias
              WHERE url = '" . $this->_db->Escape($url) . "'
              LIMIT 1";
      $r = $this->_db->QueryFetch($sql);
      if ($r) {
        $r = $this->_db->Row;
      }
      if ($r && ($r['item_type'] != $this->_objectName || $r['item_id'] != $id)) {
        $url .= '_' . $id;
      }

      $sql = "INSERT IGNORE INTO " . Class_Config::DB_PREFIX . "ref_url_alias
              SET url = '" . $this->_db->Escape($url) . "',
                  item_type = '" . $this->_objectName . "',
                  item_id = '" . $id . "'
              ";
      $this->_db->Query($sql);
      $sql = "UPDATE " . Class_Config::DB_PREFIX . "ref_url_alias
              SET url = '" . $this->_db->Escape($url) . "'
              WHERE item_type = '" . $this->_objectName . "'
                AND item_id = '" . $id . "'
              ";
      $this->_db->Query($sql);
    }
  }

}

