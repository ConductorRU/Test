<?php

namespace common\widgets\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class WidgetAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'/css/widget.css?1',
	];
	public $js = [
		'/js/widget.js?1',
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
