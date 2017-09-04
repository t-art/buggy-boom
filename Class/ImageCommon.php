<?php

class Class_ImageCommon {

  protected $_cacheDir;
  protected $_cacheDirWeb;
  protected $_dir;
  protected $_dirWeb;


  public function __construct() {
    $this->_cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache/image';
    $this->_cacheDirWeb = '/cache/image';
    $this->_dir = $_SERVER['DOCUMENT_ROOT'] . '/img';
    $this->_dirWeb = '/img';
  }


  public function ClearCache($id) {
    $id = (int)$id;
    $dirPath = $this->_cacheDir . '/' . $id;
    if ($id && file_exists($dirPath) && is_dir($dirPath)) {
      if ($handle = opendir($dirPath)) {
        while (false !== ($entry = readdir($handle))) {
          if ($entry != '.' && $entry != '..') {
            unlink($dirPath . '/' . $entry);
          }
        }
        closedir($handle);
        rmdir($dirPath);
      }
    }
  }


  public function GetPathToFullSize($id) {
    $return = false;
    if (isset($_SESSION['is_admin_logged']) && $_SESSION['is_admin_logged']) {
      $return = $this->_dirWeb . "/{$id}.jpg";
    } else {
      if (file_exists("{$this->_cacheDir}/{$id}/fullsize.jpg")) {
        $return = "{$this->_cacheDirWeb}/{$id}/fullsize.jpg";
      } else {
        if (file_exists("{$this->_cacheDir}/{$id}") || (mkdir("{$this->_cacheDir}/{$id}") && chmod("{$this->_cacheDir}/{$id}", 0777))) {
          $sourceFile = "{$this->_dir}/{$id}.jpg";
          $size = @getimagesize($sourceFile);
          if ($size && $size['mime']) {
            list($tmp, $mime) = explode('/', $size['mime']);
            if ($mime) {
              $functionName = "imagecreatefrom{$mime}";
              if (function_exists($functionName)) {
                $widthImage = $size[0];
                $heightImage = $size[1];
                $wmImagePath = $_SERVER['DOCUMENT_ROOT'] . '/img/watermark.png';
                $size = getimagesize($wmImagePath);
                $widthWM = $size[0];
                $heightWM = $size[1];
                if ($widthImage >= $widthWM && $heightImage >= $heightWM) {
                  $watermark = imagecreatefrompng($wmImagePath);
                  $image = $functionName($sourceFile);
                  $destX = round(($widthImage - $widthWM) / 2);
                  $destY = round(($heightImage - $heightWM) / 2);

                  imagealphablending($image, true);
                  imagealphablending($watermark, true);
                  imagecopy($image, $watermark, $destX, $destY, 0, 0, $widthWM , $heightWM);


                  imagejpeg($image, "{$this->_cacheDir}/{$id}/fullsize.jpg", 90);
                  chmod("{$this->_cacheDir}/{$id}/fullsize.jpg", 0777);
                  imagedestroy($image);
                  imagedestroy($watermark);
                  $return = $this->_cacheDirWeb . "/{$id}/fullsize.jpg";
                } else {
                  copy($sourceFile, $this->_cacheDir . "/{$id}/fullsize.jpg");
                  $return = $this->_cacheDirWeb . "/{$id}/fullsize.jpg";
                }
              }
            }
          }
        }
      }
    }
    return $return ? $return : "/img/no_photo.jpg";
  }


  public function GetPathToThumb($id, $width, $height, $trimmed = false) {
    if (!$trimmed) {
      return $this->GetPathToNonTrimmedThumb($id, $width, $height);
    } else {
      return $this->GetPathToTrimmedThumb($id, $width, $height);
    }
  }


