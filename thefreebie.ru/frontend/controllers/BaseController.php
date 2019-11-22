<?php
namespace frontend\controllers;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class BaseController extends Controller
{
	public function beforeAction($action)
	{
		$user = Yii::$app->user->identity;
		if($user)
		{
			$user->visited_at = date('Y-m-d H:i:s');
			$user->update();
		}
		return parent::beforeAction($action);
	}
	public function actions()
	{
		return [
			'error' => ['class' => 'yii\web\ErrorAction'],
			'captcha' =>
			[
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}
}
