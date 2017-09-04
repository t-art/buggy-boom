<div class="product_page" itemscope itemtype="http://schema.org/Offer">
	<?php  include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';  ?>
	<div class="breadcrumbs"> <a href="/">Главная</a>
		<?=$data['breadcrumbs']?>
		<div class="item">
			<?=$data['name']?>
		</div>
	</div>
	<h1 itemprop="name">
		<?=$data['name']?>
	</h1>
	<?php  if ($data['primary_image']) {    ?>
	<div class="image_primary"> <a class="fancybox" rel="gallery" href="<?=$data['primary_image']['path_big']?>"><img src="<?=$data['primary_image']['path_middle']?>" itemprop="image" width="320" height="270" alt=""></a> </div>
	<?php  } else {    ?>
	<div class="image_primary"> <img src="/img/no_photo.jpg" width="320" height="270" alt=""> </div>
	<?php  }?>
	<?if ($data['features']) {?>
		<div class="item" itemprop="description" style="float: left; width:33%;">
			<div class="caption">Характеристики товара:</div>
			<div class="features" style="width:100%;">
				<table style="width:100%;">
					<?php
					$num=0;
					foreach ($data['features'] as $featureID => $featureData) {
						$num++;
						?>
						<tr style="<?=$num%2==0?'background:#f0f0f0;':''?>">
							<td style="padding:2px;"><?=$featureData['feature_name']?></td>
							<td style='padding:2px;padding-left:30px;'><?=$featureData['value_id'] ? $featureData['value_name'] : $featureData['value_manual']?></td>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
		</div>
	<?}?>
	<div style="float: left; margin-left: 30px;">
		<?if($data['brand_name'])
		{	?>
			<div class="item">
				<div class="caption">Производитель</div>
				<?php echo $data['brand_name']?>
			</div>
	<?	}
		if ($data['article']) {    ?>
		<div class="item">
			<div class="caption">Артикул</div>
			<?php echo $data['article']?> </div>
		<div class="item">
			<div class="caption">Цена</div>
			<div style="color:#0390C8; font:bold 18px Arial;"><?php echo $data['price']?> руб.</div>
			<meta itemprop="price" content="<?=$data['price']?>">
		   <meta itemprop="priceCurrency" content="RUB">
		</div>
		<?php  }  ?>
		<div class="item">
			<input type="text" class="spinner" value="1" id="quant<?=$data['id']?>" readonly>
			<input type="button" class="btn" id="bc<?=$data['id']?>" onclick="cartAdd(<?=$data['id']?>)" value="добавить в корзину">
		</div>
	</div>
	<div class="fboth"></div>

	<?php  if (trim(strip_tags($data['full_descr'])) != '') {    ?>
	<div class="item">
		<div class="caption">Описание</div>
		<?php echo $data['full_descr']?> 
	</div>
	<?php  }  if (false && count($data['products_recommend']) > 0) {    ?>
	<div style="font-size: 16px;font-weight: bold;margin: 10px 0 10px 0;">Рекомендуем</div>
	<table class="category_products">
		<tr>
			<th>Марка</th>
			<th style="width:90px;">Цена</th>
			<th style="width:70px;">Кол-во</th>
			<th style="width:140px;">&nbsp;</th>
		</tr>
		<?php      foreach ($data['products_recommend'] as $productID => $productData) {        ?>
		<tr>
			<td><a href="/<?=$productData['url']?>.html">
				<?=$productData['name']?>
				<?=$productData['article']?>
				</a></td>
			<td><?=$productData['price']?></td>
			<td><input type="text" class="spinner" value="1" id="quant<?=$productID?>" readonly></td>
			<td><input type="button" class="btn" id="bc<?=$productID?>" onclick="cartAdd(<?=$productID?>)" value="добавить в корзину"></td>
		</tr>
		<?php      }      ?>
	</table>
	<?php  }  ?>
</div>
