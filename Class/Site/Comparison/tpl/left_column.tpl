<div class="comparison">
  <div class="caption2"><div>Сравнение товаров</div></div>
  <div class="block1" style="padding:0 0 10px 0;margin-bottom: 20px;">
    <?php
    foreach ($data['comparison'] as $comparisonID => $comparisonData) {
      ?>
      <div class="item fboth">
        <div class="delete"><a href="javascript:compareDelete(<?=$comparisonID?>)"><img src="/img/im1.gif" width="12" height="12"></a></div>
        <div class="name"><a href="/<?=$comparisonData['url']?>.html"><?=$comparisonData['name']?></a> &ndash; <?=$comparisonData['price']?> руб.</div>
      </div>
    <?php
    }
    ?>
    <input type="button" value="сравнить" class="button_grey" onclick="document.location.href='comparison.php'">
  </div>
</div>
