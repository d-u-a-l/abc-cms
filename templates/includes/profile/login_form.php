<?php if (isset($modules['profile']) AND isset($modules['login'])) { ?>
	<div id="login_form">
	<?php if (access('user auth')) { ?>
		<?=i18n('profile|hello')?>, <?=$user['email']?>! &nbsp;
		<a href="/<?=$modules['profile']?>/"><?=i18n('profile|link')?></a> &nbsp;
		<a href="/<?=$modules['login']?>/exit/"><?=i18n('profile|exit')?></a>
		<?php if (access('user admin')) echo '<br /><a href="/admin.php">панель управления</a><br />'; ?>
	<?php } else { ?>

		<a href="/<?=$modules['login']?>/" class="window_open" data-window_id="window_login" title="<?=i18n('profile|enter')?>"><?=i18n('profile|enter')?></a>
		<?php if (isset($modules['profile'])) {?>
		| <a href="/<?=$modules['registration']?>/" title="<?=i18n('profile|registration')?>"><?=i18n('profile|registration')?></a>
		<?php } ?>

		<div id="window_login" class="window window_login" data-attr="modal middle">
			<form action="/<?=$modules['login']?>/enter/" method="post" class="window_data validate">
				<div class="window_title"><?=i18n('profile|auth',true)?></div>
				<div class="window_close"></div>
				<input name="email" class="input email required" value="" />
				<input name="password" class="input required" type="password" value="" /><br />
				<?=html_array('form/captcha2')?>
				<label class="remember_me"><input name="remember_me" type="checkbox" value="1"/ ><span>запомнить меня</span></label>
				<?php if (isset($modules['remind'])) {?>
				<a class="remind" href="/<?=$modules['remind']?>/" title="<?=i18n('profile|remind')?>"><?=i18n('profile|remind')?></a>
				<?php } ?>
				<a href="#" class="button gray js_submit" title="<?=i18n('profile|enter')?>"><span><?=i18n('profile|enter')?></span></a>
				<div class="clear"></div>
			</form>
		</div>
	<?php } ?>
	</div>
<?php } ?>