<?php
namespace common\models;
use Yii;

/**
 * This is the model class for table "pack".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property int $small_id
 * @property int $image_id
 * @property int $background_id
 * @property int $sort
 * @property string $created_at
 * @property int $props
 * @property int $available
 * @property int $status
 *
 * @property Image $image
 * @property Pack $parent
 * @property Pack[] $packs
 * @property Image $small
 * @property StockPack[] $stockPacks
 * @property Stock[] $stocks
 */
class Pack extends \yii\db\ActiveRecord
{
	private $old_img_id = 0;
	private $old_small_id = 0;
	private $old_back_id = 0;
	public $propsL = [];
	public $products = [];
	public $iproducts = null;
	public $packProducts = [];
	public static $PROPS = [1 => 'Показывать в каталоге',  2 => 'Доступен по подписке', 16 => 'Включать товары из пакетов, Порядок которых ниже, чем текущий'];//1 => 16
	public static $ROOT_PROPS = [1 => 'Показывать в каталоге',  2 => 'Доступен по подписке',  4 => 'Активировать скидки на пакет', 8 => 'Показывать на главной (Актуальные предложения)'];//2 => 4, 4 => 8
	public static $ALL_PROPS = [1 => 'В каталоге', 2 => 'По подписке', 4 => 'Скидка', 8 => 'На главной'];
	static public $TYPE = [1 => 'Игра', 2 => 'Онлайн-курс', 3 => 'Книга', 100 => 'Смешанный'];
	static public $TYPES = [1 => ['Игры', 'games'], 3 => ['Книги', 'books'], 2 => ['Интернет-уроки', 'courses']];
	public static $AVAILABLE = [0 => 'Нет в наличии', 1 => 'Есть в наличии'];
	public static $STATUS = [0 => 'Отключено', 1 => 'Включено'];
	public static function tableName()
	{
		return 'pack';
	}
	public function rules()
	{
		return [
			[['parent_id', 'price', 'type', 'price_discount', 'small_id', 'image_id', 'background_id', 'sort', 'props', 'available', 'status'], 'integer'],
			[['name', 'price', 'type', 'sort', 'created_at', 'status'], 'required'],
			[['discount_to', 'created_at'], 'safe'],
			['url', 'unique'],
			[['name', 'altername'], 'string', 'max' => 255],
			[['description'], 'string', 'max' => 10000],
			[['propsL'], 'each', 'rule' => ['integer']],
			[['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['image_id' => 'id']],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pack::className(), 'targetAttribute' => ['parent_id' => 'id']],
			[['small_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['small_id' => 'id']],
		];
	}
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'parent_id' => 'Родительский пакет',
			'name' => 'Название',
			'altername' => 'Альтернативное название (без слова "Пакет")',
			'type' => 'Тип',
			'url' => 'Символьный код из латинских букв (для формирования ссылки на пакет)',
			'description' => 'Описание',
			'price' => 'Цена, от',
			'price_discount' => 'Цена со скидкой, от',
			'discount_to' => 'Дата завершения акции (скидки)',
			'small_id' => 'Превью',
			'image_id' => 'Фото акции',
			'background_id' => 'Фон шапки',
			'sort' => 'Порядок',
			'created_at' => 'Создан',
			'props' => 'Свойства',
			'propsL' => 'Свойства',
			'available' => 'Наличие',
			'status' => 'Статус',
		];
	}
	public static function GetSubscriptPacks($user_id)
	{
		$user = User::findOne($user_id);
		if(!$user)
			return null;
		$exclude = $user->GetPacksIds();
		for($i = 1; $i < 3; ++$i)
		{
			if($i == 1)
			{
				$pack = static::find()-where(['type' => $i, 'status' => 1])->andWhere('props & 2')->one();
			}
			else
			{
				$pack = static::find()-where(['type' => $i, 'status' => 1])->andWhere('props & 2')->one();
			}
		}
	}
	public function IsProp($prop_id)
	{
		return $this->props & $prop_id;
	}
	public function AddProp($prop_id)
	{
		$this->props = Help::AddFlag($this->props, $this->propsL, $prop_id);
	}
	public function RemoveProp($prop_id)
	{
		$this->props = Help::RemoveFlag($this->props, $this->propsL, $prop_id);
	}
	public function GetPropsHtml()
	{
		$t = '';
		foreach($this->propsL as $p)
			if(isset(Pack::$ALL_PROPS[$p]))
			{
				if($p == 1)
					$t .= '<div><a href="/catalog" data-pjax="0" target="_blank"><b>' . Pack::$ALL_PROPS[$p] . '</b></a></div>';
				else if($p == 2)
					$t .= '<div><a href="/subscript" data-pjax="0" target="_blank">' . Pack::$ALL_PROPS[$p] . '</a></div>';
				else
					$t .= '<div>' . Pack::$ALL_PROPS[$p] . '</div>';
			}
		return $t;
	}
	public function getImage()
	{
		return $this->hasOne(Image::className(), ['id' => 'image_id']);
	}
	public function getParent()
	{
		return $this->hasOne(Pack::className(), ['id' => 'parent_id']);
	}
	public function getPacks()
	{
		return $this->hasMany(Pack::className(), ['parent_id' => 'id']);
	}
	public function getSmall()
	{
		return $this->hasOne(Image::className(), ['id' => 'small_id']);
	}
	public function getStockPacks()
	{
		return $this->hasMany(StockPack::className(), ['pack_id' => 'id']);
	}
	public function getStocks()
	{
		return $this->hasMany(Stock::className(), ['id' => 'stock_id'])->viaTable('stock_pack', ['pack_id' => 'id']);
	}
	public function getProducts()
	{
		return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('pack_product', ['pack_id' => 'id']);
	}
	public function getPackProducts()
	{
		return $this->hasMany(PackProduct::className(), ['pack_id' => 'id']);
	}
	public function getIncludedProducts()
	{
		if(!$this->parent_id)
		{
			$this->iproducts = [];
			return [];
		}
		if($this->iproducts != null)
			return $this->iproducts;
		$ids = [];
		$prods = [];
		$packs = Pack::find()->where(['parent_id' => $this->parent_id])->andWhere(['!=', 'id', $this->id])->andWhere(['<', 'sort', $this->sort])->all();
		foreach($packs as $pack)
		{
			$ps = $pack->products;
			foreach($ps as $p)
				if(!isset($ids[$p->id]))
				{
					$p->pack_id = $pack->id;
					$prods[] = $p;
					$ids[$p->id] = 1;
				}
		}
		$this->iproducts = $prods;
		return $this->iproducts;
	}
	public function getTotalProductCount()
	{
		return (count($this->products) + count($this->iproducts));
	}
	public function beforeValidate()
	{
		if(parent::beforeValidate())
		{
			if(!$this->id && !is_subclass_of($this, 'common\models\Pack'))
				$this->created_at = date('Y-m-d H:i:s');
			return true;
		}
		return false;
	}
	public function UpdateAvailable()
	{
		$this->available = 0;
		$childs = $this->getPacks()->all();
		if(count($this->products) || count($this->iproducts))
			$childs[] = $this;
		foreach($childs as $child)
		{
			$prods = array_merge($child->products, $child->iproducts);
			if(!count($prods))
				continue;
			$gameCnt = 0;
			foreach($prods as $prod)
				if($prod->type == 1)
					++$gameCnt;
			$keys = $child->GetKeysCount(1);
			if(!$gameCnt || ($keys > 0 && ($child == $this || $child->status == 1)))
				$this->available = 1;
		}
	}
	public function beforeSave($insert)
	{
		if(parent::beforeSave($insert))
		{
			$this->props = Help::SaveFlag($this->propsL);
			if($insert)
			{
				$this->old_img_id = $this->image_id;
				$this->old_small_id = $this->small_id;
				$this->old_back_id = $this->background_id;
			}
			if($this->parent_id)
			{
				$par = $this->getParent()->one();
				if($par->props & 2)
					$par->RemoveProp(2);//не доступен по подписке
			}
			else if($this->getPacks()->count())
			{
				if($this->props & 2)
					$this->RemoveProp(2);//не доступен по подписке
			}
			$this->UpdateAvailable();
			return true;
		}
		return false;
	}
	public function afterFind()
	{
		parent::afterFind();
		$this->propsL = Help::FindFlag($this->props);
		$this->products = $this->getProducts()->all();
		$prods = $this->getPackProducts()->all();
		$aProds = [];
		foreach($prods as $prod)
			$aProds[$prod->product_id] = $prod;
		foreach($this->products as &$prod)
		{
			$prod->pack = $aProds[$prod->id];
		}
		$this->getIncludedProducts();
		//$this->products = Product::find()->with('packs')->all();
	}
	public function afterSave($insert, $changedAttributes)
	{
		if($insert)
		{
			if($this->old_img_id && $this->old_img_id != $this->image_id)
				Image::DeleteById($this->old_img_id);
			if($this->old_small_id && $this->old_small_id != $this->small_id)
				Image::DeleteById($this->old_small_id);
			if($this->old_back_id && $this->old_back_id != $this->background_id)
				Image::DeleteById($this->old_back_id);
		}
		$remPack = [];
		foreach($this->products as $p)
			$remPack[$p->id] = $p->id;
		$n = 0;
		foreach($this->packProducts as $prod)
		{
			if(isset($remPack[$prod->product_id]))
			{
				unset($remPack[$prod->product_id]);
				$prod->update();
			}
			else
				$prod->save();
			++$n;
		}
		foreach($remPack as $k => $v)
		{
			$p = PackProduct::Get($this->id, $k);
			if($p)
				$p->delete();
		}
		if($this->url === null || !$this->url)
		{
			$this->url = Lexix::translit($this->name);
			if(Pack::find()->select('id')->where(['url' => $this->url])->andWhere(['!=', 'id', $this->id])->scalar())
				$this->url = Lexix::translit($this->name) . '_' . $this->id;
			$this->update();
		}
		parent::afterSave($insert, $changedAttributes);
	}
	public function getSmallUrl($orBig = 0)
	{
		$image = $this->getSmall()->one();
		if($orBig && !$image)
			return $this->getImageUrl();
		return $image ? $image->url : '/img/no-photo.png';
	}
	public function getImageUrl($orSmall = 0)
	{
		$image = $this->getImage()->one();
		if($orSmall && !$image)
			return $this->getSmallUrl();
		return $image ? $image->url : '/img/no-photo.png';
	}
	public function getBackgroundUrl()
	{
		$image = $this->getBackground()->one();
		return $image ? $image->url : '/img/no-photo.png';
	}
	public function getAnyImageUrl()
	{
		$image = $this->getSmall()->one();
		if($image)
			return $image->url;
		$image = $this->getImage()->one();
		if($image)
			return $image->url;
		return '/img/no-photo.png';
	}
	public function getTypeUrl()
	{
		$types = [1 => '/img/type-games.svg', 2 => '/img/type-course.svg', 3 => '/img/type-books.svg'];
		return (isset($types[$this->type]) ? $types[$this->type] : '');
	}
	public function getNameHtml()
	{
		$count = $this->getTotalProductCount();
		$type = Lexix::NumberCase($count, 'продукт', 'продукта', 'продуктов');
		if($this->type == 1)
			$type = Lexix::NumberCase($count, 'игра', 'игры', 'игр');
		else if($this->type == 2)
			$type = Lexix::NumberCase($count, 'курс', 'курса', 'курсов');
		else if($this->type == 3)
			$type = Lexix::NumberCase($count, 'книга', 'книги', 'книг');
		$t = $this->name . ': ' . $count . ' ' . $type . ' · ';
		if($this->IsDiscount())
		{
			$t .= 'от ' . $this->price_discount . ' ' . Lexix::NumberCase($this->price_discount, 'рубль', 'рубля', 'рублей') . ' ';
			if($this->price > 0 && $this->price_discount < $this->price)
			{
				$p = 100 - (int)(($this->price_discount/$this->price)*100.0);
				if($p)
					$t .= '<span>Акция ' . $p . '%</span>';
			}
		}
		else if($this->price > 0)
			$t .= 'от ' . $this->price . ' ' . Lexix::NumberCase($this->price, 'рубль', 'рубля', 'рублей');
		else
			$t .= 'бесплатно';
		return $t;
	}
	public function IsDiscount()
	{
		$isProp = (($this->parent_id && $this->parent && $this->parent->props & 4) || ($this->props & 4));
		if($isProp && $this->price_discount >= 0)
			return true;
	}
	public function getRealPrice()
	{
		if($this->IsDiscount())
			return $this->price_discount;
		if($this->price > 0)
			return $this->price;
		return 0;
	}
	public function getPriceHtml()
	{
		$minPrice = null;
		$packs = ($this->packs && count($this->packs)) ? $this->packs : [$this];
		foreach($packs as $p)
		{
			$price = $p->getRealPrice();
			if($minPrice === null || $minPrice > $price)
				$minPrice = $price;
		}
		if($minPrice > 0)
			return 'Всего лишь от <span>' . $minPrice . ' ' . Lexix::NumberCase($minPrice, 'рубль', 'рубля', 'рублей') . '</span>';
		return '<span>БЕСПЛАТНО</span>';
	}
	public function getDescHtml($isBig = false)
	{
		if(trim($this->description))
			return $this->description;
		$prods = $this->products;
		foreach($this->packs as $child)
			$prods += $child->products;
		$t = '';
		$i = 0;
		foreach($prods as $prod)
		{
			if($i == 3)
				break;
			if($t)
				$t .= ', ';
			$t .= $prod->name;
			++$i;
		}
		if($t)
			return $t . ' и многое другое' . ($isBig ? '<br>Получи все это уже сегодня' : '');
		return '';
	}
	public function GetKeysCount($status = 1)//количество действующих ключей
	{
		$keys = null;
		$packs = [];
		$inc = $this->getIncludedProducts();
		foreach($inc as $in)
			if($in->pack)
				$packs[] = $in->pack;
		foreach($this->products as $in)
			if($in->pack)
				$packs[] = $in->pack;
		foreach($packs as $pack)
		{
			$cnt = $pack->GetKeysCount($status);
			if($keys === null || $keys > $cnt)
				$keys = $cnt;
		}
		return $keys;
	}
	public function GetUrl()
	{
		return '/catalog/' . $this->url;
	}
	public static function GetCatalogQuery($type = 0)
	{
		$q = Pack::find()->where(['status' => 1])->andWhere('parent_id IS NULL')->andWhere('props & 1')->andWhere('pack.available & 1');
		if($type)
			$q->andWhere(['type' => $type]);
		return $q;
	}
	public static function GetCatalog($q = null)
	{
		$res = [];
		$row1 = [];
		$row2 = [];
		$grid = [];//0 1 2 3 | 4 5 6 7
		$lastBlock = 0;
		if($q)
			$packs = $q->orderBy('sort, id')->all();
		else
			$packs = Pack::GetCatalogQuery()->orderBy('sort, id')->all();
		foreach($packs as $pack)
		{
			if($pack->IsProp(4))//скидка, big
			{
				for($i = $lastBlock; ; $i += 2)
				{
					if(!isset($grid[$i]) && !isset($grid[$i + 1]) && !isset($grid[$i + 4]) && !isset($grid[$i + 5]))
					{
						$grid[$i] = $grid[$i + 1] = $grid[$i + 4] = $grid[$i + 5] = 1;
						$grid[$i] = $pack;
						break;
					}
				}
			}
			else
			{
				for($i = $lastBlock; ; ++$i)
				{
					if(!isset($grid[$i]))
					{
						$grid[$i] = 1;
						$grid[$i] = $pack;
						break;
					}
				}
			}
			for($i = $lastBlock; ; ++$i)
				if(!isset($grid[$i]))
				{
					$lastBlock = $i;
					break;
				}
		}
		ksort($grid);
		$rows = [];
		$row = ['h' => 0, 'left' => ['h' => 1, 'childs' => []], 'right' => ['h' => 1, 'childs' => []]];
		$siz = 0;
		$lastAdd = 0;
		foreach($grid as $n => $g)
		{
			if($g === 1)
				continue;
			if($n%4 == 0)
			{
				++$siz;
				if($row['h'] == 1)
				{
					$siz = 1;
					$rows[] = $row;
					$row['left']['h'] = 1;
					$row['left']['childs'] = [];
					$row['right']['childs'] = [];
					$lastAdd = 0;
				}
				else if($row['h'] == 2 && $siz == 2)
				{
					$siz = 1;
					$rows[] = $row;
					$row['left']['h'] = 1;
					$row['left']['childs'] = [];
					$row['right']['childs'] = [];
					$lastAdd = 0;
				}
				$isBig = 1;
				for($i = 0; $i < 4; ++$i)//проверяем линию
					if(isset($grid[$n + $i]) && $grid[$n + $i] !== 1 && $grid[$n + $i]->IsProp(4))//если есть 
						$isBig = 2;
				$row['h'] = $isBig;
			}
			if($g->IsProp(4))
			{
				if(!count($row['left']['childs']))
				{
					$row['left']['h'] = 2;
					$row['left']['childs'][] = $g;
					$lastAdd = 1;
				}
				else if(!count($row['right']['childs']))
				{
					$row['right']['h'] = 2;
					$row['right']['childs'][] = $g;
					$lastAdd = 1;
				}
			}
			else
			{
				if($row['left']['h'] == 1 && count($row['left']['childs']) < 2*$row['h'])
				{
					$row['left']['childs'][] = $g;
					$lastAdd = 1;
				}
				else if($row['right']['h'] == 1 && count($row['right']['childs']) < 2*$row['h'])
				{
					$row['right']['childs'][] = $g;
					$lastAdd = 1;
				}
			}
		}
		if($lastAdd)
			$rows[] = $row;
		return $rows;
	}
}
