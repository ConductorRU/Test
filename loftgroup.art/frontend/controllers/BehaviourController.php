<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Url;
use common\models\Settings;

class BehaviourController extends Controller
{
	public function getJSON($view, $params = [])
	{
		$res = [];
		$res['html'] = $this->renderPartial($view, $params);
		$res['title'] = Yii::$app->view->title;
		return $res;
	}
	public function behaviors()
	{
			return [
					'access' => [
							'class' => AccessControl::className(),
							'only' => ['logout'],
							'rules' => [
									[
											'actions' => ['logout'],
											'allow' => true,
											'roles' => ['@'],
									],
							],
					],
					'verbs' => [
							'class' => VerbFilter::className(),
							'actions' => [
									'logout' => ['post'],
							],
					],
			];
	}

	public function actions()
	{
		return
		[
			'error' =>
			[
					'class' => 'yii\web\ErrorAction',
			]
		];
	}
	public function beforeAction($action)
	{
		$domain = Url::base(true);
		$keys = array_keys(Settings::$LANG);
		$lang = 'ru';
		$m = [];
		if(preg_match('/\/\/([A-z]{2})\./', $domain, $m) && in_array($m[1], $keys))
			$lang = $m[1];
		Yii::$app->params['lang'] = $lang;
		$this->enableCsrfValidation = false;
		if(Yii::$app->request->isAjax)
			Yii::$app->response->format = Response::FORMAT_JSON;
		if(!Yii::$app->user->identity && (int)Settings::getValue('maintenance'))
			$this->layout = 'maintenance';
		return parent::beforeAction($action);
	}
}
