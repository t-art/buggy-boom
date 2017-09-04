<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';
?>
<div class="breadcrumbs" style="margin-left:10px">
  <a href="/">Главная</a> <div class="item"><?=$data['name']?></div>
</div>
<h1 style="margin-left:10px"><div><?=$data['name']?></div></h1>
<div class="block1_text cart_info">
  <?php
  if (count($data['items']) > 0) {
    ?>
    <form id="frmCart" action="/cart.php" method="post">
    <table class="cart_products" style="width:100%;">
      <tr>
        <th></th>
        <th style="width:20px;">&nbsp;</th>
        <th>Наименование товара</th>
        <th style="width:80px;">Кол-во</th>
        <th style="width:80px;">Цена</th>
        <th style="width:90px;">Стоимость</th>
      </tr>
    <?php
    foreach ($data['items'] as $productID => $productData) {
      ?>
      <tr>
        <td>
        </td>
        <td>
          <input type="checkbox" name="delete[<?=$productID?>]" value="1">
        </td>
        <td>
          <a href="/<?=$productData['url']?>.html"><?=$productData['name']?> <?=$productData['article']?></a>
        </td>
        <td>
          <input type="text" class="spinner" value="<?=$productData['quantity']?>" id="quant<?=$productID?>" readonly>
        </td>
        <td>
          <?=number_format($productData['price'], 0, ',', ' ')?> руб.
        </td>
        <td>
          <span id="cart_amount<?=$productID?>"><?=number_format($productData['amount'], 0, ',', ' ')?></span> руб.
        </td>
      </tr>
      <?php
    }
    ?>
      <tr>
        <td></td>
        <td colspan="2" style="font-weight:bold;">Итого:</td>
        <td colspan="3" style="font-weight:bold;">
          <span id="cart_total_quantity"><?=number_format($data['totals']['quantity'], 0, ',', ' ')?></span> <span id="cart_total_unit"><?=$data['totals']['unit']?></span><br>
          на сумму <span id="cart_total_amount"><?=number_format($data['totals']['amount'], 0, ',', ' ')?></span> руб.
        </td>
      </tr>
    </table>
    </form>
    <div style="float:left;margin-top: 20px; margin-left:10px;">
      <input type="button" class="btn" onclick="$('#frmCart').submit()" value="удалить отмеченные товары">
    </div>
    <div style="float:right;margin-top: 20px; margin-right:10px;">
      <input type="button" value="оформить заказ" class="btn" onclick="document.location.href='/checkout.php'">
    </div>
    <div class="cb"></div>
    <?php
  } else {
    ?>
    <div class="empty">Корзина пуста</div>
    <?php
  }
  ?>
</div>

