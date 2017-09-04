<div class="breadcrumbs">
  <a href="/">Главная</a> - <span><?=$data['name']?></span>
</div>
<h1><div><?=$data['name']?></div></h1>

<div class="category_products fboth" style="margin-top:20px;">
  <?php
  $curProduct = 1;
  foreach ($data['products'] as $productID => $productData) {
    $productData['cur'] = $curProduct;
    $productData['exclude'] = array('compare' => 'compare');
    echo $this->_renderTemplate('common', 'product_item', $productData);
    ++$curProduct;
  }
  ?>
</div>

