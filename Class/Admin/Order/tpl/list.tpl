<h3 class="acenter">Заказы</h3>

<table cellspacing="0" cellpadding="0"><tr valign="top">
  <input type="hidden" id="frCalendarDate">
  <td><div id="frCalendar"></div></td>
  <td style="padding-left: 20px;"><div id="frHours"></div></td>
</tr></table>

<form id="frmList" action="./index.php?request=order/save_bulk" method="post">
  <div class="aright" style="margin-bottom:5px;">
    <a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a>
  </div>
<table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">
  <tr>
    <th width="100">Номер, дата заказа</th>
    <th class="aleft">Клиент</th>
    <th class="aleft">Доставка</th>
    <th width="150">Сумма</th>
    <th style="text-align: left;">Товары</th>
    <th class="aleft">Примечание</th>
    <th width="100">Статус</th>
  </tr>
  <?php
  $statuses = array(
    'new' => 'Новый',
    'complete' => 'Выполнен',
    'removed' => 'Удален',
  );
  foreach ($data['items'] as $item) {
    switch ($item['status']) {
      case 'complete':
        $class = 'green';
        break;
      case 'removed':
        $class = 'red';
        break;
      default:
        $class = 'even';
    }
    ?>
    <tr class="<?=$class?> acenter">
      <td>
        <strong><?=str_pad($item['id'], 6, '0', STR_PAD_LEFT)?></strong><br>
        <?=$item['datef']?>
        <input type="hidden" name="item[<?=$item['id']?>]" value="<?=$item['id']?>">
      </td>
      <td class="aleft">
        <?php
        if ($item['client_type'] == 'jur') {
          ?>
          <u>Компания</u>: <?=$item['jur_name']?><br>
          <u>Реквизиты</u>: <?=nl2br($item['jur_details'])?><br>
          <u>Контактное лицо</u>: <?=$item['fio']?><br>
          <?php
        } else {
          ?>
          <?=$item['fio']?><br>
          <?php
        }
        ?>
        тел.: <?=$item['phone']?><br>
        <?=$item['email'] ? "e-mail: <a href='mailto:{$item['email']}'>{$item['email']}</a>" : ''?>
      </td>
      <td class="aleft">
        <?php
        if ($item['delivery_type'] == 'pickup') {
          ?>
          Самовывоз
          <?php
        } else {
          ?>
          <?=$item['delivery_type'] == 'courier' ? 'Курьером' : 'Транспортной компанией'?><br>
          <?php
          $address = "";
          if ($item['delivery_address_index']) {
            $address .= "{$item['delivery_address_index']} ";
          }
          $address .= "{$item['delivery_address_city']} ";
          $address .= "ул. {$item['delivery_address_street']} ";
          $address .= "д. {$item['delivery_address_home']} ";
          if ($item['delivery_address_building']) {
            $address .= "корп. {$item['delivery_address_building']} ";
          }
          if ($item['delivery_address_flat']) {
            $address .= "кв. {$item['delivery_address_flat']} ";
          }
          ?>
          Адрес: <?=$address?>
          <?php
        }
        ?>
      </td>
      <td><?=number_format($item['amount'], 0, ',', ' ')?> р.</td>
      <td class="aleft">
        <?php
        foreach ($item['content'] as $contentRow) {
          echo $contentRow . '<br>';
        }
        ?>
      </td>
      <td class="aleft"><?=nl2br($item['note'])?></td>
      <td>
        <select name="status[<?=$item['id']?>]">
          <?php
          foreach ($statuses as $statusID => $statusName) {
            ?>
            <option value="<?=$statusID?>" <?=$statusID == $item['status'] ? 'selected' : ''?>><?=$statusName?></option>
            <?php
          }
          ?>
        </select>
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