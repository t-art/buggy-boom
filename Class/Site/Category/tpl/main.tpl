<?php



include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';



?>



  <div class="breadcrumbs">



  <a href="/">Главная</a> <?=$data['breadcrumbs']?> <div class="item"><?=$data['name']?></div>



</div>



<h1><?=$data['h1']?></h1>





<?php



if (trim(stripslashes($data['full_descr'])) || $data['subcategories']) {



  ?>



  <div style="margin-bottom: 20px;">



    <?php



    if ($data['image']) {



      ?>



      <!--img src="<?=$data['image']?>" width="200" height="200" style="float:left;margin:0 20px 20px 0;"-->



      <?php



    }



    ?>



    <?=$data['full_descr']?>



  </div>



  <div class="cb"></div>



  <?php



  if ($data['subcategories']) {



    ?>



    <div class="category_subitems">

		



      <div class="block" style="width:48%; margin:0;">



        <?php



        $data['subcategories'] = array_values($data['subcategories']);

		  

        $cnt = count($data['subcategories']);



        for ($i = 0; $i < ceil($cnt / 2); $i++) {



          $subcatData = $data['subcategories'][$i];



          ?>



          <div class="item">



            <a href="/<?=$subcatData['url']?>.html"><?=$subcatData['name']?></a>



          </div>



        <?php



        }



        ?>



      </div>





      <div class="block" style="width:48%; float:right; margin:0;">



        <?php



        for ($i = (int)ceil($cnt / 2); $i < $cnt; $i++) {



          $subcatData = $data['subcategories'][$i];



          ?>



          <div class="item">



            <a href="/<?=$subcatData['url']?>.html"><?=$subcatData['name']?></a>



          </div>



        <?php



        }



        ?>



      </div>



      <div class="fboth"></div>



    </div>



  <?php



  }



  ?>



  <?php



}







if ($data['products']) {



  ?>

<div class="p_on_index_wrp">

  <!--<table class="category_products">



    <tr>



      <th>Марка</th>



      <th style="width:90px;">Цена</th>



      <th style="width:70px;">Кол-во</th>



      <th style="width:140px;">&nbsp;</th>



    </tr>-->



    <?php

$i=0;

    foreach ($data['products'] as $productID => $p) {



$i++;

$last = $i%4==0?" last":"";

?><div class="p_on_index<?=$last?>">

    <div class="inner">

        <div class="title">

            <a href="/<?=$p["url"]?>.html"><?=$p["name"]?> <?=$p["article"]?></a>

        </div>

        <br/>



        <div class="img">

            <a href="/<?=$p["url"]?>.html">

            <img src="<?=$p["image"]?>" alt="<?=$p["name"]?>" />

            </a>

        </div>

        <br/>



        <div class="price">

            <b style="float:right; 	 color:#0390C8; font:bold 18px Arial;"><?=$p["price"]?> р.</b>

            Цена:

        </div>

        <br/>



        <div class="button">

            <input type="hidden" class="spinner" value="1" id="quant<?=$p["id"]?>" readonly>

            <input type="button" class="btn" id="bc<?=$p["id"]?>" onclick="cartAdd(<?=$p["id"]?>)" value="Добавить в корзину">

        </div>

    </div>

</div><?php

            if($i%4==0)

            {

                ?><div class="cb"></div><?php

            }





/*

      ?>



      <tr>



        <td><a href="/<?=$productData['url']?>.html"><?=$productData['name']?> <?=$productData['article']?></a></td>



        <td><?=$productData['price']?></td>



        <td><input type="text" class="spinner" value="1" id="quant<?=$productID?>" readonly></td>



        <td><input type="button" class="btn" id="bc<?=$productID?>" onclick="cartAdd(<?=$productID?>)" value="добавить в корзину"></td>



      </tr>



      <?php

*/

    }



    ?>



  <!--</table>-->

</div>



  <?php



  if ($data['paginator']) {



    echo $data['paginator'];



  }



}







if ($data['lower_text']) {



  ?>



  <div class="description">



    <?=$data['lower_text']?>



  </div>



  <?php



}







