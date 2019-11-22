<?php

namespace frontend\models;
use yii\web\UrlRule;
use common\models\Category;

class CatUrlRule extends UrlRule
{
	public $connectionID = 'db';
	public $pattern = 'car';
	public $route = 'car';

	public function createUrl($manager, $route, $params)
	{
		if ($route === 'catalog/index') 
		{
			if(isset($params['cat']))
			{
				$p = $params['cat']->getPath();
				unset($params['cat']);
				$q = http_build_query($params);
				return $p . '?' . $q;
			}
		}
		return false;  // this rule does not apply
	}
	public function parseRequest($manager, $request)
	{
		$pathInfo = $request->getPathInfo();
		$cat = Category::find()->where(['url' => $pathInfo, 'status' => 1])->one();
		if($cat)
			return ['projects/category', ['category_id' => $cat->id]];
		return false;
	}
}