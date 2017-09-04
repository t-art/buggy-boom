<h3 class="acenter">Бренды</h3>
<form id="frmList" action="./index.php?request=<?=$this->_commonObj->_objectName?>/save_bulk" method="post">
  <div class="aright" style="margin-bottom:5px;">
    <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/edit&id=0" title="Добавить"><img src="/img/admin/add32.png"></a>
    &nbsp;
    <a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a>
  </div>
<table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">
  <tr>
    <th width="120">&nbsp;</th>
    <th class="aleft">Название</th>
    <th width="100">Скрыто</th>
    <th width="100">Действия</th>
  </tr>
  <?php
  $i = 0;
  foreach ($data['items'] as $item) {
    ++$i;
    ?>
    <tr class="<?=($i%2 == 0) ? 'odd' : 'even'?> acenter">
      <td>
        <?php
        $img = $this->_commonObj->GetPrimaryImagePath($item['id'], 100, 50);
        if ($img) {
          ?>
          <img src="<?=$img?>" width="100"><input type="hidden" name="item[<?= $item['id'] ?>]" value="<?= $item['id'] ?>">
          <?php
        }
        ?>
      </td>
      <td class="aleft"><?=$item['name']?><input type="hidden" name="item[<?=$item['id']?>]" value="<?=$item['id']?>"></td>
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
      <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/edit&id=0" title="Добавить"><img src="/img/admin/add32.png"></a>
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