<h3 class="acenter">Характеристики</h3>
		<table>
			<tr valign="top">
				<!--td>
                  <div style="margin: 40px 0px;" id="log_info">
                    <?
                      $lg=mysql_query("select * from teksvet_log_import order by id DESC");
                      while ($row_lg=mysql_fetch_array($lg))
                      {
                        echo $row_lg['log_import'].'&nbsp;&nbsp;<a href="import.php?id_log='.$row_lg['id'].'&action=bak" target="iframe">откатить изменения</a><br>';
                        echo '<hr>';
                      }
                    ?>
                  </div>
                </td-->
                <td style="padding-left:40px;">
                	<form action="import.php?action=import_goods" target="iframe" method="post" enctype="multipart/form-data">
						<table class="content" width="580">
							<tr>
								<th><span class="la16" style="background-position:0 -448px;"></span> Импортировать характеристики</th>
							</tr>
                            
							<tr>
								<td>
									<table class="content" width="100%">
										<tr>
											<td class="fwn">Файл xlsx</td>
											<td><input type="file" name="file_import_goods"></td>
                                            <td>(поля: ID, артикул, урл, ID категории. Загрузка со 2-ой строки)</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<th style="text-align:right;"><input type="submit" value="Загрузить" /></th>
							</tr>                            
                            
                         </table>
                      </form>                  
                
				</td>
			</tr>
		</table>  <br />
  
<form id="frmList" action="./index.php?request=<?=$this->_commonObj->_objectName?>/save_bulk" method="post">
  <div class="aright" style="margin-bottom:5px;">
    <a href="./index.php?request=<?=$this->_commonObj->_objectName?>/edit&id=0" title="Добавить"><img src="/img/admin/add32.png"></a>
    &nbsp;
    <a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a>
  </div>
<table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">
  <tr>
    <th style="text-align: left;">Название</th>
    <th width="100">Тип</th>
    <th width="100">Порядок</th>
    <th width="100">Листинг</th>
    <th width="100">Действия</th>
  </tr>
  <?php
  $i = 0;
  foreach ($data['items'] as $item) {
    ++$i;
    ?>
    <tr class="<?=($i%2 == 0) ? 'odd' : 'even'?> acenter">
      <td class="aleft"><?=$item['name']?><input type="hidden" name="item[<?=$item['id']?>]" value="<?=$item['id']?>"></td>
      <td><?=$item['type'] == 'range' ? 'Диапазон' : 'Список'?></td>
      <td><input type="text" name="sort[<?=$item['id']?>]" value="<?=$item['sort'] ? $item['sort'] : ''?>" style="width:50px;text-align:right;"></td>
      <td><input type="checkbox" name="in_listing[<?=$item['id']?>]" value="1" <?=$item['in_listing'] ? 'checked' : ''?>></td>
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
      <!--a href="javascript:submitForm()" title="Сохранить"><img src="/img/admin/save32.png"></a-->
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