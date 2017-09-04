<h3 class="acenter">Рубрики</h3>
<form id="frmList" action="./index.php?request=<?=$this->_commonObj->_objectName?>/save_bulk" method="post">
  <div class="aright" style="margin-bottom:5px;">
    <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/edit&id=0&parent_id=<?=$data['current_category']?>" title="Добавить"><img src="/img/admin/add32.png"></a>
    &nbsp;
    <a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a>
  </div>
<table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">
  <tr>
    <th width="120">&nbsp;</th>
    <th style="text-align: left;">Название</th>
    <th width="100">Порядок</th>
<!--    <th width="100">Подбор включен</th>-->
<!--    <th width="100">Размеры в подборе</th>-->
<!--    <th width="100">Бренды в подборе</th>-->
    <th width="100">Скрыто</th>
    <th width="100">Действия</th>
  </tr>
  <?php
  $i = 0;
  foreach ($data['items'] as $item) {
    ++$i;
    if ($item['name'] == 'Вверх') {
      ?>
      <tr class="<?=($i%2 == 0) ? 'odd' : 'even'?> acenter">
        <td class="aleft" colspan="8" style="font-weight:bold;"><?=$item['parent_name']?> &nbsp;&nbsp;&nbsp; <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/list&id=<?=$item['id']?>"><?=$item['name']?></a></td>
      </tr>
      <?php
      continue;
    }
    ?>
    <tr class="<?=($i%2 == 0) ? 'odd' : 'even'?> acenter">
      <td><img src="<?=$this->_commonObj->GetPrimaryImagePath($item['id'], 100, 100)?>" width="100"><input type="hidden" name="item[<?=$item['id']?>]" value="<?=$item['id']?>"></td>
      <?php
      if (!$item['has_childs']) {
        ?>
        <td class="aleft"><?=$item['name']?> (<a href="./index.php?request=product/list&filter_category=<?=$item['id']?>"><?=$item['products']?></a>)</td>
        <?php
      } else {
        ?>
        <td class="aleft"><a href="./index.php?request=<?=$this->_commonObj->_objectName?>/list&id=<?=$item['id']?>"><?=$item['name']?></a> (<a href="./index.php?request=product/list&filter_category=<?=$item['id']?>"><?=$item['products']?></a>)</td>
      <?php
      }
      ?>
      <td><input type="text" name="sort[<?=$item['id']?>]" value="<?=$item['sort'] ? $item['sort'] : ''?>" style="width:50px;text-align:right;"></td>
<!--      <td><input type="checkbox" name="selection_on[<?=$item['id']?>]" value="1" <?=$item['selection_on'] ? 'checked' : ''?>></td> -->
<!--      <td><input type="checkbox" name="selection_sizes[<?=$item['id']?>]" value="1" <?=$item['selection_sizes'] ? 'checked' : ''?>></td> -->
<!--      <td><input type="checkbox" name="selection_brands[<?=$item['id']?>]" value="1" <?=$item['selection_brands'] ? 'checked' : ''?>></td> -->
      <td><input type="checkbox" name="hide[<?=$item['id']?>]" value="1" <?=$item['hide'] ? 'checked' : ''?>></td>
      <td>
        <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/edit&id=<?=$item['id']?>" title="Редактировать"><img src="/img/admin/info16.png"></a>
        &nbsp;
        <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/delete&id=<?=$item['id']?>" title="Удалить" onclick="return sure()"><img src="/img/admin/delete16.png"></a>
      </td>
    </tr>
    <?php
  }
  ?>
</table>
  <?php
  if (count($data['items']) > 10) {
    ?>
    <div class="aright" style="margin-top:5px;">
      <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/edit&id=0&parent_id=<?=$data['current_category']?>" title="Добавить"><img src="/img/admin/add32.png"></a>
      &nbsp;
      <a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a>
    </div>
  <?
  }
  ?>
</form>

<script type="text/javascript">
  function sure() {
    return confirm('Уверены?');
  }

  function submitForm() {
    if (sure()) {
      $('#frmList').submit();
    }
  }
</script>