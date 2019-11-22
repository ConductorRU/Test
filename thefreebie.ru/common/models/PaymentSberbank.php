<?php
namespace common\models;
use yii\helpers\Url;
class PaymentSberbank
{
	public $error = null;
	public $url = null;
	public $extern_id = null;
	public static function PrepareVars($andPrefix = false, $isTest = false, $testBattle = false)
	{
		$vars = array();
		if((!$isTest && Settings::getValue('payment_sb_test')) || ($isTest && !$testBattle))
		{
			$vars['userName'] = Settings::getValue('payment_sb_test_login');
			$vars['password'] = Settings::getValue('payment_sb_test_pass');
			if($andPrefix)
				$vars['orderNumber'] = Settings::getValue('payment_sb_test_prefix');
		}
		else
		{
			$vars['userName'] = Settings::getValue('payment_sb_login');
			$vars['password'] = Settings::getValue('payment_sb_pass');
			if($andPrefix)
				$vars['orderNumber'] = Settings::getValue('payment_sb_prefix');
		}
		return $vars;
	}
	public static function CreateOrder($order, $isTest = false, $testBattle = false, $testEmail = '')
	{
		$user = $order->GetUser()->one();
		$vars = static::PrepareVars(true, $isTest, $testBattle);
		if($isTest)
			$vars['orderNumber'] .= 'TEST_' . $order->id;
		else
			$vars['orderNumber'] .= $order->id;
		$vars['amount'] = $order->price*100;
		$vars['returnUrl'] = Url::base(true) . '/payment-sb';
		$vars['failUrl'] = Url::base(true) . '/payment-sb';
		if($isTest && $testEmail)
			$vars['jsonParams'] = json_encode(['email' => $testEmail]);
		else if($user && $user->email)
			$vars['jsonParams'] = json_encode(['email' => $user->email]);

		// Описание заказа, не более 24 символов, запрещены % + \r \n
		if($isTest)
			$vars['description'] = 'тестирование заказа №' . $order->id;
		else if($order->type == 1)
			$vars['description'] = 'Оплата пакета №' . $order->pack_id;
		else if($order->type == 2)
			$vars['description'] = 'Оплата подписки на ' . $order->months . ' ' . Lexix::NumberCase($order->months, 'месяц', 'месяца', 'месяцев');

		$ch = curl_init('https://3dsec.sberbank.ru/payment/rest/register.do?' . http_build_query($vars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, JSON_OBJECT_AS_ARRAY);
		//var_dump($res);
		$pm = new PaymentSberbank;
		if(empty($res['orderId']))
			$pm->error = $res['errorMessage'];                        
		else
		{
			$pm->url = $res['formUrl'];
			$pm->extern_id = $res['orderId'];
		}
		return $pm;
	}
	public static function Cancel($pay)
	{
		$vars = static::PrepareVars();
		$vars['orderId'] = $pay->extern_id;
		$ch = curl_init('https://3dsec.sberbank.ru/payment/rest/reverse.do?' . http_build_query($vars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, JSON_OBJECT_AS_ARRAY);
		return isset($res['errorMessage']) ? $res['errorMessage'] : '';
	}
	public static function GetStatus($pay)
	{
		$r = ['status' => null, 'desc' => null, 'error' => null];
		$vars = static::PrepareVars();
		$vars['orderId'] = is_string($pay) ? $pay : $pay->extern_id;
		$ch = curl_init('https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do?' . http_build_query($vars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($res, JSON_OBJECT_AS_ARRAY);
		if(!empty($res['OrderStatus']))
		{
			if($res['OrderStatus'] == 0)
			{
				$r['status'] = 1;
				$r['desc'] = 'заказ зарегистрирован, но не оплачен';
			}
			if($res['OrderStatus'] == 1)
			{
				$r['status'] = 1;
				$r['desc'] = 'предавторизованная сумма удержана';
			}
			if($res['OrderStatus'] == 2)
			{
				$r['status'] = 2;
				$r['desc'] = 'проведена полная авторизация суммы заказа';
			}
			if($res['OrderStatus'] == 3)
			{
				$r['status'] = 0;
				$r['desc'] = 'авторизация отменена';
			}
			if($res['OrderStatus'] == 4)
			{
				$r['status'] = 4;
				$r['desc'] = 'по транзакции была проведена операция возврата';
			}
			if($res['OrderStatus'] == 5)
			{
				$r['status'] = 1;
				$r['desc'] = 'инициирована авторизация через сервер контроля доступа банка-эмитента';
			}
			if($res['OrderStatus'] == 6)
			{
				$r['status'] = 0;
				$r['desc'] = 'авторизация отклонена';
			}
		}
		else if(!empty($res['errorMessage']))
			$r['error'] = $res['errorMessage'];
		return $r;
	}
}