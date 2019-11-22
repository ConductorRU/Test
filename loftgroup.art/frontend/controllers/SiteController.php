<?php
namespace frontend\controllers;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SiteController extends BehaviourController
{
	public function actionIndex()
	{
		if(Yii::$app->request->isAjax)
			return $this->getJSON('index');
		return $this->render('index');
	}
	public function actionBureau()
	{
		if(Yii::$app->request->isAjax)
			return $this->getJSON('bureau');
		return $this->render('bureau');
	}
	public function actionContacts()
	{
		if(Yii::$app->request->isAjax)
			return $this->getJSON('contacts');
		return $this->render('contacts');
	}
	public function actionLogout()
	{
		Yii::$app->user->logout();
		return $this->goHome();
	}
	public function actionCallback()
	{
		if(Yii::$app->request->isAjax)
			return $this->getJSON('callback');
		return $this->render('callback');
	}
	public function actionCalculate()
	{
		if(Yii::$app->request->isAjax)
			return $this->getJSON('calculate');
		return $this->render('calculate');
	}
}
