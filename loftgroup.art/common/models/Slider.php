<?php

namespace common\models;
use Yii;

/**
 * This is the model class for table "slider".
 *
 * @property int $id
 * @property string $name
 * @property string $lang
 * @property int $photo_id
 * @property int $sort
 * @property int $status
 *
 * @property Image $photo
 */
class Slider extends Lang
{
	public static $STATUS = [0 => 'Отключено', 1 => 'Включено'];
	public static function tableName()
	{
		return 'slider';
	}
	public function rules()
	{
		return [
			[['name', 'status'], 'required'],
			[['lang'], 'string'],
			[['photo_id', 'sort', 'status'], 'integer'],
			[['name'], 'string', 'max' => 255],
			['props', 'each', 'rule' => ['string']],
			[['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['photo_id' => 'id']],
		] + parent::rules();
	}
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Название',
			'lang' => 'Lang',
			'photo_id' => 'Фотография',
			'sort' => 'Порядок',
			'status' => 'Статус',
		];
	}
	public static function GetAll()
	{
		return static::find()->where(['status' => 1])->andWhere(['NOT', ['photo_id' => null]])->orderBy('sort, id')->all();
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
}
