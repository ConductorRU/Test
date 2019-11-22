<?php
	use common\models\Settings;
	$this->registerMetaTag(['name' => 'description','content' => Settings::Lang('contacts_xx_meta_desc', Settings::META | Settings::SLASH)]);
	$this->registerMetaTag(['name' => 'keywords','content' => Settings::Lang('contacts_xx_meta_keys', Settings::META | Settings::SLASH)]);
	$name = Settings::Lang('contacts_xx_name');
	$photo = Settings::GetImageUrl('contacts_photo');

	$this->title = $name . ' - Loft Group';
?>
<div id="map">
	<?php if($photo): ?><div class="img" style="background-image:url(<?= $photo ?>)"></div><?php endif ?>
</div>
<div id="contacts" class="container">
	<div class="projects">
		<div class="row align-items-end">
			<div class="col-2">
				<a class="pgBack ajax" data-dir="left" href="/"><img class="svg" src="/img/arrow_back.svg" alt="<?= Settings::Lang('lang_xx_back', Settings::SLASH) ?>"><span><?= Settings::Lang('lang_xx_back') ?></span></a>
			</div>
			<div class="col-8">
				<h1><?= $name ?></h1>
			</div>
		</div>
		<div class="row conts">
			<div class="col-2 leftTitle">
				<p><?= Settings::Lang('contacts_xx_left') ?><p>
				<p><a class="ajax red" href="/callback"><?= Settings::Lang('callback_xx_left') ?></a><p>
				<div id="clock">
					<div id="clockT"></div>
					<div class="clockDesc"><?= Settings::Lang('contacts_xx_time') ?></div>
				</div>
			</div>
			<div class="col-8 address">
				<p><?= Settings::Lang('contacts_xx_phone') ?></p>
				<p><?= Settings::Lang('contacts_xx_email') ?></p>
				<p class="addr"><?= Settings::Lang('contacts_xx_address', Settings::TEXT) ?></p>
			</div>
		</div>
	</div>
</div>