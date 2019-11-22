<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\Settings;
use common\models\Category;
use yii\helpers\Url;
AppAsset::register($this);
$isMain = (Url::toRoute(Url::home()) == Url::toRoute(Yii::$app->controller->getRoute()));
$isRed = (Yii::$app->controller->getRoute() == 'site/callback');
$path = Yii::$app->request->pathInfo;
$domain = Url::base(true) . ($path ? '/' . Yii::$app->request->pathInfo : '');
$domain = preg_replace('/\/\/([A-z]{2})\./', '//', $domain);

$socIG = Settings::Lang('xx_social_ig');
$socFB = Settings::Lang('xx_social_fb');
$sliderSpeed = (int)trim(Settings::Lang('preload_slider_speed'));
$sliderSpeed = ($sliderSpeed < 1000) ? 5000 : $sliderSpeed;
$cats = Category::GetAll();

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php $this->registerCsrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<link rel="icon" type="image/png" href="/favicon.png">
	<link rel="icon" type="image/png" href="/img/favicon-32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/img/favicon-48.png" sizes="48x48">
	<?php $this->head() ?>
	<style>
		head, body {margin:0;padding:0;}
		#preload {position:fixed;left:0;right:0;top:0;bottom:0;z-index:10000;background:#fff;}
		#preload .header {text-align:right;padding:40px 0;}
		#preload .load {text-align:center; position: absolute; top: 50%;left: 50%;transform: translate(-50%, -50%);color: #D91E1E;font-weight: 500;font-size: 72px;line-height: 115%;}
		#preload .bar {position:absolute;bottom:0;left:0;right:0;height:2px;}
		#preload .bar i {display:block;height:100%;width:0%;background:#D91E1E;}
	</style>
</head>
<body id="body" class="<?= $isMain ? '' : 'noMain' ?> <?= $isRed ? 'isRed' : '' ?>">
	<div id="preload">
		<div class="header">
			<div class="container">
				<img src="/img/logo_br.svg" alt="Loft Group" />
			</div>
		</div>
		<div class="load"><span id="preloadN">0</span>%</div>
		<div class="bar"><i></i></div>
	</div>
	<script>
		var gNum = 0;
		var gInt = 0;
		var gAll = 0;
		var isSlider = 1;
		var sliderSpeed = <?= $sliderSpeed ?>;
		function UpdatePreloader()
		{
			if(gNum > 95 && !gAll)
				gNum += 0.1;
			else if(gNum > 90 && !gAll)
				gNum += 0.5;
			else if(gNum > 80 && !gAll)
				gNum += 1;
			else if(gNum > 50 && !gAll)
				gNum += 5;
			else if(gAll)
				gNum += 15;
			else
				gNum += 10;
			if(gNum >= 100)
				gNum = 100;
			$("#preload .bar i").animate({width:(gNum + '%')}, 150);
			$("#preloadN").html(parseInt(gNum));
			if(gNum == 100)
			{
				main.AniSlider(sliderSpeed);
				$("#preload").fadeOut(300, function() {$("body").css('overflow-y', 'auto');});
				clearInterval(gInt);
				main.StartAnimate();
			}
		}
		$("body").css('overflow-y', 'hidden');
		gInt = setInterval(UpdatePreloader, 150);
		window.onload = function()
		{
			gAll = 1;
		};
	</script>
	<?php $this->beginBody() ?>
	<header>
		<div id="head">
			<div class="container">
				<div class="row">
					<div class="col-2">
						<ul class="lang">
							<?php foreach(Settings::$LANG as $k => $v):
							$langDomain = str_replace('://', '://' . $k . '.', $domain);
							if(Yii::$app->params['lang'] != $k): ?>
								<li><a class="lang ajax" href="<?= $v == 'RU' ? $domain : $langDomain ?>"><?= $v ?></a></li><?php
							else: ?><li class="active"><span class="lang"><?= $v ?></span></li><?php
							endif;
							endforeach ?>
						</ul>
					</div>
					<div class="col-6">
						<span class="menuBar" onclick="main.MenuToggle();"><i class="ar"></i><i></i></span>
					</div>
					<div class="col-2">
						<a class="ajax" href="/" id="logo" data-dir="top"><img src="/img/logo.svg" class="svg" alt="<?= addslashes(Settings::Lang('xx_title')) ?>" /></a>
					</div>
				</div>
			</div>
		</div>
		<div class="menu">
			<div class="container">
				<div class="row">
					<div class="col-2">
						<div class="social">
							<?php if($socIG):
								?><a href="<?= $socIG ?>" title="Facebook">
								<img class="svg" src="/img/social_fb.svg" alt="Facebook" />
							</a><?php endif;
							if($socFB): ?><a href="<?= $socFB ?>" title="Instagram">
								<img class="svg" src="/img/social_in.svg" alt="Instagram" />
							</a><?php endif ?>
						</div>
						<div class="made"><a href="https://brickfabrique.ru">Made by Brick Fabrique</a></div>
					</div>
					<div class="col-2">
						<nav>
							<ul class="ul">
								<li><a class="ajax" href="/bureau">Бюро</a></li>
								<li><a class="ajax" href="/projects">Проекты</a></li>
								<li><a class="ajax" href="/contacts">Контакты</a></li>
								<li><a class="red ajax" href="/calculate">Расчет стоимости</a></li>
							</ul>
						</nav>
					</div>
					<div class="col-2">
						<nav>
							<ul class="ul">
								<?php foreach($cats as $cat): ?><li><a class="ajax" href="/projects/<?= $cat->url ?>"><?= $cat->GetProp('xx_name') ?></a></li><?php endforeach ?>
							</ul>
						</nav>
					</div>
					<div class="col-4 contacts">
						<div class="title"><?= Settings::Lang('lang_xx_contacts_info') ?></div>
						<div class="phones"><a href="tel:<?= str_replace([' ', '(', ')', '-'], '', Settings::Lang('contacts_xx_phone')) ?>"><?= Settings::Lang('contacts_xx_phone') ?></a><a href="mailto:<?= Settings::Lang('contacts_xx_email') ?>"><?= Settings::Lang('contacts_xx_email') ?></a></div>
						<div class="address"><?= Settings::Lang('contacts_xx_address', Settings::TEXT) ?></div>
					</div>
				</div>	
			</div>
		</div>
	</header>
	<div id="fadeMask"></div>
	<div class="wrap">
		<div class="container"><?= Alert::widget() ?></div>
		<div id="main"><?= $content ?></div>
	</div>

	<footer>
		<div class="container">
			<div class="row">
				<div class="col-2"><a id="callRed" class="ajax red" data-dir="ring" href="/callback"><i></i><span><?= Settings::Lang('lang_xx_callback') ?></span></a></div>
				<div class="col-1 anim2"><?= Settings::Lang('contacts_xx_phone') ?></div>
				<div class="col-1 anim2"><?= Settings::Lang('contacts_xx_email') ?></div>
				<div class="col-6 social anim2">
					<?php if($socIG):
						?><a href="<?= $socIG ?>" title="Facebook">
						<img class="svg" src="/img/social_fb.svg" alt="Facebook" />
					</a><?php endif;
					if($socFB): ?><a href="<?= $socFB ?>" title="Instagram">
						<img class="svg" src="/img/social_in.svg" alt="Instagram" />
					</a><?php endif ?>
				</div>
			</div>
		</div>
	</footer>
	<div id="ajaxFade"></div>
	<div id="gallery">
		<div class="gMain"></div>
		<div class="gControl">
			<div></div>
			<div></div>
			<i onclick="main.GalleryClose();"></i>
		</div>
	</div>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
