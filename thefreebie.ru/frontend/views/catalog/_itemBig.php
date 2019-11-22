<?php

?>
<div class="card big">
	<div class="row title">
		<div class="col-lg-8 col-md-16 col-xm-8 col-xs-16">
			<div class="type"><img src="<?= $item->getTypeUrl() ?>" alt="" /></div>
			<div class="name"><?= $item->name ?></div>
		</div>
		<?php
		if($item->discount_to):
		$t = strtotime($item->discount_to);
		if($t + 60*60*24 > time()):
		?>
		<div class="col-xs-8 discountTime">
			<div class="remain">Действительно до:</div>
			<div class="date"><span><?= date('d', $t) ?></span><span><?= date('m', $t) ?></span><span><?= date('y', $t) ?></span></div>
		</div>
		<?php endif; endif ?>
	</div>
	<div class="image">
		<div class="filter">
			<div class="img" style="background-image:url('<?= $item->getImageUrl(true) ?>');"></div>
		</div>
		<div class="info">
			<div class="infoTitle">Плати сколько хочешь</div>
			<div class="desc"><?= $item->getDescHtml(true) ?></div>
			<ul class="ulClear opts">
			<?php foreach($item->packs as $pack):
			?><li><a href="<?= $item->GetUrl() ?>#set_<?= $pack->id ?>"><?= $pack->getNameHtml() ?></a></li><?php
			endforeach ?>
			</ul>
			<div class="subscribe">Или <span>оформи</span> подписку по 450 руб. в месяц для того чтобы получать подобные пакеты еженедельно</div>
		</div>
	</div>
	<a class="detail but" href="<?= $item->GetUrl() ?>">Подробнее</a>
</div>