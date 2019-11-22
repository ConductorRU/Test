<?php
namespace common\models;
use Yii;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property string $url
 * @property string $preview
 */
class Image extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return 'image';
	}
	public function rules()
	{
		return [
			[['url'], 'required'],
			[['url', 'preview'], 'string', 'max' => 255],
		];
	}
	public function attributeLabels()
	{
		return
		[
			'id' => 'ID',
			'url' => 'Ссылка',
			'preview' => 'Превью',
		];
	}
	public static function DeleteById($id)
	{
		$img = Image::findOne($id);
		if($img)
		{
			$tPath = Yii::getAlias('@frontend/web/uploads/') . $img->url;
			if(file_exists($tPath))
			{
				if(unlink($tPath))
					$img->delete();
			}
		}
	}
	public static function GetPath(&$numPath)
	{
		$tPath = Yii::getAlias('@frontend/web/uploads/');
		$num = static::find()->select('MAX(id)')->scalar();
		$numPath = (int)($num/10000) . '/';
		$numPath .= (int)(((int)($num%10000))/100) . '/';
		$tPath .= $numPath;
		if(!file_exists($tPath))
			mkdir($tPath, 0755, true);
		return $tPath;
	}
	public static function CopyImage($url)
	{
		$numPath = '';
		$tPath = Image::GetPath($numPath);
		$q = explode('?', $url)[0];
		$res = explode('.', $q);
		$imgname = Lexix::generateRandomString(16) . '_' . time() . '.' . array_pop($res);
		file_put_contents($tPath . $imgname, file_get_contents($url));
		$im = new Image;
		$im->url = '/uploads/' . $numPath . $imgname;
		$im->save(true);
		return $im;
	}
	public static function createImage($file, $props = [])
	{
		$numPath = '';
		$tPath = Image::GetPath($numPath);
		
		$fName = '';
		$imgname = dechex(mt_rand()) . 'x' . dechex(time());
		$width = isset($props['width']) ? (int)$props['width'] : 100;
		$height = isset($props['height']) ? (int)$props['height'] : 100;
		$max_width = isset($props['max_width']) ? (int)$props['max_width'] : 0;
		$max_height = isset($props['max_height']) ? (int)$props['max_height'] : 0;
		$outer = isset($props['outer']) ? (int)$props['outer'] : 2;
		if(($max_width && $max_width < $width) || ($max_height && $max_height < $height))
		{
			$width = $max_width;
			$height = $max_height;
			if($outer == 2)
				$outer = 1;
		}
		if($outer == 2)//original
		{
			$im = new Image;
			$ex = explode('.', $file->name);
			$imgname .= '.' . array_pop($ex);
			$im->url = '/uploads/' . $numPath . $imgname;
			$file->saveAs($tPath . $imgname);
			$im->save(true);
			return $im;
		}
		if(is_string($file))
			$fName = $file;
		else
		{
			$fName = $tPath . $file->name;
			$file->saveAs($fName);
		}
		$imagine = \yii\imagine\Image::getImagine();
		$imn = null;
		$imgname .= '.jpg';
		$imgn = $imagine->open($fName);
		$sizeX = $width;
		$sizeY = $height;
		if(!$sizeX)
			$sizeX = 150;
		if(!$sizeY)
			$sizeY = 150;
		$size = $imgn->getSize();
		$background = new RGB('#fff');
		if($outer)//обрезать картинку
		{
			if($size->getWidth() < $size->getHeight())
			{
				$ratio = $size->getHeight()/$size->getWidth();
				$height = $sizeY;
				$width = round($height/$ratio);
			}
			else
			{
				$ratio = $size->getWidth()/$size->getHeight();
				$width = $sizeX;
				$height = round($width/$ratio);
			}
			$box = new Box($width, $height);
			$boxReal = new Box(max($width, $sizeX), max($height, $sizeY));
			$imgn->resize($box);
			$topLeft    = new Point(floor(abs($boxReal->getWidth() - $width)/2), floor(abs($boxReal->getHeight() - $height)/2));
			$canvas     = $imagine->create($boxReal, $background->color('#FFFFFF'));
			$canvas->paste($imgn, $topLeft)->crop(new Point(0, 0), new Box($sizeX, $sizeY))->save($tPath . $imgname, ['quality' => 91]);
		}
		else //добавить белые полосы
		{
			if($size->getWidth() < $size->getHeight())
			{
				$ratio = $size->getHeight()/$size->getWidth();
				$width = $sizeX;
				$height = round($width*$ratio);
			}
			else
			{
				$ratio = $size->getWidth()/$size->getHeight();
				$height = $sizeY;
				$width = round($height*$ratio);
			}
			$box = new Box($width, $height);
			$boxReal = new Box(max($width, $sizeX), max($height, $sizeY));
			$imgn->resize($box);
			$topLeft    = new Point(floor(abs($boxReal->getWidth() - $width)/2), floor(abs($boxReal->getHeight() - $height)/2));
			$canvas     = $imagine->create($boxReal, $background->color('#FFFFFF'));
			$canvas->paste($imgn, $topLeft)->crop(new Point(0, 0), new Box($sizeX, $sizeY))->save($tPath . $imgname);
		}
		$im = new Image;
		$im->url = '/uploads/' . $numPath . $imgname;
		$im->save(true);
		if(!is_string($file))
			unlink($fName);
		return $im;
	}
	public function GetUrl()
	{
		return isset(Yii::$app->params['imgServer']) ? (Yii::$app->params['imgServer'] . $this->url) : $this->url;
	}
}
