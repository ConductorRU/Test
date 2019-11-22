<?php
namespace common\models;
use Yii;
/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $lang
 * @property int $year
 * @property int $category_id
 * @property int $file_id
 * @property int $photo_id
 * @property int $preview_id
 * @property int $sort
 * @property string $created_at
 * @property int $status
 *
 * @property Category $category
 * @property File $file
 * @property Image $photo
 */
class Project extends Lang
{
	public static $STATUS = [0 => 'Отключено', 1 => 'Включено'];
	public static function tableName()
	{
		return 'project';
	}
	public function rules()
	{
		return [
			[['name', 'created_at', 'status'], 'required'],
			[['lang'], 'string'],
			[['year', 'category_id', 'file_id', 'photo_id', 'preview_id', 'sort', 'status'], 'integer'],
			[['created_at'], 'safe'],
			[['name', 'url'], 'string', 'max' => 255],
			[['url'], 'unique'],
			[['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
			[['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
			[['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['photo_id' => 'id']],
			[['preview_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['preview_id' => 'id']],
			['props', 'each', 'rule' => ['string']],
		] + parent::rules();
	}
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Имя',
			'url' => 'Символьный код (должен быть уникальным)',
			'lang' => 'Lang',
			'year' => 'Год, в котором был реализован проект',
			'category_id' => 'Направление',
			'file_id' => 'PDF-файл с проектом',
			'photo_id' => 'Титульное изображение',
			'preview_id' => 'Превью (в разделе "Проекты")',
			'sort' => 'Порядок',
			'created_at' => 'Создан',
			'status' => 'Статус',
		];
	}
	public function getCategory()
	{
		return $this->hasOne(Category::className(), ['id' => 'category_id']);
	}
	public function getCategoryName()
	{
		$cat = $this->getCategory()->one();
		if($cat)
			return $cat->GetProp('xx_name');
		return '';
	}
	public function getFile()
	{
		return $this->hasOne(File::className(), ['id' => 'file_id']);
	}
	public function getPhoto()
	{
		return $this->hasOne(Image::className(), ['id' => 'photo_id']);
	}
	public function getPreview()
	{
		return $this->hasOne(Image::className(), ['id' => 'preview_id']);
	}
	public function getPhotoUrl()
	{
		$img = $this->getPhoto()->one();
		if($img)
			return $img->GetUrl();
		return '';
	}
	public function getFileUrl()
	{
		$file = $this->getFile()->one();
		if($file)
			return $file->url;
		return '';
	}
	public function getUrl()
	{
		$cat = $this->getCategory()->one();
		if($cat)
			return '/projects/' . $cat->url . '/' . $this->url;
		return '';
	}
	public function getPreviewUrl($orTitle = true)
	{
		$img = $this->getPreview()->one();
		if($img)
			return $img->GetUrl();
		if($orTitle)
			return $this->getPhotoUrl();
		return '';
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
