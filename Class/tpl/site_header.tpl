<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"

  "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <title><?=$data['meta_title']?></title>

  <META NAME="keywords" CONTENT="<?=$data['meta_keywords']?>">

  <META NAME="description" CONTENT="<?=$data['meta_description']?>">
  <meta name='yandex-verification' content='6f994507b59d0d8e' />

  <link rel="stylesheet" type="text/css" href="/css/site.css">
  <link rel="stylesheet" type="text/css" href="/inc/fonts/stylesheet.css">

  <link rel="stylesheet" type="text/css" href="/css/jquery-ui-1.9.2.custom.css">

  <link rel="stylesheet" type="text/css" href="/css/jquery.fancybox.css">

  <link rel="stylesheet" type="text/css" href="/css/jquery.pnotify.default.css">

  <script type="text/javascript" src="/js/jquery.js"></script>

  <script type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>

  <script type="text/javascript" src="/js/jquery.fancybox.pack.js"></script>

  <script type="text/javascript" src="/js/jquery.pnotify.min.js"></script>

  <script type="text/javascript" src="/js/common.js"></script>

  <script type="text/javascript">

    $(document).ready(function() {

      $(".fancybox").fancybox();

      $("#feedback_button").fancybox({autosize: true});

      $("input.spinner").spinner({ min: 1, max: 99 });

      $(".cart_info table input.spinner").on("spinstop", function( event, ui ) {

        var elemID = String($(this).prop('id'));

        elemID = elemID.replace('quant', '');

        cartRecount(elemID);

      });

      $.pnotify.defaults.history = false;

      $( "#search" ).autocomplete({

        source: "/ajax/search_autocomplete.php",

        minLength: 3,

        select: function( event, ui ) {

          if (ui.item.url != undefined) {

            document.location.href = '/' + ui.item.url + '.html';

          }

        }

      });



        cartRecount();





		<?

			foreach($_SESSION['cart']['products'] as $key=>$none) { ?>

				$('[onclick="cartAdd(<?=$key?>)"]').addClass('incart').css({ width:'149px' }).val('Уже в корзине');

		<?	}	?>





    });

 
  </script>

</head>

<body style="background:url(/img/bg.png);">

<iframe src="/inc/none.html" name="ajax" id="ajax" style="display:none; width:100%;"></iframe>

<?
    // отрабатываем фильтр (сколько выбратно товаров)
    if($_GET['filter_pre']) {
      ob_clean();
      ?><script>
        $('#span_count').html('<?=$data["count_products"]?>');
        $('#selection_hint').fadeIn();
        out_div_pre = setTimeout("$('#selection_hint').fadeOut();", 5000);
      </script>
      <?
      die();
    }
    ?>
<div id="main">
<div id="wrapper">

<div class="header" style="height:auto!important;">

  <div>

    <div class="logo" style="padding-left:10px;position:relative; z-index: 5;"><a href="/"><img src="/img/logo1.png" alt=""></a></div>

      <div class="phones" style="padding-top:32px!important;"><span class="phone" style="color:#0094C3;line-height: 26px;">+7 (495) 778-70-93</span>

          <br/><span style="color: #a3a3a3;font-size: 12px;">ежедневно с <span style="color: #9B9B9B;font-weight:bold;">9:00</span> до <span style="font-weight:bold;color: #9B9B9B;">18:00</span></span></div>

    <div class="pages" style="padding:34px 0 0!important;">

      <?php

      foreach ($data['pages'] as $page) {

        ?>

        <div class="item">

          <a href="<?=$page['url']?>"><?=$page['name']?></a>

        </div>

        <?php

      }

      ?>

		<div style="clear:both;"></div>

		

    </div>

      <div class="header_cart" style="cursor:pointer;" onClick="location.href='/cart.php';">

        <div class="inner" style="white-space:nowrap; width:96px; overflow:visible; background:none; margin-top: 10px;">

          <div style="float:left; margin-left: -50px">
            <img src="/img/cart.png">
            <span id="cart_quantity">0</span>
          </div>
          <div style="float:left; margin-top: 5px;">
            <span id="cart_sum">0</span> <span class="b-rub">Р</span>
          </div>

        </div>

      </div>

    <!--<div class="phone"><img src="/img/phone.png" width="281" height="76" alt=""></div>-->

    <div class="cb"></div>

  </div>

