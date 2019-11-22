<?php
	use common\models\Settings;
	use common\models\Image;
	$this->registerMetaTag(['name' => 'description','content' => $project->GetProp('xx_meta_desc', Settings::META)]);
	$this->registerMetaTag(['name' => 'keywords','content' => $project->GetProp('xx_meta_keys', Settings::META)]);
	$name = $project->GetProp('xx_name');
	$cat = $project->GetCategory()->one();
	$lg = Yii::$app->params['lang'];
	$photos = [];
	if(isset($project->props['photos']))
		$photos = json_decode($project->props['photos']);
	$this->title = addslashes(strip_tags($name)) . ' - Loft Group';
?>
<div class="container">
	<div id="project" class="projects">
		<div class="row align-items-end">
			<div class="col-2">
				<a class="pgBack ajax" data-dir="left" href="/projects"><img class="svg" src="/img/arrow_back.svg" alt="Назад"><span>Назад</span></a>
			</div>
			<div class="col-8">
				<h1><?= $project->GetProp('xx_name', Settings::TEXT) ?></h1>
			</div>
		</div>
		<div>
			<div class="row">
				<div class="col-2">
					
				</div>
				<div class="col-8">
					<div class="subTitle">
						<?= $project->year ?><br><?= $cat ? $cat->GetProp('xx_name') : '' ?>
					</div>
					<?php if($project->photo_id): ?>
					<div class="mainPhoto">
						<?php if($project->file_id): ?>
						<a href="<?= $project->GetFileUrl() ?>" target="_blank">
							<img src="<?= $project->GetPhotoUrl() ?>" alt="" />
							<i>PDF</i>
						</a>
						<?php else: ?>
							<img src="<?= $project->GetPhotoUrl() ?>" alt="" />
						<?php endif ?>
					</div>
					<?php endif ?>
				</div>
			</div>
		</div>
		<div class="info">
			<div class="row">
				<div class="col-2">
					<div class="leftTitle">Информация о проекте:</div>
				</div>
				<div class="col-8">
					<?= $project->GetProp('xx_info') ?>
				</div>
			</div>
		</div>
		<div class="info">
			<div class="row">
				<div class="col-2">
					<div class="leftTitle">Описание проекта:</div>
				</div>
				<div class="col-8">
					<div class="desc"><?= $project->GetProp('xx_desc') ?></div>	
				</div>
			</div>
		</div>
		<div class="projList projPhotos">
			<div class="row">
				<div class="col-2"></div>
				<div class="col-3">
					<?php $i = 0; foreach($photos as $photo):
						if($i%2 == 1)
						{
							++$i;
							continue;
						}
						$img = Image::findOne($photo->photo);
						if(!$img)
						{
							++$i;
							continue;
						}
					?>
						<div class="item">
							<div class="name"><?= $photo->name->$lg ? $photo->name->$lg : $photo->name->ru ?></div>
							<div class="img"><img src="<?= $img->GetUrl() ?>" alt="" /><i></i></div>
						</div>
					<?php ++$i; endforeach ?>
				</div>
				<div class="col-1">
					
				</div>
				<div class="col-4">
					<?php $i = 0; foreach($photos as $photo):
						if($i%2 == 0)
						{
							++$i;
							continue;
						}
						$img = Image::findOne($photo->photo);
						if(!$img)
						{
							++$i;
							continue;
						}
					?>
						<div class="item">
							<div class="name"><?= $photo->name->$lg ? $photo->name->$lg : $photo->name->ru ?></div>
							<div class="img"><img src="<?= $img->GetUrl() ?>" alt="" /><i></i></div>
						</div>
					<?php ++$i; endforeach ?>
				</div>
			</div>
		</div>
		<div class="info">
			<div class="row">
				<div class="col-2">
					
				</div>
				<div class="col-8">
					<div class="row">
						<div class="col-5">
							<?php if($pPrev): ?><a class="pgBack ajax" data-dir="left" href="<?= $pPrev->GetUrl() ?>"><img class="svg" src="/img/arrow_back.svg" alt="<?= addslashes(Settings::Lang('lang_xx_back')) ?>"><span><?= $pPrev->GetProp('xx_name') ?></span></a><?php endif ?>
						</div>
						<div class="col-5 right">
						<?php if($pNext): ?><a class="pgBack pgBackR ajax" data-dir="right" href="<?= $pNext->GetUrl() ?>"><span><?= $pNext->GetProp('xx_name') ?></span><img class="svg" src="/img/arrow_next.svg" alt="<?= addslashes(Settings::Lang('lang_xx_forward')) ?>"></a><?php endif ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>