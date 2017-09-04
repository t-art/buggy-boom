<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';
?>
<div class="breadcrumbs">
  <a href="/">Главная</a> <?=$data['breadcrumbs']?> <div class="item"><?=$data['name']?></div>
</div>
<h1><div><?=$data['name']?></div></h1>
<?=$data['text']?>

