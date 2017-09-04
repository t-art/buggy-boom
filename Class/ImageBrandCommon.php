<?php

final class Class_ImageBrandCommon extends Class_ImageCommon {

  public function __construct() {
    $this->_cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache/image/brand';
    $this->_cacheDirWeb = '/cache/image/brand';
    $this->_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/brand';
    $this->_dirWeb = '/img/brand';
  }


}