</div>

<div class="subheader"><div>&nbsp;</div></div>



<div class="content" style="margin-top:0;">



  <?php

  if (isset($data['left_menu']) && count($data['left_menu']) > 0) {

    ?>

    <div class="fleft">

      <div id="full_catalog" style="margin-top:0px;" class="<?=($data['root_cat'] || $data['name']=='Корзина')?'dop_page':''?>">

        <div class="left_menu <?=($data['root_cat'] || $data['name']=='Корзина')? '_dp_page':''?>" style="padding-top:0px;display:<?=($data['root_cat'] || $data['name']=='Корзина' || $data['name']=='Оформление заказа') ? 'none': ''?>">

          <!--span style="  font-family: tahoma;  color: #010101;  font-size: 18px;  padding: 10px 5% 0 5%;  margin-left: 5px;  display: block;">Каталог продукции</span><br/-->

          <div style=" border-bottom:2px solid #F0F0F0;"></div>

          <?php

          foreach ($data['left_menu'] as $catData) {

            ?>

            <div class="item" style="background:url(<?=$catData['image']?>)no-repeat 12px center #FAFAFA;">

              <div class="name">

                <a href="/<?=$catData['url']?>.html"><?=$catData['name']?></a>

              </div>

              <div class="subitems" id="subcats_<?=$catData['id']?>">

                <?php
                $count = 0;
                echo "<div style=\"width: 100%\">";
                foreach ($catData['subitems'] as $subcatData) {
                  if ($count % 2 == 0 && $count != 0){
                    echo "</div><div style=\"width: 100%\">";
                  }
                  ?>

                  <div class="item">

                    <a href="/<?=$subcatData['url']?>.html"><?=$subcatData['name']?></a>

                  </div>

                  <?php
                  $count++;
                }
                echo "</div>";
                ?>

              </div>

            </div>

            <?php

          }

          ?>
        </div>
      </div>

      <?if ($data['root_cat'] && !$data['article']) {?>
        <form id='frmFilter' action=<?=$_SERVER['SCRIPT_URI']?>>
          <div class="category_selection">
            <?php
            if (count($data['brands']) > 0) {
              ?>
              <div class="item1">
                <div class="caption">Производители:</div>
                <div class="data">
                  <?php
                  foreach ($data['brands'] as $brandID => $brandName) {
                    ?>
                    <label style="width:90px;"><input type="checkbox" name="b[]" <?=in_array($brandID, $data['brands_selected']) ? 'checked' : ''?> value="<?=$brandID?>"> <?=$brandName?></label>
                    <?php
                  }
                  ?>
                </div>
              </div>
              <div class="cb"></div>
              <?php
            }
            ?>
            <div class="item1">
              <div class="caption" style="padding-top: 3px;">Цена:</div>
              <div class="data">
                от <input type="text" name="price_from" value="<?=$data['price_from'] ? htmlspecialchars($data['price_from'], ENT_QUOTES) : ''?>" placeholder="<?=$data['price_from_placeholder']?>" style="width:70px;">
                до <input type="text" name="price_to" value="<?=$data['price_to'] ? htmlspecialchars($data['price_to'], ENT_QUOTES) : ''?>" placeholder="<?=$data['price_to_placeholder']?>" style="width:70px;">
                руб.
              </div>
            </div>
            <div class="cb"></div>
            <div style="background-color: #f0f0f0;height:1px;margin: 10px 0;"></div>
            <?php
            if (is_array($data['features']) && count($data['features']) > 0) {
              foreach ($data['features'] as $featureId => $featureData) {
                if (count($featureData['values'])>1 || $featureData['to'])
                {
                  ?>
                  <div class="item2" style="margin-top:10px;">
                    <div class="caption" style="width:100px;font-weight:bold;margin-bottom:3px;overflow:hidden;"><?=$featureData['name']?>:</div>
                    <div class="data">
                      <?php

                      if ($featureData['to'])
                      {
                        if (isset($data['features_pr'][$featureId]))
                        {
                          $val_from=$data['features_pr'][$featureId]['from'];
                          $val_to=$data['features_pr'][$featureId]['to'];
                        }

                        ?>
                        от <input type="text" name="pr[<?=$featureId?>][from]" value="<?=$val_from?$val_from:(double)$featureData['from']?>" style="width:70px;">
                        до <input type="text" name="pr[<?=$featureId?>][to]" value="<?=$val_to?$val_to:(double)$featureData['to']?>" style="width:70px;">
                        <div style="clear:both;margin-top:20px;"></div>
                        <?
                      }
                      else
                      {
                        foreach ($featureData['values'] as $value) {
                          ?>
                          <label style="width:90px;"><input type="checkbox" name="f[<?=$featureId?>][]" value="<?=$value?>" <?=isset($data['features_selected'][$featureId]) && in_array($value, $data['features_selected'][$featureId]) ? 'checked' : ''?>> <?=$value?></label>
                          <?php
                        }
                      }
                      ?>
                    </div>
                  </div>
                  <?php
                }
              }
            }
            ?>

            <div class="cb"></div>
            <!--div style="text-align: right;margin: 10px 20px 0 0;">
              <input type="submit" class="btn1" value="Подобрать">
            </div>
          </div-->


            <input type="hidden" value="1" name="filter_pre" id="filter_pre">
            <div style="text-align: center">
              <input class="button" type="button" onClick="$('#filter_pre').val(0); this.form.submit();" value="Подобрать">
            </div>
          </div>
          <div id="selection_hint">
            <!--img width="16" height="27" style="position:absolute; margin:-3px 0 0 224px;" src="/img/p4.png"-->
            Отобрано товаров:&nbsp;<span id="span_count"></span>
            <div style="margin-top:5px;"><a href="javascript://" onClick="$('#filter_pre').val(0); $(this).parents('form:first')[0].submit();">показать</a></div>
          </div>
        </form>
        <script>
          var out_div_pre;
          $('#frmFilter input:checkbox').change(function(){
            //var ap = absPosition($('#'+$(this).attr('id_parent'))[0]);
            var ap = absPosition($(this));
            var x = Math.round(ap.x)+70;
            var y = Math.round(ap.y) - 30;

            try{ clearTimeout(out_div_pre); }catch(e){}
            $('#selection_hint').hide().css('left', x).css('top', y);
            toAjax($('#frmFilter')[0]);
          });
          $('#frmFilter input:text').keyup(function(){
            //var ap = absPosition($('#'+$(this).attr('id_parent'))[0]);
            var ap = absPosition($(this));
            var x = Math.round(ap.x)+70;
            var y = Math.round(ap.y) - 25;

            try{ clearTimeout(out_div_pre); }catch(e){}
            $('#selection_hint').css('left', x).css('top', y);
            toAjax($('#frmFilter')[0]);
          });
        </script>

      <?}?>
      <?php

      //if (isset($data['products_viewed']) && count($data['products_viewed']) > 0) {
if (1==0){
        ?>

        <div class="products_viewed">

          <div class="caption">Просмотренные товары</div>

          <?php

          foreach ($data['products_viewed'] as $productViewedData) {

            ?>

            <div class="item">

              <div class="name"><a href="/<?=$productViewedData['url']?>.html"><?=$productViewedData['name']?></a></div>

              <div class="price"><?=$productViewedData['price']?></div>

              <div class="cb"></div>

            </div>

            <?php

          }

          ?>

        </div>

        <?php

      }

      ?>

    </div>
    <div class="search-block">
      <div class="search">

      <form id="frmSearch" action="/search.php">

        <table width="100%" cellspacing="0" cellpadding="0" border="0">

          <tr>

            <td>

              <div class="ui-widget">

                <input type="text" name="term" id="search" value="<?=isset($this->_getParams['term']) ? htmlspecialchars(trim($this->_getParams['term']), ENT_QUOTES) : ''?>" placeholder="Поиск">

              </div>

            </td>

            <td width="80" align="center">

              <img src="/img/btn_search.png" width="24" height="24" onclick="$('#frmSearch').submit();">

            </td>

          </tr>

        </table>

      </form>

    </div>
    </div>

    <?php

  }

  ?>

  <div style="float: right; <?=(isset($data['article']) || $data['name'] == 'Корзина' || $data['name'] == 'Оформление заказа') ? "width:100%;" : 'width:70%;'?>"><?