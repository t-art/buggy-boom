<?php

class Class_Site_Paginator {

  public function Show($url, $page, $ppp, $total, $pagesToDisplay = 0) {
    ob_start();

    $pagesCnt = ceil($total / $ppp);

    if ($pagesCnt > 1) {
      $pageFrom = 0;
      $pageTo = $pagesCnt - 1;
      if ($pagesToDisplay > 0 && $pagesCnt > $pagesToDisplay) {
        $pageFrom = $page - ceil($pagesToDisplay / 2);
        $pageFrom = $pageFrom >= 0 ? $pageFrom : 0;
        $pageTo = $page + ceil($pagesToDisplay / 2);
        $pageTo = $pageTo < $pagesCnt ? $pageTo : $pagesCnt - 1;
        if ($pageTo - $pageFrom < $pagesToDisplay) {
          if ($pageFrom == 0) {
            $pageTo = $pageFrom + $pagesToDisplay;
            $pageTo = $pageTo < $pagesCnt ? $pageTo : $pagesCnt - 1;
          }
          if ($pageTo == $pagesCnt - 1) {
            $pageFrom = $pageTo - $pagesToDisplay;
            $pageFrom = $pageFrom >= 0 ? $pageFrom : 0;
          }
        }
      }
      ?>
      <div class="paginator">
        <?php
        if ($pageFrom > 0) {
          ?>
          <div class="arrows"><a href="<?=$url?>">&lt;&lt;</a></div>
          <div class="item">&hellip;</div>
          <?php
        }
        $first = true;
        for ($p = 0; $p < $pagesCnt; $p++) {
          if ($p >= $pageFrom && $p <= $pageTo) {
            ?>
            <div class="item<?=$p == $page ? ' current' : ''?><?=$first ? ' first' : ''?>">
              <a href="<?=$url?>&page=<?=$p?>"><?=$p+1?></a>
            </div>
            <?php
            $first = false;
          }
        }
        if ($pageTo < $pagesCnt - 1) {
          ?>
          <div class="item">&hellip;</div>
          <div class="arrows"><a href="<?=$url?>&page=<?=$pagesCnt-1?>">&gt;&gt;</a></div>
          <?php
        }
        ?>
      </div>
      <?php
    }

    return ob_get_clean();
  }

}

