<?php

final class Class_ImageProductCommon extends Class_ImageCommon {

  public function __construct() {
    $this->_cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache/image/product';
    $this->_cacheDirWeb = '/cache/image/product';
    $this->_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/product';
    $this->_dirWeb = '/img/product';
  }


}