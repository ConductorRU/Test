<?php
namespace common\widgets;
use Yii;
use common\widgets\assets\WidgetAsset;
class ImageWidget extends \yii\bootstrap\Widget
{
	public $images = [];
	public $model;
	public $attribute;
	public $count = 1;
	public $max_width = 0;
	public $max_height = 0;
	public $size = 100;
	public $aspect = 1;
	public $isCover = 0;
	public function init()
	{
		parent::init();
		WidgetAsset::register(Yii::$app->getView());
	}
	public function run()
	{
		return $this->render('imageupload', ['model' => $this->model, 'count' => $this->count, 'width' => $this->max_width, 'isCover' => $this->isCover, 'height' => $this->max_height, 'size' => $this->size, 'aspect' => $this->aspect, 'attribute' => $this->attribute, ]);
	}
}