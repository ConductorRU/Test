<?php
	use common\models\Settings;
	foreach($projects as $proj):
		$cat = $proj->GetCategory()->one();
		?>
		<a class="item ajax" data-dir="right" href="<?= $proj->GetUrl() ?>">
			<div class="name"><?= $proj->GetProp('xx_name', Settings::TEXT) ?></div>
			<div class="desc"><?= $proj->year ?><br/><?= $cat ? $cat->GetProp('xx_name') : '' ?></div>
			<div class="img"><img src="<?= $proj->GetPreviewUrl(true) ?>" alt="" /><i></i></div>
		</a>
	<?php endforeach ?>