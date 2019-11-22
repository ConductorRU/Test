<?php

namespace common\models;
use Yii;

/**
 * This is the model class for table "callback".
 *
 * @property int $id
 * @property int $page
 * @property int $lang
 * @property string $name
 * @property string $phone
 * @property string $text
 * @property string $file
 * @property string $created_at
 * @property int $status
 */
class Callback extends \yii\db\ActiveRecord
{
	public static $STATUS = [0 => 'На рассмотрении', 1 => 'Рассмотрено'];
	public static function tableName()
	{
			return 'callback';
	}
	public function rules()
	{
		return [
			[['page', 'lang', 'status'], 'integer'],
			[['name', 'created_at', 'status'], 'required'],
			[['text'], 'string'],
			[['created_at'], 'safe'],
			[['name', 'phone', 'file'], 'string', 'max' => 255],
		];
	}
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'page' => 'Страница',
			'lang' => 'Язык сайта',
			'name' => 'Имя',
			'phone' => 'Телефон',
			'text' => 'Данные',
			'file' => 'Файл',
			'created_at' => 'Дата обращения',
			'status' => 'Статус',
		];
	}
}
