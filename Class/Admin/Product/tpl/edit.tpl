<h3 class="acenter"><?= $data['id'] ? 'Редактирование' : 'Добавление' ?> товара</h3>
<form id="frmEdit" action="./index.php?request=<?= $this->_commonObj->_objectName ?>/save&id=<?= $data['id'] ?>"
      method="post" enctype="multipart/form-data"><input type="hidden" id="doClose" name="do_close" value="0"> <input
    type="hidden" name="redirect_url"
    value="<?= htmlspecialchars(isset($this->_getParams['redirect_url']) ? $this->_getParams['redirect_url'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''), ENT_QUOTES) ?>">
  <table class="edit" cellspacing="0" cellpadding="0" align="center">
    <tr class="even">
      <th>Название:</th>
      <td><input type="text" name="name" value="<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>"
                 <?php if (!$data['id']) { ?>onkeyup="$('#url').val(toTranslit(this.value))"<?php } ?>></td>
    </tr>
    <tr class="even">
      <th>Артикул:</th>
      <td><input type="text" name="article" value="<?= htmlspecialchars($data['article'], ENT_QUOTES) ?>"></td>
    </tr>
    <tr class="odd">
      <th>URL:</th>
      <td><input type="text" name="url" id="url" value="<?= htmlspecialchars($data['url'], ENT_QUOTES) ?>"></td>
    </tr>
    <tr class="even">
      <th>Рубрика:</th>
      <td><select class="chosen" name="category_id" id="category_id" onchange="reloadFeatures()">
          <option
            value="0"></option> <?= implode('', $this->_categoryCommon->GetChildOptionsList(0, $data['category_id'])) ?>
        </select></td>
    </tr>
    <!--
    <tr class="odd">
      <th class="atop">Рубрики:</th>
      <td><?= implode('', $this->_categoryCommon->GetChildCheckboxesList(0, $data['other_categories'])) ?></td>
    </tr>
    -->
    <tr class="odd">
      <th>Бренд:</th>
      <td><select name="brand_id">          <?= $data['select_boxes']['brands'] ?>        </select></td>
    </tr>
    <!--
    <tr class="even">
      <th class="atop">Краткое описание:</th>
      <td><textarea name="short_descr" rows="5"><?= htmlspecialchars($data['short_descr'], ENT_QUOTES) ?></textarea>
      </td>
    </tr>
    -->
    <tr class="even">
      <th class="atop">Описание:</th>
      <td style="width:740px;"><textarea id="ckeditor_full_descr"
                                         name="full_descr"><?= htmlspecialchars($data['full_descr'], ENT_QUOTES) ?></textarea>
      </td>
    </tr>
    <tr class="odd">
      <th class="atop">Изображения:</th>
      <td>
        <?php $imageRow = 0;
        foreach ($data['images'] as $imageID => $imageSort) {
          ?>
          <div id="div_image_<?= $imageRow ?>" class="product_image_item"><input type="hidden"
                                                                                 name="image[<?= $imageRow ?>][<?= $imageID ?>]"
                                                                                 id="image_<?= $imageRow ?>"
                                                                                 value="<?= $imageID ?>"> <img
              src="<?= $this->_productImageCommon->GetPath($imageID, 100, 100) ?>" width="100" height="100">

            <div class="product_image_item_delete"><a href="javascript:deleteImage(<?= $imageRow ?>)"><img
                  src="/img/admin/delete16.png"></a></div>
            <div class="product_image_item_sort"><input type="text" name="image_sort[<?= $imageRow ?>][<?= $imageID ?>]"
                                                        value="<?= $imageSort ?>" title="Порядок"></div>
          </div>
          <?php ++$imageRow;
        }
        ?>
        <div class="fboth"><a href="javascript:addImage()" id="btn_add_image"><img src="/img/admin/add24.png"></a></div>
      </td>
    </tr>
    <tr class="odd">
      <th class="atop">Характеристики:</th>
      <td>
        <div id="features"></div>
      </td>
    </tr>
    <tr class="even">
      <th>Цена:</th>
      <td><input type="text" name="price"
                 value="<?= $data['price'] ? htmlspecialchars($data['price'], ENT_QUOTES) : '' ?>"></td>
    </tr>



    <!--      <tr class="odd">          <th>Остаток на складе:</th>          <td><input type="text" name="quantity" value="<?= (int)$data['quantity'] ?>"></td>        </tr>      <tr class="even">          <th>Акция:</th>          <td>            <input type="checkbox" name="is_action" id="is_action" value="1" <?= $data['is_action'] ? 'checked' : '' ?> class="cb" onclick="toggleIsAction()">            скидка: <input type="text" name="discount" id="discount" value="<?= $data['discount'] ? htmlspecialchars($data['discount'], ENT_QUOTES) : '' ?>" style="width:30px;text-align:right;">%          </td>        </tr>      <tr class="odd">          <th class="atop">Комплекты:</th>          <td class="atop">            <?php $complectRow = 0;
    foreach ($data['complect_products'] as $complectID => $complectData) { ?>              <div id="div_complect_<?= $complectRow ?>" class="product_additional_item">                <input type="hidden" name="complect[<?= $complectRow ?>][<?= $complectID ?>]" id="complect_<?= $complectRow ?>" value="<?= $complectID ?>">                <div class="product_additional_name">                  <?= $complectData['name'] ?>                </div>                <div class="product_additional_sort">                  <input type="text" name="complect_sort[<?= $complectRow ?>][<?= $complectID ?>]" value="<?= $complectData['sort'] ?>" title="Порядок">                </div>                <a href="javascript:deleteComplect(<?= $complectRow ?>)"><img src="/img/admin/delete16.png"></a>              </div>              <?php ++$complectRow;
    } ?>            <select id="complect_category_id" style="width:150px;">              <?= implode('', $this->_categoryCommon->GetChildOptionsList(0)) ?>            </select>            <a href="javascript:complectSearch()" title="Поиск"><img src="/img/admin/search16.png"></a>            <div id="complect_search_results"></div>          </td>        </tr>        <tr class="odd">          <th class="atop">Сопутствующие товары:</th>          <td class="atop">            <?php $additionalRow = 0;
    foreach ($data['additional_products'] as $additionalID => $additionalData) { ?>              <div id="div_additional_<?= $additionalRow ?>" class="product_additional_item">                <input type="hidden" name="additional[<?= $additionalRow ?>][<?= $additionalID ?>]" id="additional_<?= $additionalRow ?>" value="<?= $additionalID ?>">                <div class="product_additional_name">                  <?= $additionalData['name'] ?>                </div>                <div class="product_additional_sort">                  <input type="text" name="additional_sort[<?= $additionalRow ?>][<?= $additionalID ?>]" value="<?= $additionalData['sort'] ?>" title="Порядок">                </div>                <a href="javascript:deleteAdditional(<?= $additionalRow ?>)"><img src="/img/admin/delete16.png"></a>              </div>              <?php ++$additionalRow;
    } ?>            <select id="additional_category_id" style="width:150px;">              <?= implode('', $this->_categoryCommon->GetChildOptionsList(0)) ?>            </select>            <a href="javascript:additionalSearch()" title="Поиск"><img src="/img/admin/search16.png"></a>            <div id="additional_search_results"></div>          </td>        </tr>      <tr class="odd">          <th class="atop">Аналогичные товары:</th>          <td class="atop">            <?php $similarRow = 0;
    foreach ($data['similar_products'] as $similarID => $similarData) { ?>              <div id="div_similar_<?= $similarRow ?>" class="product_additional_item">                <input type="hidden" name="similar[<?= $similarRow ?>][<?= $similarID ?>]" id="similar_<?= $similarRow ?>" value="<?= $similarID ?>">                <div class="product_additional_name">                  <?= $similarData['name'] ?>                </div>                <div class="product_additional_sort">                  <input type="text" name="similar_sort[<?= $similarRow ?>][<?= $similarID ?>]" value="<?= $similarData['sort'] ?>" title="Порядок">                </div>                <a href="javascript:deleteSimilar(<?= $similarRow ?>)"><img src="/img/admin/delete16.png"></a>              </div>              <?php ++$similarRow;
    } ?>            <select id="similar_category_id" style="width:150px;">              <?= implode('', $this->_categoryCommon->GetChildOptionsList(0)) ?>            </select>            <a href="javascript:similarSearch()" title="Поиск"><img src="/img/admin/search16.png"></a>            <div id="similar_search_results"></div>          </td>        </tr>        <tr class="even">          <th>Новинка:</th>          <td><input type="checkbox" name="is_on_index" value="1" <?= $data['is_on_index'] ? 'checked' : '' ?> class="cb"></td>        </tr>      -->



    <tr class="odd">
      <th>Рекомендовано:</th>
      <td><input type="checkbox" name="recommend" value="1" <?= $data['recommend'] ? 'checked' : '' ?> class="cb"></td>
    </tr>
    <tr class="even">
      <th>Скрыть:</th>
      <td><input type="checkbox" name="hide" value="1" <?= $data['hide'] ? 'checked' : '' ?> class="cb"></td>
    </tr>
    <tr class="odd">
      <th>Выводить на маркет:</th>
      <td><input type="checkbox" name="export_to_market" value="1" <?= $data['export_to_market'] ? 'checked' : '' ?> class="cb"></td>
    </tr>
    <tr class="even">
      <th>Ставка маркета:</th>
      <td><input type="text" name="market_bid" value="<?= (int)$data['market_bid'] ?>"></td>
    </tr>
    <tr class="odd">
      <th>Title:</th>
      <td><input type="text" name="meta_title" value="<?= htmlspecialchars($data['meta_title'], ENT_QUOTES) ?>"></td>
    </tr>
    <tr class="even">
      <th>Keywords:</th>
      <td><input type="text" name="meta_keywords" value="<?= htmlspecialchars($data['meta_keywords'], ENT_QUOTES) ?>">
      </td>
    </tr>
    <tr class="odd">
      <th>Description:</th>
      <td><input type="text" name="meta_description" value="<?= htmlspecialchars($data['meta_description'], ENT_QUOTES) ?>"></td>
    </tr>
    <tr>
      <td class="noborder"></td>
      <td class="noborder aright"><a href="javascript:void(0);" onclick="frmEditSubmit(0)" title="Сохранить"><img
            src="/img/admin/save32.png"></a> &nbsp; <a href="javascript:void(0);" onclick="frmEditSubmit(1)"
                                                       title="Сохранить и закрыть"><img src="/img/admin/ok32.png"></a>
      </td>
    </tr>
  </table>
