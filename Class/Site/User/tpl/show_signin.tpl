<div class="breadcrumbs">
  <a href="/">Главная</a> - <span><?=$data['name']?></span>
</div>

<div style="float:left;width:370px;margin-right:30px;">
  <div class="caption2"><div>Стать дилером</div></div>
  <div class="block1_text" id="user_signup">
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signup_email" style="width:315px;" value="e-mail" onfocus="if(this.value == 'e-mail') this.value = ''" onblur="if(this.value == '') this.value = 'e-mail'"></div>
    </div>
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signup_password" style="width:315px;" value="пароль" onfocus="this.type='password';if(this.value == 'пароль') this.value = ''" onblur="if(this.value == '') {this.value = 'пароль';this.type='text';}"></div>
    </div>
    <div class="fboth" style="margin-bottom: 20px;">
      <div class="fleft"><input class="fancy" type="text" id="signup_password_repeat" style="width:315px;" value="повторите пароль" onfocus="this.type='password';if(this.value == 'повторите пароль') this.value = ''" onblur="if(this.value == '') {this.value = 'повторите пароль';this.type='text';}"></div>
    </div>
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signup_fio" style="width:315px;" value="Ф.И.О." onfocus="if(this.value == 'Ф.И.О.') this.value = ''" onblur="if(this.value == '') this.value = 'Ф.И.О.'"></div>
    </div>
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signup_phone" style="width:315px;" value="Телефон" onfocus="if(this.value == 'Телефон') this.value = ''" onblur="if(this.value == '') this.value = 'Телефон'"></div>
    </div>
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signup_company_name" style="width:315px;" value="Наименование организации" onfocus="if(this.value == 'Наименование организации') this.value = ''" onblur="if(this.value == '') this.value = 'Наименование организации'"></div>
    </div>
    <div class="fboth">
      <div class="fleft"><textarea class="fancy" id="signup_company_details" style="width:315px;height:60px;" onfocus="if(this.value == 'Реквизиты организации') this.value = ''" onblur="if(this.value == '') this.value = 'Реквизиты организации'">Реквизиты организации</textarea></div>
    </div>
    <div class="fboth">
      <div class="fleft"><textarea class="fancy" id="signup_notes" style="width:315px;height:60px;" onfocus="if(this.value == 'Примечание') this.value = ''" onblur="if(this.value == '') this.value = 'Примечание'">Примечание</textarea></div>
    </div>
    <input type="button" class="button_grey" value="подать заявку" style="width:225px;" onclick="userSignUp()">
  </div>
</div>

<div style="float:left;width:340px;">
  <div class="caption2"><div>Вход</div></div>
  <div class="block1_text" id="user_signin">
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signin_email" style="width:285px;" value="e-mail" onfocus="if(this.value == 'e-mail') this.value = ''" onblur="if(this.value == '') this.value = 'e-mail'"></div>
    </div>
    <div class="fboth">
      <div class="fleft"><input class="fancy" type="text" id="signin_password" style="width:285px;" value="пароль" onfocus="this.type='password';if(this.value == 'пароль') this.value = ''" onblur="if(this.value == '') {this.value = 'пароль';this.type='text';}"></div>
    </div>
    <input type="button" class="button_grey" value="войти" style="width:225px;" onclick="userSignIn()">
  </div>
</div>

