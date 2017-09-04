<h3 class="acenter">Импорт товаров</h3>
<form action="./index.php?request=service/import_price_import" method="post" enctype="multipart/form-data" onsubmit="return confirm('Уверены?')">
  <div style="text-align: center;">
    <b>Колонки</b>: артикул, название, цена, название рубрики, производитель<br>
    Данные должны располагаться на первом листе<br>
    <br>
    <label><input type="checkbox" name="only_update_price" value="1"> Только обновление цен</label><br>
    <br>
    <label><input type="checkbox" name="hide_if_not_in_price" value="1"> Скрывать товары, отсутствующие в прайсе</label><br>
    <br>
    <input type="file" name="price"><br>
    <br>
    <input type="submit" value="Импорт" class="button">
  </div>

</form>