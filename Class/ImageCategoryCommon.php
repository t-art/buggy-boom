<?php

final class Class_ImageCategoryCommon extends Class_ImageCommon {

  public function __construct() {
    $this->_cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache/image/category';
    $this->_cacheDirWeb = '/cache/image/category';
    $this->_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/category';
    $this->_dirWeb = '/img/category';
  }


}