</form>
<script type="text/javascript">
  CKEDITOR.replace('ckeditor_full_descr');
  function frmEditSubmit(doClose) {
    $('#doClose').val(doClose);
    $('#frmEdit').submit();
  }
  function toggleIsAction() {
    if ($('#is_action').prop('checked')) {
      $('#discount').prop('disabled', false);
    } else {
      $('#discount').val('');
      $('#discount').prop('disabled', true);
    }
  }
  function deleteImage(imageID) {
    $('#image_' + imageID).val('');
    $('#div_image_' + imageID).hide();
  }
  var imageRow = <?=$imageRow?>;
  function addImage() {
    html = '<div style="margin-bottom: 5px;">';
    html += '  <input type="file" name="image[]">';
    html += '</div>';
    $('#btn_add_image').before(html);
    imageRow++;
  }
  var complectRow = <?=$complectRow?>;
  function deleteComplect(complectID) {
    $('#complect_' + complectID).val('');
    $('#div_complect_' + complectID).hide();
  }
  function complectSearch() {
    var categoryID = $('#complect_category_id').val();
    var id = <?=$data['id']?>;
    $('#complect_search_results').html('Ищем ...');
    $('#complect_search_results').load('./index.php?request=<?=$this->_commonObj->_objectName?>/get_goods_from_category&id=' + id + '&category_id=' + categoryID + '&what=complect');
  }
  function complectAddPre(id) {
    $.get('./index.php?request=<?=$this->_commonObj->_objectName?>/get_good_name&id=' + id, function (name) {
      if (name) {
        html = '<div id="div_complect_' + complectRow + '" class="product_additional_item">';
        html += '  <input type="hidden" name="complect[' + complectRow + '][' + id + ']" id="complect_' + complectRow + '" value="' + id + '">';
        html += '  <div class="product_additional_name">';
        html += name;
        html += '  </div>';
        html += '  <div class="product_additional_sort">';
        html += '    <input type="text" name="complect_sort[' + complectRow + '][' + id + ']" value="99" title="Порядок">';
        html += '  </div>';
        html += '  <a href="javascript:deleteComplect(' + complectRow + ')"><img src="/img/admin/delete16.png"></a>';
        html += '</div>';
        $('#complect_category_id').before(html);
        complectRow++;
      }
    });
  }
  var additionalRow = <?=$additionalRow?>;
  function deleteAdditional(additionalID) {
    $('#additional_' + additionalID).val('');
    $('#div_additional_' + additionalID).hide();
  }
  function additionalSearch() {
    var categoryID = $('#additional_category_id').val();
    var id = <?=$data['id']?>;
    $('#additional_search_results').html('Ищем ...');
    $('#additional_search_results').load('./index.php?request=<?=$this->_commonObj->_objectName?>/get_goods_from_category&id=' + id + '&category_id=' + categoryID + '&what=additional');
  }
  function additionalAddPre(id) {
    $.get('./index.php?request=<?=$this->_commonObj->_objectName?>/get_good_name&id=' + id, function (name) {
      if (name) {
        html = '<div id="div_additional_' + additionalRow + '" class="product_additional_item">';
        html += '  <input type="hidden" name="additional[' + additionalRow + '][' + id + ']" id="additional_' + additionalRow + '" value="' + id + '">';
        html += '  <div class="product_additional_name">';
        html += name;
        html += '  </div>';
        html += '  <div class="product_additional_sort">';
        html += '    <input type="text" name="additional_sort[' + additionalRow + '][' + id + ']" value="99" title="Порядок">';
        html += '  </div>';
        html += '  <a href="javascript:deleteAdditional(' + additionalRow + ')"><img src="/img/admin/delete16.png"></a>';
        html += '</div>';
        $('#additional_category_id').before(html);
        additionalRow++;
      }
    });
  }
  var similarRow = <?=$similarRow?>;
  function deleteSimilar(similarID) {
    $('#similar_' + similarID).val('');
    $('#div_similar_' + similarID).hide();
  }
  function similarSearch() {
    var categoryID = $('#similar_category_id').val();
    var id = <?=$data['id']?>;
    $('#similar_search_results').html('Ищем ...');
    $('#similar_search_results').load('./index.php?request=<?=$this->_commonObj->_objectName?>/get_goods_from_category&id=' + id + '&category_id=' + categoryID + '&what=similar');
  }
  function similarAddPre(id) {
    $.get('./index.php?request=<?=$this->_commonObj->_objectName?>/get_good_name&id=' + id, function (name) {
      if (name) {
        html = '<div id="div_similar_' + similarRow + '" class="product_additional_item">';
        html += '  <input type="hidden" name="similar[' + similarRow + '][' + id + ']" id="similar_' + similarRow + '" value="' + id + '">';
        html += '  <div class="product_additional_name">';
        html += name;
        html += '  </div>';
        html += '  <div class="product_additional_sort">';
        html += '    <input type="text" name="similar_sort[' + similarRow + '][' + id + ']" value="99" title="Порядок">';
        html += '  </div>';
        html += '  <a href="javascript:deleteSimilar(' + similarRow + ')"><img src="/img/admin/delete16.png"></a>';
        html += '</div>';
        $('#similar_category_id').before(html);
        similarRow++;
      }
    });
  }
  function reloadFeatures() {
    var c = $('#category_id').val();
    $('#features').load('./index.php?request=<?=$this->_commonObj->_objectName?>/load_features&id=<?=$data['id']?>&category_id=' + c);
  }
  toggleIsAction();
  reloadFeatures();
</script>