<?php
namespace frontend\controllers;
use yii\web\NotFoundHttpException;
use common\models\Pack;
use Yii;

class CatalogController extends BaseController
{
	public function behaviors()
	{
		return [];
	}
	public function actions()
	{
		return ['error' => ['class' => 'yii\web\ErrorAction'],];
	}
	public function actionIndex($type = null)
	{
		$selType = null;
		$iType = null;
		$types = [];
		foreach(Pack::$TYPES as $k => $v)
		{
			$cnt = Pack::GetCatalogQuery($k)->count();
			if($cnt)
				$types[$k] = ['name' => $v[0], 'url' => $v[1], 'count' => $cnt];
			if($v[1] == $type)
			{
				$selType = $type;
				$iType = $k;
			}
		}
		$q = Pack::GetCatalogQuery($iType);		
		$packs = Pack::GetCatalog($q);

		$count = Pack::GetCatalogQuery($iType)->count();
		$allCount = Pack::GetCatalogQuery()->count();
		return $this->render('index', ['packs' => $packs, 'count' => $count, 'allCount' => $allCount, 'types' => $types, 'selType' => $selType]);
	}
	public function actionItem($url)
	{
		$pack = Pack::find()->where(['url' => $url, 'status' => 1])->one();
		if(!$pack)
			throw new NotFoundHttpException('Пакет не найден');
		$childs = count($pack->packs) ? $pack->packs : [$pack];
		$packs = [];
		$firstDesc = null;
		$n = 0;
		foreach($childs as $child)
		{
			$price = $child->GetRealPrice();
			$packs[$n] = ['price_from' => $price, 'price_to' => 0, 'pack' => $child, 'prods' => [], 'desc' => ''];
			if($n > 0)
				$packs[$n - 1]['price_to'] = $price;
			++$n;
		}
		$n = 0;
		$prodCount = 0;
		$allProds = [];
		foreach($childs as $child)
		{
			$prods = array_merge($child->iproducts, $child->products);
			$prodCount += count($prods);
			$packs[$n]['prods'] = $prods;
			foreach($prods as $prod)
			{
				if($packs[$n]['desc'] != '')
					$packs[$n]['desc'] .= ', ';
				$packs[$n]['desc'] .= $prod->name;
				$allProds[$prod->id] = $prod;
			}
			if($firstDesc === null)
				$firstDesc = $packs[$n]['desc'];
			++$n;
		}
		if(!$prodCount)
			throw new NotFoundHttpException('Ошибка: в пакете отсутствуют товары');
		return $this->render('item', ['pack' => $pack, 'packs' => $packs, 'prods' => $allProds, 'childs' => $childs, 'firstDesc' => $firstDesc]);
	}
}
