<h3 class="acenter"><?=$data['id'] ? 'Редактирование' : 'Добавление'?> характеристики</h3>
<form id="frmEdit" action="./index.php?request=<?=$this->_commonObj->_objectName?>/save&id=<?=$data['id']?>" method="post">
  <input type="hidden" id="doClose" name="do_close" value="0">
  <table class="edit" cellspacing="0" cellpadding="0" align="center">
    <tr class="even">
      <th>Название:</th>
      <td><input type="text" name="name" value="<?=htmlspecialchars($data['name'], ENT_QUOTES)?>"></td>
    </tr>
    <tr class="odd">
      <th class="atop">Тип:</th>
      <td>
        <label><input type="radio" name="type" value="range" <?=$data['type'] == 'range' ? 'checked' : ''?> class="cb"> Диапазон</label>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="type" value="list" <?=$data['type'] == 'list' ? 'checked' : ''?> class="cb"> Список</label>
      </td>
    </tr>
    <tr class="odd">
      <th>В листинг:</th>
      <td><input type="checkbox" name="in_listing" value="1" <?= $data['in_listing'] ? 'checked' : '' ?> class="cb"></td>
    </tr>    
    <tr>
      <td class="noborder"></td>
      <td class="noborder aright">
        <a href="javascript:void(0);" onclick="frmEditSubmit(0)" title="Сохранить"><img src="/img/admin/save32.png"></a>
        &nbsp;
        <a href="javascript:void(0);" onclick="frmEditSubmit(1)" title="Сохранить и закрыть"><img src="/img/admin/ok32.png"></a>
      </td>
    </tr>
  </table>
</form>

<script type="text/javascript">

  function frmEditSubmit(doClose) {
    $('#doClose').val(doClose);
    $('#frmEdit').submit();
  }

</script>