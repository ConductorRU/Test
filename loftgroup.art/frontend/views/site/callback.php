<?php
	use common\models\Settings;
	$this->registerMetaTag(['name' => 'description','content' => Settings::Lang('callback_xx_meta_desc', Settings::META | Settings::SLASH)]);
	$this->registerMetaTag(['name' => 'keywords','content' => Settings::Lang('callback_xx_meta_keys', Settings::META | Settings::SLASH)]);
	$name = Settings::Lang('callback_xx_name');
	$this->title = $name . ' - Loft Group';
	$m1 = str_replace('#POLICY#', '<span>' . Settings::Lang('callback_xx_policy_word') . '</span>', Settings::Lang('callback_xx_policy'));
	$m2 = str_replace('#AGREE#', '<span>' . Settings::Lang('callback_xx_agree_word') . '</span>', Settings::Lang('callback_xx_agree'));
	$m3 = str_replace('#MESSAGE#', '<span>' . Settings::Lang('callback_xx_message_word') . '</span>', Settings::Lang('callback_xx_message'));
?>
<div id="callback">
	<div class="container">
		<div class="row">
			<div class="col-2">
				<a class="pgBack ajax" data-dir="left" href="/"><img class="svg" src="/img/arrow_back.svg" alt="<?= Settings::Lang('lang_xx_back', Settings::SLASH) ?>"><span><?= Settings::Lang('lang_xx_back') ?></span></a>
				<div class="info">
					<div><a class="ajax" href="/contacts"><?= Settings::Lang('contacts_xx_left') ?></a></div>
					<div class="transp"><?= Settings::Lang('callback_xx_left') ?></div>
					<div class="policy">
						<div><?= $m1 ?></div>
						<div><?= $m2 ?></div>
						<div><?= $m3 ?></div>
					</div>
				</div>
			</div>
			<div class="col-8">
				<div class="form">
					<div class="field"><i class="placeholder"><?= Settings::Lang('callback_xx_form_name') ?></i><input type="text" name="name" autocomplete="off" /></div>
					<div class="field"><i class="placeholder"><?= Settings::Lang('callback_xx_form_phone') ?></i><input type="text" name="phone" autocomplete="off"  /></div>
					<div class="field"><i class="placeholder"><?= Settings::Lang('callback_xx_form_comment') ?></i><input type="text" name="comment" autocomplete="off" /></div>
					<div class="send"><button><?= Settings::Lang('callback_xx_form_send') ?></button></div>
				</div>
			</div>
		</div>
	</div>
</div>