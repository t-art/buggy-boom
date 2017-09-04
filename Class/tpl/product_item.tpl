<div class="item<?=($data['cur'] % 4 == 0) ? ' last' : ''?>">
  <div class="image"><a href="/<?=$data['url']?>.html"><img src="<?=$data['image']?>" width="150" height="120"></a></div>
  <div class="name" id="product_name_<?=$data['id']?>"><a href="/<?=$data['url']?>.html"><?=$data['name']?></a></div>
  <?php
  if (!isset($data['exclude']['article'])) {
  ?>
    <div class="article">Артикул: <?=$data['article']?></div>
  <?php
  }
  if (!isset($data['exclude']['features'])) {
  ?>
    <div class="features" id="product_feature_<?=$data['id']?>">
      <?php
      foreach ($data['features'] as $featureID => $featureData) {
        ?>
        <div><?=$featureData['feature_name']?>: <span><?=$featureData['value_id'] ? $featureData['value_name'] : $featureData['value_manual']?></span></div>
      <?php
      }
      ?>
    </div>
  <?php
  }
  ?>
  <div class="availability"><?=$data['quantity'] > 0 ? 'есть' : 'нет'?> в наличии</div>
  <div class="price"><?=Class_Shared::GetDealerPrice($data['price'])?> руб.</div>
  <div class="cart fboth">
    <div class="quantity"><input type="text" class="spinner" value="1" id="quant<?=$data['id']?>" readonly></div>
    <div class="cart_add"><input type="button" value="<?=$data['quantity'] > 0 ? 'купить' : 'заказать'?>" class="button<?=$data['quantity'] <= 0 ? '_grey' : ''?>" onclick="cartAdd(<?=$data['id']?>)" id="bc<?=$data['id']?>"></div>
  </div>
  <?php
  if (!isset($data['exclude']['compare'])) {
  ?>
    <div class="compare">
      <span class="checkbox">
        <input type="checkbox" id="comparator<?=$data['id']?>" onclick="compareAdd(<?=$data['id']?>)" <?=$data['in_comparison'] ? 'checked' : ''?>>
        <span class="check"></span>
        <label class="label">добавить к сравнению</label>
      </span>
    </div>
  <?php
  }
  ?>
</div>
<?php
if ($data['cur'] % 4 == 0) {
  ?>
  <div class="fboth"></div>
  <?php
}