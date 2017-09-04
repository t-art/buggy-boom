<div class="product_page">
  <?php
  include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';
  ?>
  <div class="breadcrumbs">
    <a href="/">Главная</a> <?=$data['breadcrumbs']?> <div class="item"><?=$data['name']?></div>
  </div>

  <h1><?=$data['name']?></h1>

  <div class="block1_text" style="padding-top: 0;">
    <div class="fboth">
      <div class="image_primary">
        <a class="fancybox" rel="gallery" href="<?=$data['primary_image']['path_big']?>"><img src="<?=$data['primary_image']['path_middle']?>" width="320" height="270" alt=""></a>
      </div>
      <?php
      if ($data['images']) {
        ?>
        <div class="images_secondary">
          <?php
//          if (count($data['images']) > 3) {
            ?>
            <div class="pointer" style="height:30px;text-align:center;">
              <a class="prev"><img src="/img/arr_up.gif" width="12" height="30" alt=""></a>
            </div>
          <?php
//          }
          ?>
          <div class="border">
            <div class="scrollable" id="scrollable">
              <div class="items">
                <?php
                $first = true;
                foreach ($data['images'] as $imageData) {
                  ?>
                  <div class="item <?=$first ? 'first' : ''?>">
                    <a class="fancybox" rel="gallery" href="<?=$imageData['path_big']?>"><img src="<?=$imageData['path_small']?>" width="90" height="83" alt=""></a>
                  </div>
                <?php
                  $first = false;
                }
                ?>
              </div>
            </div>
          </div>
          <?php
//          if (count($data['images']) > 3) {
            ?>
            <div class="pointer" style="height:30px;text-align: center;">
              <a class="next"><img src="/img/arr_down.gif" width="12" height="30" alt=""></a>
            </div>
          <?php
//          }
          ?>
        </div>
      <?php
      }
      ?>
      <div class="fleft" style="width:200px;">
        <div class="article">Артикул: <?=$data['article']?></div>
        <?php
        if ($data['brand_name']) {
          ?>
          <div class="brand">
            Бренд:
            <div class="name"><?=$data['brand_name']?></div>
            <?php
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/img/brand/{$data['brand_id']}_flag.jpg")) {
              ?>
              <div class="flag"><img src="/img/brand/<?=$data['brand_id']?>_flag.jpg" width="20" alt=""></div>
              <?php
            }
            if ($data['brand_country'] != '') {
              ?>
              <div class="country"><?=$data['brand_country']?></div>
              <?
            }
            ?>
          </div>
          <?php
        }
        ?>
        <?php
        if ($data['brand_attention']) {
          ?>
          <div class="brand_attention">
            <?php
            if (!$data['brand_attention_url']) {
              ?>
              <?=$data['brand_attention']?>
              <?php
            } else {
              ?>
              <a href="<?=$data['brand_attention_url']?>" target="_blank"><?=$data['brand_attention']?></a>
              <?php
            }
            ?>
          </div>
          <?php
        }
        ?>
        <div class="features">
          <?php
          foreach ($data['features'] as $featureID => $featureData) {
            ?>
            <div><?=$featureData['feature_name']?>: <span><?=$featureData['value_id'] ? $featureData['value_name'] : $featureData['value_manual']?></span></div>
          <?php
          }
          ?>
        </div>
        <div class="availability_price fboth">
          <div class="availability"><?=$data['quantity'] > 0 ? 'есть' : 'нет'?> в наличии</div>
          <div class="price"><?=Class_Shared::GetDealerPrice($data['price'])?> руб.</div>
        </div>
        <div class="cart fboth">
          <div class="quantity"><input type="text" class="spinner" value="1" id="quant<?=$data['id']?>"></div>
          <div class="cart_add"><input type="button" value="<?=$data['quantity'] > 0 ? 'купить' : 'заказать'?>" class="button<?=$data['quantity'] <= 0 ? '_grey' : ''?>" onclick="cartAdd(<?=$data['id']?>)" id="btnCartAdd<?=$data['id']?>"></div>
        </div>
      </div>
    </div>
    <div class="full_descr">
      <div class="caption">Описание товара</div>
      <div><?=$data['full_descr']?></div>
    </div>
  </div>

  <?php
  if ($data['quantity'] > 0 && count($data['complects']) > 0) {
    ?>
    <div class="caption2"><div>Комплекты</div></div>
    <div class="block1">
      <div class="additionals fboth">
        <?php
        $curProduct = 1;
        foreach ($data['complects'] as $productID => $productData) {
          $productData['cur'] = $curProduct;
          $productData['exclude'] = array('article' => 'article', 'features' => 'features', 'compare' => 'compare');
          echo $this->_renderTemplate('common', 'product_item', $productData);
          ++$curProduct;
        }
        ?>
      </div>
    </div>
  <?php
  }
  if ($data['quantity'] <= 0 && count($data['similars']) > 0) {
    ?>
    <div class="caption2"><div>Аналогичные товары</div></div>
    <div class="block1">
      <div class="additionals fboth">
        <?php
        $curProduct = 1;
        foreach ($data['similars'] as $productID => $productData) {
          $productData['cur'] = $curProduct;
          $productData['exclude'] = array('article' => 'article', 'features' => 'features', 'compare' => 'compare');
          echo $this->_renderTemplate('common', 'product_item', $productData);
          ++$curProduct;
        }
        ?>
      </div>
    </div>
  <?php
  }
  if (count($data['additionals']) > 0) {
    ?>
    <div class="caption2"><div>Сопутствующие товары</div></div>
    <div class="block1">
      <div class="additionals fboth">
        <?php
        $curProduct = 1;
        foreach ($data['additionals'] as $productID => $productData) {
          $productData['cur'] = $curProduct;
          $productData['exclude'] = array('article' => 'article', 'features' => 'features', 'compare' => 'compare');
          echo $this->_renderTemplate('common', 'product_item', $productData);
          ++$curProduct;
        }
        ?>
      </div>
    </div>
    <?php
  }
  ?>
</div>