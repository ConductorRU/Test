<?php
	use common\models\Settings;
	$this->registerMetaTag(['name' => 'description','content' => $cat->GetProp('xx_meta_desc', Settings::META)]);
	$this->registerMetaTag(['name' => 'keywords','content' => $cat->GetProp('xx_meta_keys', Settings::META)]);

	$name = $cat->GetProp('xx_name');
	$this->title = addslashes(strip_tags($name)) . ' - Loft Group';
	$img = $cat->getPhoto()->one();
	$stages = null;
	if(isset($cat->props['stages']) && $cat->props['stages'])
		$stages = json_decode($cat->props['stages']);
	$list = null;
	if(isset($cat->props['list']) && $cat->props['list'])
		$list = json_decode($cat->props['list']);	
?>
<div class="pages">
	<div class="container">
		<div class="projects">
			<div class="row align-items-end">
				<div class="col-2">
					<a class="pgBack ajax" data-dir="left" href="/"><img class="svg" src="/img/arrow_back.svg" alt="<?= addslashes(Settings::Lang('lang_xx_back')) ?>"><span><?= Settings::Lang('lang_xx_back') ?></span></a>
				</div>
				<div class="col-8">
					<h1><?= $name ?></h1>
				</div>
			</div>
		</div>
	</div>
	<?php if($img): ?>
	<div class="imgWide">
		<img src="<?= $img->GetUrl() ?>" alt="" />
	</div>
	<?php endif ?>
	<div class="container">
		<div class="projects">
			<div class="info">
				<div class="row">
					<div class="col-2">
						<div class="leftTitle"><?= $cat->GetProp('xx_desc_name'); ?></div>
					</div>
					<div class="col-8">
						<div class="desc">
							<?= $cat->GetProp('xx_desc'); ?>
						</div>
						
					</div>
				</div>
			</div>
			<?php if($stages && count($stages)): ?>
			<div class="info stages">
				<div class="row">
					<div class="col-2">
						<div class="leftTitle"><?= $cat->GetProp('xx_stage_name'); ?></div>
					</div>
					<?php $i = 0; foreach($stages as $stage):
						$sDesc = Settings::GetProp($stage, 'desc');
						?>
						<div class="col-2 item">
							<div class="num"><?= $stage->num ?></div>
							<div class="name"><span><?= Settings::GetProp($stage, 'name') ?></span><?php if($sDesc): ?><i data-id="<?= $stage->num ?>"><img src="/img/tips.svg" alt="!" /></i><?php endif ?></div>
							<div class="desc"><?= $sDesc ?></div>
						</div>
						<?php if($i%4 == 3): ?><div class="col-2"></div><?php endif ?>
					<?php ++$i; endforeach ?>
				</div>
				<div id="stageDesc"></div>
			</div>
			<?php endif ?>
			<?php if($list && count($list)): ?>
			<div class="info infoSpoiler">
				<div class="row">
					<div class="col-2">
						<div class="leftTitle"><?= $cat->GetProp('xx_list_name'); ?></div>
						<ul class="ul ulRef stick">
							<li><a href="#">Состав исходно-разрешительной документации</a></li>
							<li><a href="#">Состав разделов проектной документации</a></li>
							<li><a href="#">Проектная документация на линейные объекты</a></li>
							<li><a href="#">Письмо Минрегиона от 22.06.2009 № 19088-СК/08</a></li>
						</ul>
					</div>
					<div class="col-8">
						<div class="spoilers">
							<?php foreach($list as $li):
								$lDesc = Settings::GetProp($li, 'desc');
								?>
							<div class="item">
								<div class="name nosel <?= $lDesc ? '' : 'dis' ?>"><span><i><img src="/img/plus.svg" alt="+" /></i><span><?= Settings::GetProp($li, 'name') ?></span></span></div>
								<div class="text"><?= $lDesc ?></div>
							</div>
							<?php endforeach ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif ?>
			<?php if(count($projects)): ?>
			<div class="info works">
				<div class="row">
					<div class="col-2">
						<div class="leftTitle"><?= $cat->GetProp('xx_projects_name'); ?></div>
					</div>
					<?php $i = 0; foreach($projects as $proj): ?>
						<?php if($i%3 == 0 && $i > 2): ?>
						<div class="col-1"></div>
						<?php endif ?>
						<div class="col-2">
							<div class="item">
								<div class="name"><a class="ajax" href="<?= $proj->GetUrl() ?>"><?= $proj->GetProp('xx_name') ?></a></div>
								<div class="desc"><?= $proj->year ?><br><?= $proj->GetCategoryName() ?></div>
								<div class="img"><a class="ajax img" href="<?= $proj->GetUrl() ?>" style="background-image:url('<?= $proj->GetPreviewUrl() ?>');"></a></div>
							</div>
						</div>
						<?php if($i%3 != 2): ?>
						<div class="col-1"></div>
						<?php endif ?>
					<?php ++$i; endforeach ?>
				</div>
			</div>
			<?php endif ?>
		</div>
	</div>
</div>