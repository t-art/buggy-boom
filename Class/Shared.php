<?php

class Class_Shared {

  static public function Transliterate($inStr) {
    $inStr = iconv('UTF-8', 'windows-1251//TRANSLIT', $inStr);
    $inStr = iconv('windows-1251', 'UTF-8', $inStr);
    preg_match_all('/./u', $inStr, $inStr);
    $text = $inStr[0];
    $simplePairs = array( 'а' => 'a' , 'л' => 'l' , 'у' => 'u' , 'б' => 'b' , 'м' => 'm' , 'т' => 't' , 'в' => 'v' , 'н' => 'n' , 'ы' => 'y' , 'г' => 'g' , 'о' => 'o' , 'ф' => 'f' , 'д' => 'd' , 'п' => 'p' , 'и' => 'i' , 'р' => 'r' , 'А' => 'A' , 'Л' => 'L' , 'У' => 'U' , 'Б' => 'B' , 'М' => 'M' , 'Т' => 'T' , 'В' => 'V' , 'Н' => 'N' , 'Ы' => 'Y' , 'Г' => 'G' , 'О' => 'O' , 'Ф' => 'F' , 'Д' => 'D' , 'П' => 'P' , 'И' => 'I' , 'Р' => 'R' , );
    $complexPairs = array( 'з' => 'z' , 'ц' => 'c' , 'к' => 'k' , 'ж' => 'zh' , 'ч' => 'ch' , 'х' => 'kh' , 'е' => 'e' , 'с' => 's' , 'ё' => 'jo' , 'э' => 'eh' , 'ш' => 'sh' , 'й' => 'jj' , 'щ' => 'shh' , 'ю' => 'yu' , 'я' => 'ya' , 'З' => 'Z' , 'Ц' => 'C' , 'К' => 'K' , 'Ж' => 'ZH' , 'Ч' => 'CH' , 'Х' => 'KH' , 'Е' => 'E' , 'С' => 'S' , 'Ё' => 'JO' , 'Э' => 'EH' , 'Ш' => 'SH' , 'Й' => 'JJ' , 'Щ' => 'SHH' , 'Ю' => 'JU' , 'Я' => 'JA' , 'Ь' => "" , 'Ъ' => "" , 'ъ' => "" , 'ь' => "" , );
    $specialSymbols = array( "_" => "-", "'" => "", "`" => "", "^" => "", " " => "-", '.' => '', ',' => '', ':' => '', '"' => '', "'" => '', '<' => '', '>' => '', '«' => '', '»' => '', ' ' => '-', );
    $translitLatSymbols = array( 'a','l','u','b','m','t','v','n','y','g','o', 'f','d','p','i','r','z','c','k','e','s', 'A','L','U','B','M','T','V','N','Y','G','O', 'F','D','P','I','R','Z','C','K','E','S', );
    $charsToTranslit = array_merge(array_keys($simplePairs), array_keys($complexPairs));
    $translitTable = array();
    foreach ($simplePairs as $key => $val) {
      $translitTable[$key] = $simplePairs[$key];
    }
    foreach ($complexPairs as $key => $val) {
      $translitTable[$key] = $complexPairs[$key];
    }
    foreach($specialSymbols as $key => $val) {
      $translitTable[$key] = $specialSymbols[$key];
    }
    $result = "";
    $nonTranslitArea = false;
    foreach($text as $char) {
      if (in_array($char, array_keys($specialSymbols))) {
        $result .= $translitTable[$char];
      } elseif (in_array($char, $charsToTranslit)) {
        if($nonTranslitArea) {
          $result .= "";
          $nonTranslitArea = false;
        } $result .= $translitTable[$char];
      } else {
        if (!$nonTranslitArea && in_array($char, $translitLatSymbols)) {
          $result.= "";
          $nonTranslitArea = true;
        } $result .= $char;
      }
    }
    $outStr = preg_replace('#[^a-zA-Z0-9\-_]#', '', $result);
    $outStr = strtolower(preg_replace("/[-]{2,}/", '-', $outStr));
    return $outStr;
  }


  static public function GetHtmlOptionsList($data, $selectedID = 0, $showEmptyElement = true) {
    $return = '';

    if (!is_array($selectedID)) {
      $selectedID = array($selectedID);
    }

    if ($showEmptyElement) {
      $return .= '<option value=""></option>';
    }

    if (is_array($data)) {
      foreach ($data as $rowID => $rowName) {
        $return .= '<option value="' . $rowID . '" ' . (in_array($rowID, $selectedID) ? 'selected' : '') . '>' . $rowName . '</option>';
      }
    }

    return $return;
  }


  static public function GetDealerPrice($price, $doFormat = true) {
    $dealerDiscount = isset($_SESSION['dealer_discount']) ? (float)$_SESSION['dealer_discount'] : 0;
    $return = round($price * (1 - $dealerDiscount / 100));
    if ($doFormat) {
      $return = number_format($return, 0, ',', ' ');
    }
    return $return;
  }


  static public function ValidateEmail($email) {
    return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email);
  }

}