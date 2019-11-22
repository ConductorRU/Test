<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css =
	[
		'fonts/TTNorms/stylesheet.css',
		'css/bootstrap-reboot.min.css',
		'css/bootstrap-grid.css?1',
		'css/owl.carousel.min.css',
		'css/owl.theme.default.min.css',
	];
	public $js =
	[
		'js/bootstrap.min.js',
		'js/jquery.maskedinput.min.js',
		'js/owl.carousel.min.js',
	];
	public $depends =
	[
		'yii\web\YiiAsset',
		//'yii\bootstrap\BootstrapAsset',
	];
	public function init()
	{
		$t = time();
		//$t = 1;
		parent::init();
		$this->js[] = 'js/main.js?' . $t;
		$this->css[] = 'css/site.css?' . $t;
	}
}
