<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';
?>
<div class="breadcrumbs" style="margin-left: 10px;">
  <a href="/">Главная</a> <div class="item"><?=$data['name']?></div>
</div>
<h1 style="margin-left: 10px;"><div><?=$data['name']?></div></h1>
<div class="block1_text cart_info">
  <?php
  if (count($data['items']) > 0) {
    ?>
    <form id="frmCart" action="/cart.php" method="post">
    <table class="cart_products" style="width:100%;">
      <tr>
        <th></th>
        <th>Наименование товара</th>
        <th style="width:80px;">Кол-во</th>
        <th style="width:80px;">Цена</th>
        <th style="width:90px;">Стоимость</th>
      </tr>
    <?php
    foreach ($data['items'] as $productID => $productData) {
      ?>
      <tr>
        <td></td>
        <td>
          <a href="/<?=$productData['url']?>.html"><?=$productData['name']?> <?=$productData['article']?></a>
        </td>
        <td>
          <?=$productData['quantity']?>
        </td>
        <td>
          <?=number_format($productData['price'], 0, ',', ' ')?> руб.
        </td>
        <td>
          <span><?=number_format($productData['amount'], 0, ',', ' ')?></span> руб.
        </td>
      </tr>
      <?php
    }
    ?>
      <tr>
        <td></td>
        <td colspan="2" style="font-weight:bold;">Итого:</td>
        <td colspan="2" style="font-weight:bold;">
          <?=number_format($data['totals']['quantity'], 0, ',', ' ')?> <?=$data['totals']['unit']?><br>
          на сумму <?=number_format($data['totals']['amount'], 0, ',', ' ')?> руб.
        </td>
      </tr>
    </table>
    </form>

<div style="height:40px;"></div>

    <div class="order_info" style="margin-left: 10px">
      <div class="cap1">1. Способ оплаты</div>
      <div class="variants">
        <div class="item"><label><input type="radio" name="payment_type" value="cash" checked>Оплата наличными</label></div>
        <div class="item"><label><input type="radio" name="payment_type" value="bank">Безналичный расчет</label></div>
      </div>
      <div class="cap1">2. Способ доставки</div>
      <div class="variants">
        <div class="item"><label><input type="radio" name="delivery_type" value="pickup" checked onclick="$('#delivery_address').slideUp()">Самовывоз</label></div>
        <div class="item"><label><input type="radio" name="delivery_type" value="courier" onclick="$('#delivery_address').slideDown()">Курьером (Москва и Московская область)</label></div>
        <div class="item"><label><input type="radio" name="delivery_type" value="transport_company" onclick="$('#delivery_address').slideDown()">Транспортной компанией</label></div>
      </div>
      <div id="delivery_address" style="display:none;">
        <div class="cap2">Адрес доставки</div>
        <div class="fboth">
          <div class="fleft"><input class="fancy" type="text" id="delivery_address_index" style="width:90px;" value="Индекс" onfocus="if(this.value == 'Индекс') this.value = ''" onblur="if(this.value == '') this.value = 'Индекс'"></div>
          <div class="fleft"><input class="fancy" type="text" id="delivery_address_city" style="width:200px;" value="Населенный пункт" onfocus="if(this.value == 'Населенный пункт') this.value = ''" onblur="if(this.value == '') this.value = 'Населенный пункт'"></div>
        </div>
        <div class="fboth">
          <div class="fleft"><input class="fancy" type="text" id="delivery_address_street" style="width:310px;" value="Улица" onfocus="if(this.value == 'Улица') this.value = ''" onblur="if(this.value == '') this.value = 'Улица'"></div>
        </div>
        <div class="fboth">
          <div class="fleft"><input class="fancy" type="text" id="delivery_address_home" style="width:90px;" value="Дом" onfocus="if(this.value == 'Дом') this.value = ''" onblur="if(this.value == '') this.value = 'Дом'"></div>
          <div class="fleft"><input class="fancy" type="text" id="delivery_address_building" style="width:90px;" value="Корпус" onfocus="if(this.value == 'Корпус') this.value = ''" onblur="if(this.value == '') this.value = 'Корпус'"></div>
          <div class="fleft"><input class="fancy" type="text" id="delivery_address_flat" style="width:90px;" value="Квартира" onfocus="if(this.value == 'Квартира') this.value = ''" onblur="if(this.value == '') this.value = 'Квартира'"></div>
        </div>
        <div class="cb"></div>
      </div>
      <div class="cap1" style="margin-top: 20px;">3. Личные данные</div>
      <div class="variants">
        <div class="item"><label><input type="radio" name="client_type" value="phys" checked onclick="$('#jur_data').hide()">Физическое лицо</label></div>
        <div class="item"><label><input type="radio" name="client_type" value="jur" onclick="$('#jur_data').show()">Юридическое лицо</label></div>
      </div>
      <div class="fboth">
        <div class="fleft"><input class="fancy" type="text" id="fio" style="width:275px;" value="<?=$data['fio'] ? htmlspecialchars($data['fio'], ENT_QUOTES) : 'Ф.И.О.*'?>" onfocus="if(this.value == 'Ф.И.О.*') this.value = ''" onblur="if(this.value == '') this.value = 'Ф.И.О.*'"></div>
        <div class="fleft"><input class="fancy" type="text" id="email" style="width:195px;" value="<?=$data['email'] ? htmlspecialchars($data['email'], ENT_QUOTES) : 'e-mail*'?>" onfocus="if(this.value == 'e-mail*') this.value = ''" onblur="if(this.value == '') this.value = 'e-mail*'"></div>
        <div class="fleft" style="margin-right:0;"><input class="fancy" type="text" id="phone" style="width:163px;" value="<?=$data['phone'] ? htmlspecialchars($data['phone'], ENT_QUOTES) : 'Телефон*'?>" onfocus="if(this.value == 'Телефон*') this.value = ''" onblur="if(this.value == '') this.value = 'Телефон*'"></div>
      </div>
      <div id="jur_data" style="display:none;">
        <div class="fboth">
          <div class="fleft" style="margin-right:0;"><input class="fancy" type="text" id="jur_name" style="width:673px;" value="<?=$data['jur_name'] ? htmlspecialchars($data['jur_name'], ENT_QUOTES) : 'Название компании'?>" onfocus="if(this.value == 'Название компании') this.value = ''" onblur="if(this.value == '') this.value = 'Название компании'"></div>
        </div>
        <div class="fboth">
          <div class="fleft"><textarea class="fancy" id="jur_details" style="width:673px;height:60px;" onfocus="if(this.value == 'Реквизиты компании') this.value = ''" onblur="if(this.value == '') this.value = 'Реквизиты компании'"><?=$data['jur_details'] ? htmlspecialchars($data['jur_details'], ENT_QUOTES) : 'Реквизиты компании'?></textarea></div>
        </div>
      </div>
      <div class="fboth">
        <div class="fleft"><textarea class="fancy" id="comment" style="width:673px;height:120px;" onfocus="if(this.value == 'Комментарии к заказу') this.value = ''" onblur="if(this.value == '') this.value = 'Комментарии к заказу'">Комментарии к заказу</textarea></div>
      </div>
		<div style="clear:both;"></div>
      <div style="margin-top: 20px;"><input type="button" value="оформить заказ" class="btn" onclick="orderPost()"></div>
    </div>
    <?php
  } else {
    ?>
    <div class="empty">Корзина пуста</div>
    <?php
  }
  ?>
</div>

