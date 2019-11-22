<?php
namespace common\models;
use Yii;
/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $url
 * @property integer $size
 */
class File extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return 'file';
	}
	public function rules()
	{
		return [
			[['url'], 'required'],
			[['size'], 'integer'],
			[['url'], 'string', 'max' => 1000],
		];
	}
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'url' => 'Ссылка',
			'size' => 'Размер',
		];
	}
	public static function findById($id)
	{
		return static::findOne(['id' => $id]);
	}
	public function getSize()
  {
		if($this->size >= 1073741824)
			return number_format($this->size / 1073741824, 2) . ' ГБайт';
		if($this->size >= 1048576)
			return number_format($this->size / 1048576, 2) . ' МБайт';
		if($this->size >= 1024)
			return number_format($this->size / 1024, 2) . ' КБайт';
		if($this->size > 1)
			return $this->size . ' Байт';
		if($this->size == 1)
			return $this->size . ' Байт';
		return '0 Байт';
	}
	public static function createFile($file, $path = 'files/')
	{
		$tPath = Yii::getAlias('@frontend/web/' . $path);
		$url = mb_strimwidth(Lexix::translit($file->getBaseName()), 0, 128) . '_' . dechex(time()) . '.' . $file->getExtension();
		$file->saveAs($tPath . $url);
		$f = new File;
		$f->url = '/' . $path . $url;
		$f->size = $file->size;
		$f->save(true);
		return $f;
	}
	public function afterDelete()
	{
		parent::afterDelete();
		$tPath = Yii::getAlias('@frontend/web');
		$url = $tPath . $this->url;
		unlink($url);
		return true;
	}
}
