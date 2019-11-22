<?php
	use common\models\Settings;
	$this->registerMetaTag(['name' => 'description','content' => Settings::Lang('calс_xx_meta_desc', Settings::META | Settings::SLASH)]);
	$this->registerMetaTag(['name' => 'keywords','content' => Settings::Lang('calс_xx_meta_keys', Settings::META | Settings::SLASH)]);
	$name = Settings::Lang('calc_xx_name');
	$this->title = $name . ' - Loft Group';
	$agree = str_replace('#AGREE#', '<span>' . Settings::Lang('calc_xx_agree_word') . '</span>', Settings::Lang('calc_xx_agree'));
?>
<div id="calculate" class="container">
	<div class="projects">
		<div class="row align-items-end">
			<div class="col-2">
				<a class="pgBack ajax" data-dir="left" href="/"><img class="svg" src="/img/arrow_back.svg" alt="<?= Settings::Lang('lang_xx_back', Settings::SLASH) ?>"><span><?= Settings::Lang('lang_xx_back') ?></span></a>
			</div>
			<div class="col-8">
				<h1><?= $name ?></h1>
			</div>
		</div>
		<div class="form">
			<div class="row">
				<div class="col-2"></div>
				<div class="col-2"><div class="field"><input type="text" autocomplete="off" name="company" placeholder="<?= Settings::Lang('calc_xx_form_company', Settings::SLASH) ?>*" /></div></div>
				<div class="col-1"></div>
				<div class="col-2"><div class="field"><input type="text" autocomplete="off" name="name" placeholder="<?= Settings::Lang('calc_xx_form_name', Settings::SLASH) ?>*" /></div></div>
				<div class="col-1"></div>
				<div class="col-2"><div class="field"><input type="text" autocomplete="off" name="phone" placeholder="<?= Settings::Lang('calc_xx_form_phone', Settings::SLASH) ?>*" /></div></div>
			</div>
			<div class="row">
				<div class="col-2"></div>
				<div class="col-8">
					<div class="dir"><?= Settings::Lang('calc_xx_category') ?></div>
					<ul class="ul dir flOpt">
						<li><label><input type="checkbox" name="dir" value="Проектирование"><span>Проектирование</span></label></li>
						<li><label><input type="checkbox" name="dir" value="Дизайн"><span>Дизайн</span></label></li>
						<li><label><input type="checkbox" name="dir" value="Ремонт"><span>Ремонт</span></label></li>
						<li><label><input type="checkbox" name="dir" value="Строительство"><span>Строительство</span></label></li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-2"></div>
				<div class="col-2"><div class="field"><input type="text" autocomplete="off" name="square" placeholder="<?= Settings::Lang('calc_xx_form_square', Settings::SLASH) ?>" /></div></div>
				<div class="col-1"></div>
				<div class="col-2"><div class="field"><input type="text" autocomplete="off" name="city" placeholder="<?= Settings::Lang('calc_xx_form_city', Settings::SLASH) ?>" /></div></div>
				<div class="col-3"></div>
			</div>
			<div class="row">
				<div class="col-2"></div>
				<div class="col-8"><div class="field"><input type="text" autocomplete="off" name="comment" placeholder="<?= Settings::Lang('calc_xx_form_comment', Settings::SLASH) ?>" /></div></div>
			</div>
			<div class="row attach">
				<div class="col-2"></div>
				<div class="col-8">
					<div class="attachT"><?= Settings::Lang('calc_xx_file') ?></div>
					<div class="attachI"><input type="file" id="attach" name="file" /><label for="attach"><img class="svg" src="/img/attach.svg" alt="" /><span><?= Settings::Lang('calc_xx_attach') ?></span></label></div>
					<div class="send"><span class="pgBack pgBackR pgBackRed" data-dir="right" href="/calculate"><span><?= Settings::Lang('calc_xx_send') ?></span><img class="svg" src="/img/arrow_next.svg" alt="<?= Settings::Lang('calc_xx_send', Settings::SLASH) ?>"></span></div>
					<div class="agree"><?= $agree ?></div>
				</div>
			</div>
		</div>
	</div>
</div>