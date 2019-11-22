<?php
namespace common\models;
use Yii;

class Lang extends \yii\db\ActiveRecord
{
	public $props = [];
	public function rules()
	{
		return
		[
			['props', 'each', 'rule' => ['string']],
		];
	}
	public function GetProp($name, $mod = 0)
	{
		$lang = Yii::$app->params['lang'];
		$res = '';
		$n = str_replace('xx', $lang, $name);
		if($n == 'ru_name')
			$res = $this->name;
		else if(isset($this->props[$n]) && $this->props[$n] != '')
			$res = $this->props[$n];
		else if($lang != 'ru')
		{
			$n = str_replace('xx', 'ru', $name);
			if($n == 'ru_name')
				$res = $this->name;
			else if(isset($this->props[$n]))
				$res = $this->props[$n];
		}
		if($mod & Settings::META && $res == '')
		{
			if(substr_count($name, 'keys'))
				return Settings::Lang('xx_keywords', Settings::SLASH);
			if(substr_count($name, 'desc'))
				return Settings::Lang('xx_description', Settings::SLASH);
		}
		if($res != '')
		{
			if($mod & Settings::TEXT)
				$res = str_replace("\n", '<br />', $res);
			if($mod & Settings::SLASH)
				$res = addslashes($res);
		}
		return $res;
	}
	public function beforeSave($insert)
	{
		if(parent::beforeSave($insert))
		{
			$t = [];
			foreach($this->props as $k => $prop)
			{
				$prop = preg_replace('/(<!--)(.*?)(-->)/su', '', $prop);
				$t[$k] = $prop;
			}
			$this->lang = serialize($t);
			return true;
		}
		return false;
	}
	public function afterFind()
	{
    parent::afterFind();
		if($this->lang)
			$this->props = unserialize($this->lang);
	}
}
