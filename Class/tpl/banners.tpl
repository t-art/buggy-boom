<?
if($data["banners"])
{
?><div class="banners"><?
foreach($data["banners"] as $banner)
{
    ?><div class="banner">
        <a href="<?=$banner["link"]?>"><img src="<?=$banner["image"]?>" alt="" /></a>
    </div><?
}
?></div><?
}
