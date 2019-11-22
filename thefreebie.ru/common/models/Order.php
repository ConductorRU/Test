<?php
namespace common\models;
use Yii;
use yii\helpers\Html;
/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $type
 * @property int $user_id
 * @property int $pack_id
 * @property int $months
 * @property int $pay_method
 * @property string $items
 * @property int $price
 * @property string $created_at
 * @property string $payed_at
 * @property int $status
 *
 * @property Pack $pack
 * @property User $user
 * @property Product[] $products
 */
class Order extends \yii\db\ActiveRecord
{
	public static $TYPE = [1 => 'Пакет', 2 => 'Подписка', 3 => 'Пакеты по подписке'];
	public static $STATUS = [0 => 'Отменен', 1 => 'Ожидается оплата', 2 => 'Оплачен, но ключей нет в наличии', 3 => 'Выполнен', 4 => 'Ошибка при оформлении', 5 => 'Удален пользователем'];//0 - отменен, 1 - ожидается оплата, 2 - оплачен, ожидаются ключи, 3 - выполнен (оплачен, ключи отправлены)
	public static $USER_STATUS = [0 => 'Отменен', 1 => 'Ожидается оплата', 2 => 'Оплачен, но ключей нет в наличии', 3 => 'Исполнен', 4 => 'Ошибка при оформлении'];
	public static $PAYMENT = [1 => 'Банковская карта', 2 => 'WebMoney', 3 => 'Яндекс.Деньги', 4 => 'PayPal', 5 => 'Qiwi'];
	public static function tableName()
	{
		return 'order';
	}
	public function GetItems()
	{
		return (isset($this->items) && isset($this->items['items']) && is_array($this->items['items'])) ? $this->items['items'] : [];
	}
	public function IsBuyed()
	{
		return ($this->status == 2 || $this->status == 3);
	}
	public function GetPackIds()
	{
		if($this->type == 1 || $this->type == 3)
			return (isset($this->items) && isset($this->items['packs']) && is_array($this->items['packs'])) ? $this->items['packs'] : [];
		return [];
	}
	public function GetPackName()
	{
		if($this->type == 1)
			return (isset($this->items) && isset($this->items['pack_name'])) ? $this->items['pack_name'] : '';
		if($this->type == 2)
			return 'Подписка на ' . $this->months . ' ' . Lexix::NumberCase($this->months, 'месяц', 'месяца', 'месяцев');
		if($this->type == 3)
			return 'Пакеты по подписке';
		return '';
	}
	public function GetSetName()
	{
		return (isset($this->items) && isset($this->items['set_name'])) ? $this->items['set_name'] : '';
	}
	public function GetPriceHtml()
	{
		return $this->price ? (number_format($this->price, 0, '.', ' ') . ' руб') : ($this->type == 3 ? 'Получен по подписке' : 'Бесплатно');
	}
	public function rules()
	{
		return [
			[['type', 'pay_method', 'created_at', 'status'], 'required'],
			[['type', 'user_id', 'pack_id', 'pay_method', 'price', 'status', 'months'], 'integer'],
			[['items', 'created_at', 'payed_at'], 'safe'],
			[['pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pack::className(), 'targetAttribute' => ['pack_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'type' => 'Тип заказа',
			'user_id' => 'Пользователь',
			'pack_id' => 'Пакет',
			'months' => 'Месяцев подписки',
			'pay_method' => 'Метод оплаты',
			'items' => 'Состав заказа',
			'price' => 'Сумма, руб.',
			'created_at' => 'Создан',
			'payed_at' => 'Оплачен',
			'status' => 'Статус',
		];
	}
	public function GetDateHtml()
	{
		$e = explode(' ', $this->created_at);
		if(count($e) != 2)
			return '';
		$d = explode('-', $e[0]);
		if(count($d) == 3)
			return $d[2] . '.' . $d[1] . '.' . $d[0];
		return '';
	}
	public function GetUserStatusHtml()
	{
		$s = Order::$USER_STATUS[$this->status];
		if($this->status == 3)
			return '<span class="statusG">' . $s . '</span>';
		return '<span class="statusR">' . $s . '</span>';
	}
	public function GetShortItemsHtml()
	{
		$items = $this->GetItems();
		if($this->type == 2)//если подписка
			return $this->months . ' ' . Lexix::NumberCase($this->months, 'месяц', 'месяца', 'месяцев');
		if($this->type == 1 || $this->type == 3)
			return '<span class="btn btn-default" onclick="admin.ShowOrderItems(' . $this->id . ')">' . count($items) . ' ' . Lexix::NumberCase(count($items), 'наименование', 'наименования', 'наименований') . '</span>';
		return null;
	}
	public static function GetCode($aItem)
	{
		if(!isset($aItem['type']))
			return 'Ошибка получения кода';
		if($aItem['type'] == 1 && isset($aItem['key_keyword']) && isset($aItem['key_salt']))
		{
			$keyword = $aItem['key_keyword'];
			$salt = $aItem['key_salt'] ? $aItem['key_salt'] : null;
			return ProductKey::Decrypt($keyword, $salt);
		}
		if($aItem['type'] == 2 && isset($aItem['key_url']) && $aItem['key_url'])
			return $aItem['key_url'];
		if($aItem['type'] == 3 && isset($aItem['file_id']))
		{
			$file = File::findOne($aItem['file_id']);
			if($file)
				return $file->url;;
		}
		return 'Ошибка получения кода';
	}
	public function GetItemsHtml()
	{
		if($this->type == 2)//если подписка
			return 'Подписка на ' . $this->months . ' ' . Lexix::NumberCase($this->months, 'месяц', 'месяца', 'месяцев');
		if($this->type == 1 || $this->type == 3)
		{
			$items = $this->GetItems();
			if(!count($items))
				return '<b>Заказ пуст</b>';
			$t = '<table class="table table-bordered">';
			$t .= '<colgroup><col width="50px"><col width=""><col width="100px"><col width=""><col width=""></colgroup>';
			$t .= '<tr><th>№</th><th>Наименование</th><th>Фото</th><th>Тип</th><th>Ключ</th></tr>';
			$n = 0;
			foreach($items as $item)
			{
				++$n;
				$img = Image::findOne($item['image_id']);
				$t .= '<tr><td>' . $n . '</td>';
				$t .= '<td>' . Html::a($item['name'], ['/product/view?id=' . $item['id']], ['data-pjax' => 0]) . '</td>';
				$t .= $img ? ('<td style="background:url(' . $img->GetUrl() . ') center center no-repeat;background-size:cover;"></td>') : '<td></td>';
				$t .= '<td>' . Product::$TYPE[$item['type']] . '</td>';
				if($item['type'] == 1)
					$t .= '<td>' . Html::a(($item['key_keyword'] ? 'ключ зашифрован' : 'ключ снят с резерва'), ['/product-key?ProductKeySearch[id]=' . $item['key_id']], ['data-pjax' => 0]) . '</td>';
				if($item['type'] == 2)
					$t .= '<td>' . ($item['key_url'] ? Html::a($item['key_url'], $item['key_url'], ['data-pjax' => 0, 'target' => '_blank']) : 'ссылка скрыта') . '</td>';
				if($item['type'] == 3)
				{
					if($item['file_id'])
					{
						$file = File::findOne($item['file_id']);
						$t .= '<td>' . ($file ? Html::a($file->url, $file->url, ['data-pjax' => 0, 'target' => '_blank']) : 'ссылка на книгу не найдена') . '</td>';
					}
					else
						$t .= '<td>ссылка скрыта</td>';
				}
				$t .= '</tr>';
			}
			$t .= '</table>';
			return $t;
		}
		return '';
	}
	public function getAdminName($isShort = false)
	{
		if($this->type == 1)
		{
			$pack = $this->GetPack()->one();
			if(!$pack)
				return null;
			if($pack->parent)
			{
				$par = $pack->parent;
				return Html::a($par->name, ['/pack/update', 'id' => $par->id], ['data-pjax' => 0]) . '<br>' . Html::a($pack->name, ['/pack/update', 'id' => $pack->id], ['data-pjax' => 0]);
			}
			return Html::a($pack->name, ['/pack/update', 'id' => $pack->id], ['data-pjax' => 0]);
		}
		if($this->type == 2)
		{
			if($isShort)
				return 'Подписка';
			return 'Подписка на ' . $this->months . ' ' . Lexix::NumberCase($this->months, 'месяц', 'месяца', 'месяцев');
		}
		if($this->type == 3)
		{
			$ids = $this->GetPackIds();
			$t = '';
			foreach($ids as $id)
			{
				$pack = Pack::findOne($id);
				if($pack)
					$t .= '<br>' . Html::a($pack->name, ['/pack/update', 'id' => $pack->id], ['data-pjax' => 0]);
			}
			return 'Пакеты по подписке:' . $t;
		}
	}
	public function getPack()
	{
		return $this->hasOne(Pack::className(), ['id' => 'pack_id']);
	}
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
	public function getProducts()
	{
		return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('order_item', ['order_id' => 'id']);
	}
	public function CreatePayment()
	{
		$pay = new Payment();
		$pay->order_id = $this->id;
		$pay->sum = $this->price*100;
		$pay->type = $this->pay_method;
		$pay->created_at = date('Y-m-d H:i:s');
		$pay->status = 1;
		if($this->pay_method == 1)
			$pay->result = PaymentSberbank::CreateOrder($this);
		if($pay->result)
			$pay->extern_id = $pay->result->extern_id;
		$pay->save();
		return $pay;
	}
	public static function GenerateItems($pack, &$keys)
	{
		$items = [];
		$keys = [];
		$prods = array_merge($pack->products, $pack->iproducts);
		foreach($prods as $prod)
		{
			$item = [];
			$item['id'] = $prod->id;
			$item['name'] = $prod->name;
			$item['type'] = $prod->type;
			$item['image_id'] = $prod->image_id;
			if($prod->type == 1)//если игровой ключ
			{
				$key = $prod->GetFreeKey();
				if(!$key)
					return null;//ключей нет
				$item['key_id'] = $key->id;
				$item['key_keyword'] = $key->keyword;
				$item['key_salt'] = $key->salt;
				$keys[] = $key;
			}
			else if($prod->type == 2)//если онлайн-курс
				$item['key_url'] = $prod->key_url;
			else if($prod->type == 3)//если книга
				$item['file_id'] = $prod->file_id;
			$items[] = $item;
		}
		return $items;
	}
	public function SetPack($par, $pack, &$isSelled)
	{
		$this->type = 1;
		$this->pack_id = $pack->id;
		$orItems = [];
		$keys = [];
		$orItems['pack_name'] = $par->name;
		$orItems['set_name'] = ($par != $pack) ? $pack->name : '';
		$orItems['items'] = static::GenerateItems($pack, $keys);
		if($orItems['items'] === null)
		{
			$isSelled = true;
			return null;
		}
		$orItems['packs'] = [$pack->id];
		$this->items = $orItems;
		return $keys;
	}
	public function SetSubscriptPack($packs)
	{
		$this->type = 3;
		$this->pack_id = null;
		$orItems = [];
		$keys = [];
		$allItems = [];
		$allPacks = [];
		foreach($packs as $pack)
		{
			$ks = [];
			$ps = static::GenerateItems($pack, $ks);
			if($ps === null)
				return null;
			foreach($ks as $k)
				$keys[] = $k;
			foreach($ps as $p)
				$allItems[] = $p;
			$allPacks[] = $pack->id;
		}
		$orItems['packs'] = $allPacks;
		$orItems['items'] = $allItems;
		$this->items = $orItems;
		return $keys;
	}
	public function Execute()//выполняется один раз, когда средства поступили на счет
	{
		if($this->type == 1)
		{
			$all = $this->items;
			$items = $this->GetItems();
			foreach($items as &$item)
			{
				if(isset($item['key_id']))
				{
					$key = ProductKey::findOne($item['key_id']);
					if($key)
					{
						$key->status = 3;//ключ продан
						$key->update(); 
					}
				}
			}
			$all['items'] = $items;
			$this->items = $all;
			$this->status = 3;
		}
		else if($this->type == 2)
		{
			$price = $this->months ? (int)($this->price/$this->months) : 0;
			if(Subscript::AddSubscript($this->user_id, $this->months, $price))//добавить месяца в подписку
				$this->status = 3;
		}
		$this->update();
	}
	public function Cancel($cancelPayment = false)
	{
		if($this->status == 3)
			return;//нельзя отменить выполненный заказ
		if($this->type == 1)
		{
			$all = $this->items;
			$items = $this->GetItems();
			foreach($items as &$item)
			{
				if(isset($item['key_id']))
				{
					$key = ProductKey::findOne($item['key_id']);
					if($key->status != 3 && $key->order_id == $this->id)//если не продан и если этот ключ не перевязан к другому заказу
					{
						$key->order_id = null;
						$key->status = 1;
						$key->update(); 
					}
					$item['key_keyword'] = null;//на всякий случай стираем ключи
					$item['key_salt'] = null;
				}
				if(isset($item['key_url']))
					$item['key_url'] = null;
				if(isset($item['file_id']))
					$item['file_id'] = null;
			}
			$all['items'] = $items;
			$this->items = $all;
		}
		$this->status = 0;
		$this->update();
		if($cancelPayment)
		{
			$pay = Payment::find()->where(['order_id' => $this->id])->one();
			if($pay)
				echo $pay->Cancel();
		}
	}

	public function beforeSave($insert)
	{
		if(parent::beforeSave($insert))
		{
			$this->items = serialize($this->items);
			return true;
		}
		return false;
	}
	public function afterFind()
	{
		if($this->items)
			$this->items = unserialize($this->items);
	}
	public function beforeDelete()
	{
		$this->Cancel(true);
		return parent::beforeDelete();
	}
}
