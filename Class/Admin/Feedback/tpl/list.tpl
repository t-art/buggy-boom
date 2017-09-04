<h3 class="acenter">Обратная связь</h3>
<table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">
  <tr>
    <th width="100">Дата</th>
    <th width="150">Ф.И.О.</th>
    <th width="150">E-mail</th>
    <th width="150">Телефон</th>
    <th style="text-align: left;">Вопрос</th>
    <th width="100">Действия</th>
  </tr>
  <?php
  $i = 0;
  foreach ($data['items'] as $item) {
    ++$i;
    ?>
    <tr class="<?=($i%2 == 0) ? 'odd' : 'even'?> acenter">
      <td><?=$item['datef']?><input type="hidden" name="item[<?=$item['id']?>]" value="<?=$item['id']?>"></td>
      <td><?=$item['fio']?></td>
      <td><?=$item['email']?></td>
      <td><?=$item['phone']?></td>
      <td class="aleft"><?=nl2br($item['feedback'])?></td>
      <td>
        <a href="./index.php?request=feedback/delete&id=<?=$item['id']?>" title="Удалить" onclick="return sure()"><img src="/img/admin/delete16.png"></a>
      </td>
    </tr>
    <?php
  }
  ?>
</table>

<script type="text/javascript">
  function sure() {
    return confirm('Уверены?');
  }
</script>
