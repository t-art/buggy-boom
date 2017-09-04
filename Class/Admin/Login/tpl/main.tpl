<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Авторизация</title>
  <link href="/css/admin.css" rel="stylesheet" type="text/css">
</head>
<body>
<div style="margin-top:50px;">
  <?php
  if ($data['errText']) {
    ?>
    <div style="text-align:center;margin-bottom:10px;color:#aa0000"><?=$data['errText']?></div>
    <?php
  }
  ?>
  <form action="./index.php?request=login/login" method="post">
    <table cellspacing="3" cellpadding="0" align="center">
      <tr>
        <td rowspan="3" style="vertical-align:top;padding-right:10px;"><img src="/img/admin/authorize.png"></td>
        <td>Логин:</td>
        <td><input type="text" name="login" id="login" size="20"></td>
      </tr>
      <tr>
        <td>Пароль:</td>
        <td><input type="password" name="password" size="20"></td>
      </tr>
      <tr>
        <td></td>
        <td align="right"><input type="submit" value="Вход" class="button"></td>
      </tr>
    </table>
  </form>
</div>

<script type="text/javascript">
  document.getElementById('login').focus();
</script>

</body>