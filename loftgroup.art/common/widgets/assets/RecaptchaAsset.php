<?php
namespace common\widgets\assets;
use yii\web\AssetBundle;
use common\models\Setting;
/**
 * Main frontend application asset bundle.
 */
class RecaptchaAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [];
	public $js = [];
	public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
	public function init()
	{
		parent::init();
		$type = (int)Setting::GetValue('captcha_type');
		if($type == 1)
			$this->js[] = 'https://www.google.com/recaptcha/api.js';
	}
}
