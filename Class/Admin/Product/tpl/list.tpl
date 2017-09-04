<h3 class="acenter">Товары</h3>

<form id="frmList" action="" method="post">

    <div class="fleft"> Перенести выбранные в <select class="chosen" name="move_to"><?= $data['move_categories'] ?></select> <input

                type="button" class="button" value="Перенести"

                onclick="submitForm('./index.php?request=<?= $this->_commonObj->_objectName ?>/move_bulk')"></div>

    <div class="fright" style="margin-bottom:5px;"><a

                href="./index.php?request=<?= $this->_commonObj->_objectName ?>/edit&id=0" title="Добавить"><img

                    src="/img/admin/add32.png"></a> &nbsp; <a href="javascript:submitForm()" title="Сохранить"><img

                    src="/img/admin/save32.png"></a></div>

    <div class="fboth"></div>


    <?php

  if ($data['paginator']) {

    echo $data['paginator'];

  }

  ?>


    <table align="center" cellspacing="0" cellpadding="0" class="list" width="100%">

        <tr>

            <th width="30"></th>

            <th width="120">&nbsp;</th>

            <th style="text-align: left;">Название</th>

            <th style="text-align: left;" width="100">Артикул</th>

            <th style="text-align: left;">Рубрика</th>

            <th style="text-align: left;">Бренд</th>

            <th width="100">Цена</th>

            <!--th width="100">Наличие</th-->

            <th width="100" title="Рекомендуем">Реком.<br><input type="checkbox"

                                                                 onchange="$('input[name^=recommend]').prop('checked', $(this).is(':checked'));"/>
            </th>

            <th width="100" title="На главную">Глав.<br><input type="checkbox"
                                                               onchange="$('input[name^=is_on_index]').prop('checked', $(this).is(':checked'));"/>
            </th>

            <th width="100">Скрыто<br><input type="checkbox"

                                             onchange="$('input[name^=hide]').prop('checked', $(this).is(':checked'));"/>
            </th>

            <!--    <th width="100">Порядок</th>-->

            <th width="100">Выводить на маркет<br><input type="checkbox"

                                                         onchange="$('input[name^=export_to_market]').prop('checked', $(this).is(':checked'));"/>

            </th>

            <th width="100">Ставка маркета</th>

            <th width="100">Действия</th>

        </tr>

        <tr>

            <td class="acenter"><input type="checkbox" onclick="toggleCBs('selected[', this.checked)"></td>

            <td></td>

            <td><input type="text" name="filter_name" id="filter_name" style="width:200px;"

                       value="<?= htmlspecialchars($data['filter_name']) ?>"></td>

            <td><input type="text" name="filter_article" id="filter_article" style="width:100px;"

                       value="<?= htmlspecialchars($data['filter_article']) ?>"></td>

            <td><select class="chosen" name="filter_category" id="filter_category" style="width:350px;">

                    <option value=""></option>
                    <?= $data['filter_categories'] ?>      </select></td>

            <td><select name="filter_brand" id="filter_brand">        <?= $data['filter_brands'] ?>      </select></td>

            <td>&nbsp;</td>

            <td>&nbsp;</td>

            <td align="center"><select name="filter_on_index"

                                       id="filter_on_index">        <?= $data['filter_on_index'] ?>      </select>

            </td>

            <td><select name="filter_hide" id="filter_hide">        <?= $data['filter_hide'] ?>      </select></td>

            <td align="center"><select name="filter_export_to_market"

                                       id="filter_export_to_market">        <?= $data['filter_export_to_market'] ?>      </select>

            </td>

            <td>&nbsp;</td>

            <td><input type="button" value="Фильтр" onclick="filterList()" class="button"></td>

        </tr>
        <?php $i = 1;

    foreach ($data['items'] as $item) {

      ++$i; ?>

        <tr class="<?= ($i % 2 == 0) ? 'odd' : 'even' ?> acenter">

            <td><input type="checkbox" name="selected[<?= $item['id'] ?>]" value="<?= $item['id'] ?>"></td>

            <td>        <?php $image = $this->_commonObj->GetPrimaryImagePath($item['id'], 100, 100);

                if ($image) { ?> <img src="<?= $image ?>" width="100"><input type="hidden"

                                                                             name="item[<?= $item['id'] ?>]"

                                                                             value="<?= $item['id'] ?>">          <?php } ?>

            </td>

            <td class="aleft"><?= $item['name'] ?><input type="hidden" name="item[<?= $item['id'] ?>]"

                                                         value="<?= $item['id'] ?>"></td>

            <td class="aleft"><?= $item['article'] ?></td>

            <td class="aleft"><?= $item['category_name'] ?></td>

            <td class="aleft"><?= $item['brand_name'] ?></td>

            <td><input type="text" name="price[<?= $item['id'] ?>]" value="<?= $item['price'] ?>"

                       style="width:90px;text-align:right;"></td>

            <!--td><input type="text" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" style="width:50px;text-align:right;"></td-->

            <td><input type="checkbox" name="recommend[<?= $item['id'] ?>]"
                       value="1" <?= $item['recommend'] ? 'checked' : '' ?>>
            </td>

            <td><input type="checkbox" name="is_on_index[<?= $item['id'] ?>]"
                       value="1" <?= $item['is_on_index'] ? 'checked' : '' ?>>
            </td>

            <td><input type="checkbox" name="hide[<?= $item['id'] ?>]" value="1" <?= $item['hide'] ? 'checked' : '' ?>>
            </td>

            <!--      <td><input type="text" name="sort[<?= $item['id'] ?>]" value="<?= $item['sort'] ? $item['sort'] : '' ?>" style="width:50px;text-align:right;"></td> -->

            <td><input type="checkbox" name="export_to_market[<?= $item['id'] ?>]"

                       value="1" <?= $item['export_to_market'] ? 'checked' : '' ?>>
            </td>

            <td><input type="text" name="market_bid[<?= $item['id'] ?>]" value="<?= $item['market_bid'] ?>"

                       style="width:50px;text-align:right;"></td>

            <td><a href="./index.php?request=<?= $this->_commonObj->_objectName ?>/edit&id=<?= $item['id'] ?>"

                   title="Редактировать"><img src="/img/admin/info16.png"></a> &nbsp; <a

                        href="./index.php?request=<?= $this->_commonObj->_objectName ?>/delete&id=<?= $item['id'] ?>"

                        title="Удалить" onclick="return sure()"><img src="/img/admin/delete16.png"></a></td>

        </tr>
        <?php } ?></table>

