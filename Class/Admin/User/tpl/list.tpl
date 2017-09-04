<h3 class="acenter">Дилеры</h3>

<table cellspacing="0" cellpadding="0"><tr valign="top">
  <input type="hidden" id="frCalendarDate">
  <td><div id="frCalendar"></div></td>
  <td style="padding-left: 20px;"><div id="frHours"></div></td>
</tr></table>

<form id="frmList" action="./index.php?request=<?=$this->_commonObj->_objectName?>/save_bulk" method="post">
  <div class="aright" style="margin-bottom:5px;">
    <a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a>
  </div>
<table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">
  <tr>
    <th width="100">Дата заявки</th>
    <th width="200">E-mail, пароль</th>
    <th width="200">Ф.И.О., телефон</th>
    <th class="aleft">Организация</th>
    <th class="aleft">Примечание</th>
    <th width="100">Скидка, %</th>
    <th width="100">Активен</th>
    <th width="100">Действия</th>
  </tr>
  <?php
  foreach ($data['items'] as $item) {
    if ($item['approved']) {
      $class = 'green';
    } else {
      $class = 'even';
    }
    ?>
    <tr class="<?=$class?> acenter">
      <td>
        <?=$item['datef']?>
        <input type="hidden" name="item[<?=$item['id']?>]" value="<?=$item['id']?>">
      </td>
      <td>
        <?=$item['email']?><br>
        <?=$item['password']?>
      </td>
      <td>
        <?=$item['fio']?><br>
        <?=$item['phone']?>
      </td>
      <td class="aleft">
        <?=$item['company_name']?><br>
        <?=$item['company_details']?>
      </td>
      <td class="aleft">
        <?=$item['notes']?>
      </td>
      <td><input type="text" name="discount[<?=$item['id']?>]" value="<?=$item['discount'] ? $item['discount'] : ''?>" style="width:50px;text-align:right;"></td>
      <td><input type="checkbox" name="approved[<?=$item['id']?>]" value="1" <?=$item['approved'] ? 'checked' : ''?>></td>
      <td>
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