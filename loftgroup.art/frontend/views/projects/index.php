<?php
	use common\models\Settings;
	use common\models\Category;
	$this->registerMetaTag(['name' => 'description','content' => Settings::Lang('projects_xx_meta_desc', Settings::META | Settings::SLASH)]);
	$this->registerMetaTag(['name' => 'keywords','content' => Settings::Lang('projects_xx_meta_keys', Settings::META | Settings::SLASH)]);
	$cats = Category::GetList();
	$name = Settings::Lang('projects_xx_name');
	$catId = (int)Yii::$app->request->get('category_id');
	if(!$catId)
		$catId = (int)Yii::$app->request->post('category_id');
	$this->title = $name . ' - Loft Group';
?>
<div class="container">
	<div id="projects" class="projects" data-more="<?= $more ?>" data-offset="<?= $offset ?>" data-category="<?= $catId ?>">
		<div class="row">
			<div class="col-2">

			</div>
			<div class="col-8">
				<div class="projDesc"><?= Settings::Lang('projects_xx_desc', Settings::TEXT) ?></div>
			</div>
		</div>
		<div class="row  align-items-end">
			<div class="col-2">
				<a class="pgBack ajax" data-dir="left" href="/"><img class="svg" src="/img/arrow_back.svg" alt="<?= addslashes(Settings::Lang('lang_xx_back')) ?>"><span><?= Settings::Lang('lang_xx_back') ?></span></a>
			</div>
			<div class="col-8">
				<h1><?= $name ?></h1>
			</div>
		</div>
		<div class="row ">
			<div class="col-2"></div>
			<div class="col-8">
				<ul class="ul projTypes">
					<li <?= $catId ? '' : 'class="active"' ?>><a class="ajax" href="?"><?= Settings::Lang('projects_xx_all') ?></a></li>
					<?php foreach($cats as $k => $cat): ?><li <?= ($catId == $k) ? 'class="active"' : '' ?>><a class="ajax" href="?category_id=<?= $k ?>"><?= $cat ?></a></li> <?php endforeach ?>
				</ul>
			</div>
		</div>
		<div class="projList">
			<div class="row">
				<div class="col-2"></div>
				<div class="col-3"><div id="projL"></div></div>
				<div class="col-1"></div>
				<div class="col-4"><div id="projR"></div></div>
			</div>
			<div class="row">
				<div class="col-2"></div>
				<div class="col-8">
					<div class="more"><?= Settings::Lang('projects_xx_more') ?></div>
				</div>
			</div>
		</div>
		<div id="projAll">
			<?= $this->render('_items', ['projects' => $projects]); ?>
		</div>
	</div>
</div>