  public function GetPathToNonTrimmedThumb($id, $width, $height) {

    $return = false;

    $id = (int)$id;
    $width = (int)$width;
    $height = (int)$height;

    $sourceFile = "{$this->_dir}/{$id}.jpg";

    if ($id > 0 && $width > 0 && $height > 0 && file_exists($sourceFile)) {

      if (file_exists("{$this->_cacheDir}/{$id}/{$width}x{$height}_nt.jpg")) {
        $return = "{$this->_cacheDirWeb}/{$id}/{$width}x{$height}_nt.jpg";
      } else {
        if (file_exists("{$this->_cacheDir}/{$id}") || (mkdir("{$this->_cacheDir}/{$id}") && chmod("{$this->_cacheDir}/{$id}", 0777))) {
          $size = getimagesize($sourceFile);
          if ($size && $size['mime']) {
            list($tmp, $mime) = explode('/', $size['mime']);
            if ($mime) {
              $functionName = "imagecreatefrom{$mime}";
              if (function_exists($functionName)) {
                $x_ratio = $width / $size[0];
                $y_ratio = $height / $size[1];

                $ratio       = min($x_ratio, $y_ratio);
                $use_x_ratio = ($x_ratio == $ratio);

                if($size[0]>$width || $size[1]>$height)
                {
                  $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
                  $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
                  $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
                  $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
                }
                else
                {
                  $new_width   = $size[0];
                  $new_height  = $size[1];
                  $new_left    = floor(($width - $size[0]) / 2);
                  $new_top     = floor(($height - $size[1]) / 2);
                }
                $im = $functionName($sourceFile);
                $im1 = imagecreatetruecolor($width, $height);
                imagefill($im1, 0, 0, 0xffffff);
                imagecopyresampled($im1, $im, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
                imagejpeg($im1, "{$this->_cacheDir}/{$id}/{$width}x{$height}_nt.jpg", 90);
                chmod("{$this->_cacheDir}/{$id}/{$width}x{$height}_nt.jpg", 0777);
                imagedestroy($im);
                imagedestroy($im1);
                $return = "{$this->_cacheDirWeb}/{$id}/{$width}x{$height}_nt.jpg";
              }
            }
          }
        }
      }

    }

    return $return ? $return : "/img/no_photo.jpg";

  }


  public function GetPathToTrimmedThumb($id, $width, $height) {

    $return = false;

    $id = (int)$id;
    $width = (int)$width;
    $height = (int)$height;

    $sourceFile = "{$this->_dir}/{$id}.jpg";

    if ($id > 0 && $width > 0 && $height > 0 && file_exists($sourceFile)) {

      if (file_exists("{$this->_cacheDir}/{$id}/{$width}x{$height}_t.jpg")) {
        $return = "{$this->_cacheDirWeb}/{$id}/{$width}x{$height}_t.jpg";
      } else {
        if (file_exists("{$this->_cacheDir}/{$id}") || (mkdir("{$this->_cacheDir}/{$id}") && chmod("{$this->_cacheDir}/{$id}", 0777))) {
          $size = getimagesize($sourceFile);
          if ($size && $size['mime']) {
            list($tmp, $mime) = explode('/', $size['mime']);
            if ($mime) {
              $functionName = "imagecreatefrom{$mime}";
              if (function_exists($functionName)) {
                $x_ratio = $width / $size[0];
                $y_ratio = $height / $size[1];

                $ratio = max($x_ratio, $y_ratio);
                $use_x_ratio = ($x_ratio == $ratio);

                $src_w = floor($width / $ratio);
                $src_h = floor($height / $ratio);

                if ($use_x_ratio) {
                  $src_x = 0;
                  $src_y = floor(($size[1] - $src_h) / 2);
                } else {
                  $src_x = floor(($size[0] - $src_w) / 2);
                  $src_y = 0;
                }


                $im = $functionName($sourceFile);
                $im1 = imagecreatetruecolor($width, $height);
                imagefill($im1, 0, 0, 0xffffff);
                imagecopyresampled($im1, $im, 0, 0, $src_x, $src_y, $width, $height, $src_w, $src_h);
                imagejpeg($im1, "{$this->_cacheDir}/{$id}/{$width}x{$height}_t.jpg", 90);
                chmod("{$this->_cacheDir}/{$id}/{$width}x{$height}_t.jpg", 0777);
                imagedestroy($im);
                imagedestroy($im1);
                $return = "{$this->_cacheDirWeb}/{$id}/{$width}x{$height}_t.jpg";
              }
            }
          }
        }
      }

    }

    return $return ? $return : "/img/no_photo.jpg";

  }


  public function Delete($id) {
    $id = (int)$id;
    if (!$id) {
      return false;
    }
    $this->ClearCache($id);
    $imgFile = $this->_dir . '/' . $id . '.jpg';
    if (file_exists($imgFile)) {
      unlink($imgFile);
    }
    return true;
  }


}