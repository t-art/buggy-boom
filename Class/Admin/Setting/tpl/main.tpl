<h3 class="acenter">Настройки</h3>
<form action="./index.php?request=setting/save" method="post" enctype="multipart/form-data">
<table align="center" cellspacing="0" cellpadding="0" class="list">
  <?php
  $i = 0;
  foreach ($data['settings'] as $setting) {
    ++$i;
    ?>
    <tr class="<?=($i%2 == 0) ? 'odd' : 'even'?>" <?=$setting['type'] == 'ta' ? 'style="vertical-align:top;"' : ''?>>
      <td class="aright"><?=$setting['name']?></td>
      <td>
        <?php
        if ($setting['type'] == 'file') {
          ?>
          <input type="file" name="file[<?=$setting['id']?>]" style="width:400px;">
          <?php
        } elseif ($setting['type'] == 'ta') {
          ?>
          <textarea name="<?=$setting['id']?>" rows="5" style="width:400px;"><?=htmlspecialchars($setting['value'], ENT_QUOTES)?></textarea>
          <?php
        } else {
          ?>
          <input type="text" name="<?=$setting['id']?>" value="<?=htmlspecialchars($setting['value'], ENT_QUOTES)?>" style="width:400px;">
          <?php
        }
        ?>
      </td>
    </tr>
    <?php
  }
  ?>
</table>
<div class="acenter" style="margin-top:10px;">
  <input type="submit" value="Сохранить" class="button">
</div>
</form>