<?php

final class Class_AnonymousCommon extends Class_BaseCommon {

  /**
   * Конструктор класса
   * @param string $tableName - имя таблицы без префикса
   */
  public function __construct($tableName) {

    parent::__construct();
    $this->_tableName = Class_Config::DB_PREFIX . $tableName;

  }

}
