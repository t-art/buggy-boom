<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';
?>
<div class="breadcrumbs">
  <a href="/">Главная</a> <div class="item"><?=$data['name']?></div>
</div>
<h1><div><?=$data['name']?></div></h1>
<?php
if ($data['products']) {
  ?>
  <table class="category_products">
    <tr>
      <th>Марка</th>
      <th style="width:90px;">Цена</th>
      <th style="width:70px;">Кол-во</th>
      <th style="width:140px;">&nbsp;</th>
    </tr>
    <?php
    foreach ($data['products'] as $productID => $productData) {
      ?>
      <tr>
        <td><a href="/<?=$productData['url']?>.html"><?=$productData['name']?> <?=$productData['article']?></a></td>
        <td><?=$productData['price']?></td>
        <td><input type="text" class="spinner" value="1" id="quant<?=$productID?>" readonly></td>
        <td><input type="button" class="btn" id="bc<?=$productID?>" onclick="cartAdd(<?=$productID?>)" value="добавить в корзину"></td>
      </tr>
    <?php
    }
    ?>
  </table>
  <?php
  if ($data['paginator']) {
    echo $data['paginator'];
  }
}

