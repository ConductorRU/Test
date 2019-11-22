<?php
use common\models\Settings;
use common\models\Slider;
/* @var $this yii\web\View */
$this->title = Settings::Lang('xx_title');
$this->registerMetaTag(['name' => 'description','content' => addslashes(Settings::Lang('xx_description'))]);
$this->registerMetaTag(['name' => 'keywords','content' => addslashes(Settings::Lang('xx_keywords'))]);
$sliders = Slider::GetAll();
$sCount = count($sliders);
if(!$sCount)
	$sliders = [null];
?>
<div class="container">
	<div class="fullPage">
		<div id="slider" class="owl-carousel owl-theme" data-count="<?= $sCount ?>">
			<?php foreach($sliders as $slider):
			?><div class="slide" style="background-image:url('<?= $slider ? $slider->getPhotoUrl() : '/img/back.png' ?>');">
				<div class="container">
					<div>
						<div class="centre">
							<div class="row">
								<div class="col-2 bottom rowL">
									<div class="titleL anim1"><?= $slider ? $slider->GetProp('xx_label') : '' ?></div>
								</div>
								<div class="col-8 bottom anim1 rowR">
									<h1><?= $slider ? $slider->GetProp('xx_desc') : '' ?></h1>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><?php endforeach ?>
		</div>
		<div id="slideControl">
			<div class="left" data-label="<?= Settings::Lang('lang_xx_prev') ?>"></div><div class="right" data-label="<?= Settings::Lang('lang_xx_next') ?>"></div>
			<span id="slideCursor"></span>
		</div>
		<div class="container mainPage">
			<div>
				<div id="sliderBar">
					<div class="row">
						<div class="col-6">
						</div>
						<div class="col-4 sliderNav anim3">
							<span class="num numL nosel active">01</span>
							<span id="sliderNav"></span>
							<span class="num numR nosel"><?= ($sCount < 10) ? ('0' . $sCount) : $sCount ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slScroll">
			<div class="container">
				<div class="row">
					<div class="col-2">
					</div>
					<div class="col-8 anim2">
						<a class="ajax" href="/projects"><img class="svg" src="/img/scroll.svg" alt="<?= Settings::Lang('lang_xx_down') ?>" /></a>
					</div>
				</div>
			</div>
		</div>
		<div id="sliderTime"><i></i></div>
	</div>
</div>