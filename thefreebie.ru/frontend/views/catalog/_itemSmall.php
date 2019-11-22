<?php

?>
<div class="card">
	<div class="title">
		<div class="type"><img src="<?= $item->getTypeUrl() ?>" alt="" /></div>
		<div class="name"><?= $item->name ?></div>
	</div>
	<div class="image">
		<div class="filter">
			<div class="img" style="background-image:url('<?= $item->getSmallUrl(true) ?>');"></div>
		</div>
		<div class="info">
			<div class="infoTitle">Плати сколько хочешь</div>
			<div class="desc"><?= $item->getDescHtml(false) ?></div>
			<div class="price"><?= $item->getPriceHtml() ?></div>
		</div>
	</div>
	<a class="detail but" href="<?= $item->GetUrl() ?>">Подробнее</a>
</div>