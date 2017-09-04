<h3 class="acenter"><?=$data['id'] ? 'Редактирование' : 'Добавление'?> текстовой страницы</h3>
<form id="frmEdit" action="./index.php?request=<?=$this->_commonObj->_objectName?>/save&id=<?=$data['id']?>" method="post">
  <input type="hidden" id="doClose" name="do_close" value="0">
  <table class="edit" cellspacing="0" cellpadding="0" align="center">
    <tr class="even">
      <th>Название:</th>
      <td><input type="text" name="name" value="<?=htmlspecialchars($data['name'], ENT_QUOTES)?>" <?php if (!$data['id']) { ?>onkeyup="$('#url').val(toTranslit(this.value))"<?php } ?>></td>
    </tr>
    <tr class="odd" style="display: none;">
      <th>Родитель:</th>
      <td>
        <select name="parent_id">
          <option value="0"></option>
          <?=implode('', $this->_commonObj->GetChildOptionsList(0, $data['parent_id'], array($data['id'])))?>
        </select>
      </td>
    </tr>
    <tr class="odd">
      <th>URL:</th>
      <td><input type="text" name="url" id="url" value="<?=htmlspecialchars($data['url'], ENT_QUOTES)?>"></td>
    </tr>
    <tr class="odd">
      <th>Внешний URL:</th>
      <td><input type="text" name="external_url" value="<?=htmlspecialchars($data['external_url'], ENT_QUOTES)?>"></td>
    </tr>
    <tr class="even">
      <th class="atop">Текст:</th>
      <td style="width:740px;">
        <textarea id="ckeditor_full_descr" name="full_descr"><?=htmlspecialchars($data['full_descr'], ENT_QUOTES)?></textarea>
      </td>
    </tr>
    <tr class="odd">
      <th class="atop">Отображать в шапке:</th>
      <td>
        <label><input type="checkbox" name="in_header" value="1" <?=$data['in_header'] ? 'checked' : ''?> class="cb"></label><br>
      </td>
    </tr>
    <tr class="even">
      <th>Скрыть:</th>
      <td><input type="checkbox" name="hide" value="1" <?=$data['hide'] ? 'checked' : ''?> class="cb"></td>
    </tr>
    <tr class="odd">
      <th>Title:</th>
      <td><input type="text" name="meta_title" value="<?=htmlspecialchars($data['meta_title'], ENT_QUOTES)?>"></td>
    </tr>
    <tr class="even">
      <th>Keywords:</th>
      <td><input type="text" name="meta_keywords" value="<?=htmlspecialchars($data['meta_keywords'], ENT_QUOTES)?>"></td>
    </tr>
    <tr class="odd">
      <th>Description:</th>
      <td><input type="text" name="meta_description" value="<?=htmlspecialchars($data['meta_description'], ENT_QUOTES)?>"></td>
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
  CKEDITOR.replace( 'ckeditor_full_descr' );
  if ($('#ckeditor_lower_text').prop('id') != undefined) {
    CKEDITOR.replace( 'ckeditor_lower_text' );
  }

  function frmEditSubmit(doClose) {
    $('#doClose').val(doClose);
    $('#frmEdit').submit();
  }

</script>