</div>



<div class="cb"></div>

</div>

</div>

<div class="footer" style="width:980px; padding:10px;">

	<div class="inner">

		<?/*?>  <div style="display:none">    <?php    foreach ($data['pages2'] as $page) {      ?>      <div style="display: inline-block;background: url('/img/ico_price.png') top left no-repeat;padding: 30px 0 20px 80px;margin-right: 40px;">        <a href="<?=$page['url']?>"><?=$page['name']?></a>      </div>    <?php    }    ?>    <div style="display: inline-block;background: url('/img/ico_mail.png') top left no-repeat;padding: 30px 0 20px 80px;">      <a id="feedback_button" onclick="feedbackPre()" href="#div_feedback">Написать письмо</a>    </div>  </div>    <?*/?>

		<?/*?>  <div class="pages">    <?php    foreach ($data['pages'] as $page) {      ?>      <div class="item">        <a href="<?=$page['url']?>"><?=$page['name']?></a>      </div>      <?php    }    ?>  </div>    <?*/?>

		<div style="float:left;">

			<?=$this->GetSetting('copyright')?>

		</div>

		<div style="float:right">

			<?=$data['counters']?>

		</div>

	</div>

</div>

<div id="div_feedback" style="display: none;">

	<div id="feedback_form_error"></div>

	<div id="feedback_form_success">Ваш вопрос отправлен. Благодарим за уделенное время!</div>

	<div id="feedback_form_content">

		<div class="caption">Ф.И.О.</div>

		<div>

			<input type="text" id="feedback_fio" class="input">

		</div>

		<div class="caption">Телефон</div>

		<div>

			<input type="text" id="feedback_phone" class="input">

		</div>

		<div class="caption">E-mail</div>

		<div>

			<input type="text" id="feedback_email" class="input">

		</div>

		<div class="caption">Ваш вопрос</div>

		<div>

			<textarea id="feedback_feedback" class="input" rows="10"></textarea>

		</div>

		<input type="button" value="отправить" class="button_grey" onclick="feedbackPost()">

	</div>

</div>

<?	/*

<div id="cart_helper" style="display:<?=$_SESSION['cart']['totals']['quant'] == 0 ? 'none' : 'block'?>;"><a href="/cart.php"><img src="/img/cart_helper.png" width="82" height="94"></a></div>

*/ ?>
</div>

</body></html>