</form>

<?php

if ($data['paginator']) {

  echo $data['paginator'];

}

if (count($data['items']) > 10) {

?>

<div class="aright" style="margin-top:5px;"><a

            href="./index.php?request=<?= $this->_commonObj->_objectName ?>/edit&id=0" title="Добавить"><img

                src="/img/admin/add32.png"></a> &nbsp; <a href="javascript:submitForm()" title="Сохранить"><img

                src="/img/admin/save32.png"></a></div>  <? } ?>

<script type="text/javascript">  function sure() {

        return confirm('Уверены?');

    }

    function submitForm(actionURL) {

        actionURL = actionURL ? actionURL : './index.php?request=<?=$this->_commonObj->_objectName?>/save_bulk';

        if (sure()) {

            $('#frmList').attr('action', actionURL).submit();

        }

    }

    function filterList() {

        var n = $('#filter_name').val();

        var a = $('#filter_article').val();

        var c = $('#filter_category').val();

        var b = $('#filter_brand').val();

        var h = $('#filter_hide').val();

        var m = $('#filter_export_to_market').val();

        var i = $('#filter_on_index').val();

        document.location.href = './index.php?request=<?=$this->_commonObj->_objectName?>/list' + '&filter_name=' + n + '&filter_article=' + a + '&filter_category=' + c + '&filter_brand=' + b + '&filter_hide=' + h + '&filter_export_to_market=' + m + '&filter_on_index=' + i;

    }

    function toggleCBs(name, flag) {

        $("[name^='" + name + "']").attr('checked', flag);

    }</script>