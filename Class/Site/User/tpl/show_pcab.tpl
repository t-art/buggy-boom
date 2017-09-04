<div class="fright osc1" style="margin-top:15px;"><a href="/?route=user/signout">Выход</a></div>

<div class="breadcrumbs">
  <a href="/">Главная</a> - <span><?=$data['name']?></span>
</div>

<div class="caption2"><div>Скидка</div></div>
<div class="block1_text">
  Ваша скидка <span class="osc1"><?=$data['discount']?>%</span>
</div>


<div class="caption2"><div>Ваши данные</div></div>
<div class="block1_text" id="user_signup">
  <div class="fboth">
    <div class="fleft"><input class="fancy" type="text" id="email" style="width:315px;" value="<?=htmlspecialchars($data['email'], ENT_QUOTES)?>" title="e-mail" readonly></div>
  </div>
  <div class="fboth">
    <div class="fleft"><input class="fancy" type="password" id="password" style="width:315px;" value="<?=htmlspecialchars($data['password'], ENT_QUOTES)?>" title="пароль" onfocus="this.type='password';if(this.value == 'пароль') this.value = ''" onblur="if(this.value == '') {this.value = 'пароль';this.type='text';}"></div>
  </div>
  <div class="fboth" style="margin-bottom: 20px;">
    <div class="fleft"><input class="fancy" type="password" id="password_repeat" style="width:315px;" value="<?=htmlspecialchars($data['password'], ENT_QUOTES)?>" title="повторите пароль" onfocus="this.type='password';if(this.value == 'повторите пароль') this.value = ''" onblur="if(this.value == '') {this.value = 'повторите пароль';this.type='text';}"></div>
  </div>
  <div class="fboth">
    <div class="fleft"><input class="fancy" type="text" id="fio" style="width:315px;" value="<?=$data['fio'] ? htmlspecialchars($data['fio'], ENT_QUOTES) : 'Ф.И.О.'?>" title="Ф.И.О." onfocus="if(this.value == 'Ф.И.О.') this.value = ''" onblur="if(this.value == '') this.value = 'Ф.И.О.'"></div>
  </div>
  <div class="fboth">
    <div class="fleft"><input class="fancy" type="text" id="phone" style="width:315px;" value="<?=$data['phone'] ? htmlspecialchars($data['phone'], ENT_QUOTES) : 'Телефон'?>" title="Телефон" onfocus="if(this.value == 'Телефон') this.value = ''" onblur="if(this.value == '') this.value = 'Телефон'"></div>
  </div>
  <div class="fboth">
    <div class="fleft"><input class="fancy" type="text" id="company_name" style="width:315px;" value="<?=$data['company_name'] ? htmlspecialchars($data['company_name'], ENT_QUOTES) : 'Наименование организации'?>" title="Наименование организации" onfocus="if(this.value == 'Наименование организации') this.value = ''" onblur="if(this.value == '') this.value = 'Наименование организации'"></div>
  </div>
  <div class="fboth">
    <div class="fleft"><textarea class="fancy" id="company_details" style="width:315px;height:60px;" title="Реквизиты организации" onfocus="if(this.value == 'Реквизиты организации') this.value = ''" onblur="if(this.value == '') this.value = 'Реквизиты организации'"><?=$data['company_details'] ? htmlspecialchars($data['company_details'], ENT_QUOTES) : 'Реквизиты организации'?></textarea></div>
  </div>
  <div class="fboth">
    <div class="fleft"><textarea class="fancy" id="notes" style="width:315px;height:60px;" title="Примечание" onfocus="if(this.value == 'Примечание') this.value = ''" onblur="if(this.value == '') this.value = 'Примечание'"><?=$data['notes'] ? htmlspecialchars($data['notes'], ENT_QUOTES) : 'Примечание'?></textarea></div>
  </div>
  <input type="button" class="button_grey" value="сохранить" style="width:225px;" onclick="userSaveInfo()">
</div>

