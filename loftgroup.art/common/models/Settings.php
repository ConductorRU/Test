<?php
namespace common\models;
use Yii;
/**
 * This is the model class for table "setting".
 *
 * @property string $name
 * @property string $value
 */
class Settings extends \yii\db\ActiveRecord
{
	const META = 1;
	const TEXT = 2;
	const SLASH = 4;
	public static $temp = null;
	public static $editOpts = ['preset' => 'custom', 'clientOptions' =>
	[
		'toolbarGroups' =>
		[
			['name' => 'undo'],
			['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
			['name' => 'colors'],
			['name' => 'paragraph', 'groups' => ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']],
			['name' => 'links', 'groups' => ['links', 'insert']],
		],
	]];
	public $props = [];
	static $LANG = ['ru' => 'RU', 'en' => 'EN'];
	public static function tableName()
	{
		return 'settings';
	}
	public function rules()
	{
		return [
			[['name', 'value'], 'required'],
			[['name'], 'string', 'max' => 255],
			['value', 'string', 'max' => 10000],
			[['name'], 'unique'],
			['props', 'each', 'rule' => ['string']],
		];
	}
	public function attributeLabels()
	{
		return [
			'name' => 'Имя',
			'value' => 'Значение',
		];
	}
	public static function findByName($name)
	{
		return static::findOne(['name' => $name]);
	}
	public static function getValue($name)
	{
		$v = static::findOne(['name' => $name]);
		if($v)
			return $v->value;
		return '';
	}
	public static function getValues()
	{
		$res = [];
		$s = static::find()->all();
		foreach($s as $v)
			$res[$v->name] = $v->value;
		return $res;
	}
	public static function Lang($name, $mod = 0)
	{
		if(!static::$temp)
		{
			static::$temp = [];
			$s = static::find()->all();
			foreach($s as $v)
				static::$temp[$v->name] = $v->value;
		}
		$res = '';
		$n = str_replace('xx', Yii::$app->params['lang'], $name);
		if(isset(static::$temp[$n]) && static::$temp[$n] != '')
			$res = static::$temp[$n];
		else if(Yii::$app->params['lang'] != 'ru')
		{
			$n = str_replace('xx', 'ru', $name);
			if(isset(static::$temp[$n]))
				$res = static::$temp[$n];
		}
		if($mod & static::META && $res == '')
		{
			if(substr_count($res, 'keys'))
				return static::Lang('xx_keywords', Settings::SLASH);
			if(substr_count($res, 'desc'))
				return static::Lang('xx_description', Settings::SLASH);
		}
		if($res != '')
		{
			if($mod & static::TEXT)
				$res = str_replace("\n", '<br />', $res);
			if($mod & static::SLASH)
				$res = addslashes($res);
		}
		return $res;
	}
	public static function getTextValue($name)
	{
		return str_replace("\n", '<br />', static::getValue($name));
	}
	public static function GetMeta()
	{
		$a = [];
		for($i = 1; $i <= 10; ++$i)
		{
			$n = trim(Settings::getValue('meta_n' . $i));
			$v = trim(Settings::getValue('meta_v' . $i));
			if($n != '' && $v != '')
				$a[$n] = $v;
		}
		return $a;
	}
	public static function SetValue($name, $value)
	{
		$value = (string)$value;
		$v = static::findOne(['name' => $name]);
		if($v)
		{
			$v->value = $value;
			if($value === '' || $value === null)
				$v->delete();
			else
				$v->update();
			return;
		}
		else if($value === '' || $value === null)
			return;
		$v = new Settings;
		$v->name = $name;
		$v->value = $value;
		$v->save();
	}
	public static function GetProp($model, $prop)
	{
		$lg = Yii::$app->params['lang'];
		return (isset($model->$prop->$lg) && $model->$prop->$lg) ? $model->$prop->$lg : $model->$prop->ru;
	}
	public static function getImageUrl($name)
	{
		$v = static::getValue($name);
		if($v)
		{
			$img = Image::find()->where(['id' => $v])->one();
			if($img)
				return $img->GetUrl();
		}
		return '';
	}
	public function loadForm()
	{
		$all = static::find()->all();
		foreach($all as $a)
			$this->props[$a->name] = $a->value;
	}
	public function saveForm()
	{
		foreach($this->props as $key => $value)
		{
			$value = trim($value);
			$set = Settings::findByName($key);
			if($set)
			{
				$set->value = $value;
				if($set->value != '')
					$set->update();
				else
					$set->delete();
			}
			else if($value != '')
			{
				$set = new Settings;
				$set->name = $key;
				$set->value = $value;
				$set->save();
			}
		}
		return true;
	}
}
