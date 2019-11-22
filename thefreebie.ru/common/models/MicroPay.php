<?php
namespace common\models;
use yii\helpers\Url;
class MicroPay
{
	public static function PrepareVars($andPrefix = false, $isTest = false, $testBattle = false)
	{
		$vars = array();
		if((!$isTest && Settings::getValue('payment_sb_test')) || ($isTest && !$testBattle))
		{
			$vars['login'] = trim(Settings::getValue('kassa_test_login'));
			$vars['password'] = trim(Settings::getValue('kassa_test_password'));
			$vars['url'] = Settings::getValue('kassa_test_url');
			$vars['userPass'] = Settings::getValue('kassa_test_pass');
			$vars['operator'] = Settings::getValue('kassa_test_operator');
			$vars['buyerAddress'] = Settings::getValue('kassa_test_address');
			$vars['nalog'] = Settings::getValue('kassa_test_nalog');
			$vars['VATrate'] = Settings::getValue('kassa_test_nds');
		}
		else
		{
			$vars['login'] = trim(Settings::getValue('kassa_login'));
			$vars['password'] = trim(Settings::getValue('kassa_password'));
			$vars['url'] = Settings::getValue('kassa_url');
			$vars['userPass'] = Settings::getValue('kassa_pass');
			$vars['operator'] = Settings::getValue('kassa_operator');
			$vars['buyerAddress'] = Settings::getValue('kassa_address');
			$vars['nalog'] = Settings::getValue('kassa_nalog');
			$vars['VATrate'] = Settings::getValue('kassa_nds');
		}
		return $vars;
	}
	public static function CreateOrder($pay, $isTest = false, $testBattle = false, $testEmail = '')
	{
		//return '';
		$order = $pay->getOrder()->one();
		$user = null;
		$sum = $pay->sum*0.01;
		$orderName = 'Неизвестный пакет';
		if($order)
		{
			$user = $order->GetUser()->one();
			$setName = $order->GetSetName();
			$orderName = $order->GetPackName() . ($setName ? ' (' . $setName . ')': '');
		}
		$vars = static::PrepareVars(true, $isTest, $testBattle);
		if($vars['login'] == '' || $vars['password'] == '')
		{
			return null;
		}
		$bill =
		[
			'bill' =>
			[
				//'kktNumber' => '',
				'userPass' => $vars['userPass'],
				'operationType' => 1,
				'ecashTotalSum' => $sum,
				'buyerAddress' => $vars['buyerAddress'],
				'items' =>
				[
					[
						'name' => $orderName,
						'price' => $sum,
						'quantity' => 1,
						'VATrate' => $vars['VATrate'],
					]
				],
				'operator' => ($vars['operator'] ? $vars['operator'] : null),
			],
		];
		if($user && $user->email)
			$bill['bill']['buyerAddress'] = $user->email;

		$url = $vars['url'];
		if(mb_substr($url, -1) == '/')
			$url .= 'Transaction';
		else
			$url .= '/Transaction';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $vars['login'] . ':' . $vars['password']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bill, JSON_UNESCAPED_UNICODE));
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$res = curl_exec($ch);
		curl_close($ch);
		if($res === false)
			return null;
		return $res; 
	}
}