<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/search_line.tpl';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Class/tpl/banners.tpl';
if ($data['foreword']) {
    ?><div class="foreword"><?= $data['foreword'] ?></div><?
}

?><div class="p_on_index_wrp"><?php
    if($data['p_on_index'])
    {
        ?><span style="font-family: tahoma;  color: #010101;  font-size: 18px;  padding: 18px 0;  display: block;">Спецпредложения</span><?
        $i=0;
        foreach ($data['p_on_index'] as $p)
        {
            $i++;
            $last = $i%4==0?" last":"";
            ?>
    <div class="p_on_index<?=$last?>">
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
                <b style="float:right; color:#0390C8; font:bold 18px Arial;"><?=$p["price"]?> р.</b>
                Цена:
            </div>
            <br/>

            <div class="button">
                <input type="hidden" class="spinner" value="1" id="quant<?=$p["id"]?>" readonly>
                <input type="button" class="btn" id="bc<?=$p["id"]?>" onclick="cartAdd(<?=$p["id"]?>)" value="Добавить в корзину">
            </div>
        </div>
    </div>
    <?php
            if($i%4==0)
            {
                ?>
    <div class="cb"></div>
    <?php
            }
        }
    }
?>
    <div class="cb"></div>
</div>


<div class="cats_index">

    <?
        /*
          foreach ($data['categories'] as $catData) {

            ?>

    <div class="item">

        <div class="name"><?=$catData['name']?></div>

        <div class="subitems">

            <div class="block">

                <?php

                  $cnt = count($catData['subitems']);

                  for ($i = 0; $i < ceil($cnt / 2); $i++) {

                    $subcatData = $catData['subitems'][$i];

                    ?>

                <div class="item">

                    <a href="/<?=$subcatData['url']?>.html"><?=$subcatData['name']?></a>

                </div>

                <?php

                  }

                  ?>

            </div>

            <div class="block">

                <?php

                  for ($i = ceil($cnt / 2); $i < $cnt; $i++) {

                    $subcatData = $catData['subitems'][$i];

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

    </div>

    <?php

          }
        */
        ?>

</div>

<?php



if (count($data['news']) > 0) {

?>

<div class="news_index">

    <?php

        foreach ($data['news'] as $newsItem) {

            ?>

    <div class="item">

        <div class="date"><?= $newsItem['datef'] ?></div>

        <div class="name"><a href="/<?= $newsItem['url'] ?>.html"><?= $newsItem['name'] ?></a></div>

        <div class="short_descr"><?= $newsItem['short_descr'] ?></div>

    </div>

    <?php

        }

        ?>

</div>

<div class="cb"></div>

<?php

}