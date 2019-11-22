<?php
namespace common\widgets;
use Yii;
use common\widgets\assets\WidgetAsset;
class FileUploadWidget extends \yii\bootstrap\Widget
{
	public $id = '';
	public $model;
	public $attribute;
	public $max_count = 100;
	public $accept = '';
	public $select = false;
	public $uploadUrl = '/upload-file';
	public function init()
	{
		parent::init();
		WidgetAsset::register(Yii::$app->getView());
	}
	public function run()
	{
		return $this->render('fileupload', ['id' => $this->id, 'model' => $this->model, 'max_count' => $this->max_count, 'select' => $this->select, 'attribute' => $this->attribute, 'accept' => $this->accept, 'uploadUrl' => $this->uploadUrl]);
	}
}