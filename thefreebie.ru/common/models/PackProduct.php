<?php
namespace common\models;
use Yii;

/**
 * This is the model class for table "pack_product".
 *
 * @property int $pack_id
 * @property int $product_id
 * @property int $sort
 */
class PackProduct extends \yii\db\ActiveRecord
{
	public static $TYPE =
	[
		1 => ['name' => 'Steam', 'icon' => '/img/key-steam.svg'],
		2 => ['name' => 'Origin', 'icon' => '/img/key-origin.svg'],
		4 => ['name' => 'Uplay', 'icon' => '/img/key-uplay.svg'],
		8 => ['name' => 'GOG', 'icon' => '/img/key-gog.svg'],
		16 => ['name' => 'Battle.Net', 'icon' => '/img/key-blizzard.svg'],
	];
	public static $OS =
	[
		1 => ['name' => 'Windows', 'icon' => '/img/pl-windows.svg'],
		2 => ['name' => 'MacOS', 'icon' => '/img/pl-mac.svg'],
		4 => ['name' => 'Linux', 'icon' => '/img/pl-linux.svg'],
	];
	public static function tableName()
	{
		 return 'pack_product';
	}
	public function rules()
	{
		return [
			[['pack_id', 'product_id'], 'required'],
			[['pack_id', 'product_id', 'sort', 'type', 'os'], 'integer'],
		];
	}
	public function attributeLabels()
	{
		return [
			'pack_id' => 'Раздел',
			'product_id' => 'Товар',
			'type' => 'Игровая площадка',
			'os' => 'Операционная система',
			'sort' => 'Порядок',
		];
	}
	public static function Create($packId, $productId, $type = 0, $os = 0, $sort = 0)
	{
		$p = new PackProduct;
		$p->pack_id = $packId;
		$p->product_id = $productId;
		$p->type = $type;
		$p->os = $os;
		$p->sort = $sort;
		$p->save();
		return $p;
	}
	public static function Get($packId, $productId)
	{
		return PackProduct::find()->where(['pack_id' => $packId, 'product_id' => $productId])->one();
	}
	public function GetKeysCount($status = 1)//количество действующих ключей
	{
		return ProductKey::find()->where(['product_id' => $this->product_id, 'type' => $this->type, 'os' => $this->os, 'status' => $status])->count();
	}
	public function GetOsHtml()
	{
		$t = '';
		foreach(PackProduct::$OS as $k => $v)
			if($this->os & $k)
				$t .= '<span title="' . $v['name'] . '"><img src="' . $v['icon'] . '" alt="' . $v['name'] . '" /></span>';
		return $t;
	}
	public function GetPlatformHtml()
	{
		$t = '';
		foreach(PackProduct::$TYPE as $k => $v)
			if($this->type & $k)
				$t .= '<span title="' . $v['name'] . '"><img src="' . $v['icon'] . '" alt="' . $v['name'] . '" /></span>';
		return $t;
	}
}
