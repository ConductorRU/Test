<?php
namespace frontend\controllers;
use Yii;
use common\models\Category;
use common\models\Project;
use yii\db\Expression;

class ProjectsController extends BehaviourController
{
	public static $limit = 4;
	public function actionIndex()
	{
		$category_id = (int)Yii::$app->request->post('category_id');
		if(!$category_id)
			$category_id = (int)Yii::$app->request->get('category_id');
		$q = Project::find()->where(['status' => 1]);
		if($category_id)
			$q->andWhere(['category_id' => $category_id]);
		$projects = $q->limit(static::$limit)->orderBy('sort, id DESC')->all();
		$count = $q->count();
		$more = (static::$limit < $count) ? 1 : 0;
		if(Yii::$app->request->isAjax)
			return $this->getJSON('index', ['projects' => $projects, 'offset' => static::$limit, 'more' => $more]);
		return $this->render('index', ['projects' => $projects, 'offset' => static::$limit, 'more' => $more]);
	}
	public function actionMore()
	{
		$offset = (int)Yii::$app->request->post('offset');
		$category_id = (int)Yii::$app->request->post('category_id');
		$q = Project::find()->where(['status' => 1]);
		if($category_id)
			$q->andWhere(['category_id' => $category_id]);
		$projects = $q->limit(static::$limit)->offset($offset)->orderBy('sort, id DESC')->all();
		$count = $q->count();
		$offset += static::$limit;
		$more = ($offset < $count) ? 1 : 0;
		if(Yii::$app->request->isAjax)
		{
			$res = $this->getJSON('_items', ['projects' => $projects]);
			$res['offset'] = $offset;
			return $res;
		}
		return $this->render('_items', ['projects' => $projects]);
	}
	public function actionItem($url)
	{
		$project = Project::find()->where(['url' => $url, 'status' => 1])->one();
		if(!$project)
			throw new \yii\web\NotFoundHttpException();
		$pPrev = null;
		$pNext = null;
		$q = Project::find()->where(['status' => 1]);
		$projects = $q->orderBy('sort, id DESC')->all();
		$n = 0;
		foreach($projects as $p)
		{
			if($p->id == $project->id)
				break;
			++$n;
		}
		if($n > 0)
			$pPrev = $projects[$n - 1];
		if($n + 1 < count($projects))
			$pNext = $projects[$n + 1];
		if(Yii::$app->request->isAjax)
			return $this->getJSON('item', ['project' => $project, 'pPrev' => $pPrev, 'pNext' => $pNext]);
		return $this->render('item', ['project' => $project, 'pPrev' => $pPrev, 'pNext' => $pNext]);
	}
	public function actionCategory($category)
	{
		$cat = Category::find()->where(['url' => $category, 'status' => 1])->one();
		if($cat)
		{
			$projects = $cat->getProjects()->where(['status' => 1])->orderBy(new Expression('rand()'))->limit(3)->all();
			if(Yii::$app->request->isAjax)
				return $this->getJSON('category', ['cat' => $cat, 'projects' => $projects]);
			return $this->render('category', ['cat' => $cat, 'projects' => $projects]);
		}
		else
			throw new \yii\web\NotFoundHttpException();
	}
}
