<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $lang
 * @property int $photo_id
 * @property int $sort
 * @property int $status
 *
 * @property Image $photo
 * @property Project[] $projects
 */
class Category extends Lang
{
	public static $STATUS = [0 => 'Отключено', 1 => 'Включено'];
	public static function tableName()
	{
		return 'category';
	}
	public function rules()
	{
		return
		[
			[['name', 'status'], 'required'],
			['url', 'unique'],
			[['lang'], 'string'],
			[['photo_id', 'status', 'sort'], 'integer'],
			[['name', 'url'], 'string', 'max' => 255],
			[['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['photo_id' => 'id']],
			['props', 'each', 'rule' => ['string']],
		] + parent::rules();
	}
	public function attributeLabels()
	{
		return
		[
			'id' => 'ID',
			'name' => 'Наименование',
			'url' => 'Символьный код (для формирования ссылки)',
			'lang' => 'Текст',
			'photo_id' => 'Фото',
			'sort' => 'Порядок',
			'status' => 'Статус',
		];
	}
	public static function GetList()
	{
		$res = [];
		$cats = static::GetAll();
		foreach($cats as $cat)
			$res[$cat->id] = $cat->name;
		return $res;
	}
	public static function GetAll()
	{
		return static::find()->where(['status' => 1])->orderBy('sort, id')->all();
	}
	public function getPhoto()
	{
		return $this->hasOne(Image::className(), ['id' => 'photo_id']);
	}
	public function getPhotoUrl()
	{
		$img = $this->getPhoto()->one();
		if($img)
			return $img->GetUrl();
		return '';
	}
	public function getProjects()
	{
		return $this->hasMany(Project::className(), ['category_id' => 'id']);
	}
	public function beforeSave($insert)
	{
		if(parent::beforeSave($insert))
		{
			if($this->url == '' || $this->url == null)
			{
				$n = 0;
				do
				{
					$this->url = Lexix::translit($this->name);
					if($n)
						$this->url .= '-' . $n;
					++$n;
				}
				while(static::find()->where(['url' => $this->url])->one());
			}
			return true;
		}
		return false;
	}
}
