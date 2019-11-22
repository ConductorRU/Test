<?
if(!CModule::IncludeModule("iblock"))
	return;
class Callback
{
	public static $callId = 1;
	public static $excId = 5;
	public static $vacId = 54;
	public static $orderId = 11;
	public static $costId = 20;
	public static $consulId = 21;
	public static function SendCall($fio, $phone)
	{
		$theme = 'Новый запрос звонка';
		$fields = [];
		$fields['AUTHOR_NAME'] = $fio;
		$fields['AUTHOR_PHONE'] = $phone;
		$fields['THEME'] = $theme;
		$fields['SEND_TO'] = Settings::Get()['email'];
		$res = CEvent::SendImmediate("FEEDBACK_FORM", 's1', $fields);
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => static::$callId,
			"NAME"           => $fio,
			"DETAIL_TEXT"    => $phone,
			"ACTIVE"         => "Y",
			);

		self::sendAddLead($fio, $phone, $theme);

		if($ordId = $el->Add($info))
			return $ordId;
		return 0;
	}
	public static function SendConsul($fio, $phone)
	{
		$theme = 'Новый запрос консультации';
		$fields = [];
		$fields['AUTHOR_NAME'] = $fio;
		$fields['AUTHOR_PHONE'] = $phone;
		$fields['THEME'] = $theme;
		$fields['SEND_TO'] = Settings::Get()['email'];
		$res = CEvent::SendImmediate("FEEDBACK_FORM", 's1', $fields);
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => static::$consulId,
			"NAME"           => $fio,
			"DETAIL_TEXT"    => $phone,
			"ACTIVE"         => "Y",
			);

		self::sendAddLead($fio, $phone, $theme);

		if($ordId = $el->Add($info))
			return $ordId;
		return 0;
	}
	public static function SendOrder($fio, $phone, $desc, $apart_id, $is_cost)
	{
		$apart = Apart::GetApart($apart_id);
		if(!$apart)
			return 0;
		$url = 'http://' . $_SERVER['HTTP_HOST']  . $apart['url'];
		$theme = $is_cost ? 'Узнать стоимость квартиры' : 'Новая заявка на квартиру';
		$fields = [];
		$fio = $fio ? $fio : 'Аноним';
		$fields['NAME'] = $fio;
		$fields['PHONE'] = $phone;
		$fields['COMMENT'] = $desc;
		$fields['APART'] = $url;
		$fields['THEME'] = $theme;
		$fields['SEND_TO'] = Settings::Get()['email'];
		$res = CEvent::SendImmediate("ORDER_FORM", 's1', $fields);
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		$prop = array();
		if($is_cost)
		{
			$prop[81] = $phone;
			$prop[82] = $apart_id;
		}
		else
		{
			$prop[23] = $phone;
			$prop[24] = $apart_id;
		}
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => ($is_cost ? static::$costId : static::$orderId),
			"NAME"           => $fio,
			"DETAIL_TEXT"    => $desc,
			"ACTIVE"         => "Y",
			"PROPERTY_VALUES"=> $prop,
			);

		self::sendAddLead($fio, $phone, $theme);

		if($ordId = $el->Add($info))
			return $ordId;
		return 0;
	}
	public static function SendExcursion($fio, $phone, $date = null)
	{
		$theme = 'Запись на экскурсию';
		$fields = [];
		$fields['AUTHOR_NAME'] = $fio;
		$fields['AUTHOR_PHONE'] = $phone;
		//$fields['AUTHOR_DATE'] = $date;
		$fields['THEME'] = $theme;
		$fields['SEND_TO'] = Settings::Get()['email'];
		$res = CEvent::SendImmediate("FEEDBACK_FORM", 's1', $fields);
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		$prop = array();
		$prop[9] = $phone;
		$prop[10] = $date;
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => static::$excId,
			"NAME"           => $fio,
			"DETAIL_TEXT"    => $phone,
			"ACTIVE"         => "Y",
			"PROPERTY_VALUES"=> $prop,
			);

		self::sendAddLead($fio, $phone, $theme);

		if($ordId = $el->Add($info))
			return $ordId;
		return 0;
	}

	public static function SendVacancy($fio, $phone, $fmail, $mail, $vacancy, $resume)
	{
		$theme = 'Отклик на вакансию: '. $vacancy;
		$fields = [];
		$fields['FIO'] = $fio;
		$fields['PHONE'] = $phone;
		$fields['UMAIL'] = $fmail;
		$fields['THEME'] = $theme;
		$fields['SEND_TO'] = $mail;
		// $res = CEvent::SendImmediate("VACANCY_FORM", 's1', $fields);
		$res = CEvent::Send("VACANCY_FORM", 's1', $fields, 'Y','',$resume);
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		$prop = array();
		$prop[213] = $phone;
		$prop[216] = $fmail;
		$ffile = CFile::MakeFileArray($resume[0]);
		$ffile['MODULE_ID'] = 'main';
		// addMessage2Log(print_r($ffile,true));
		$prop[214] = $ffile;
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => static::$vacId,
			"NAME"           => $fio,
			"DETAIL_TEXT"    => "телефон: " . $phone. " , e-mail:" . $fmail,
			"ACTIVE"         => "Y",
			"PROPERTY_VALUES"=> $prop,
			);

		if($ordId = $el->Add($info)){
			return $ordId;
		}else{
			addMessage2Log(print_r($el->LAST_ERROR,true));
			return 0;
		}
		return 0;
	}

	public static function sendInvest($iblock_id, $fmail, $mail, $doc)
	{
		$fields = [];
		$fields['THEME'] = 'Условия инвестиций в недвижимость';
		$fields['UMAIL'] = $fmail;
		$fields['SEND_TO'] = $fmail;
		// $fields['SEND_TO'] = $mail;
		// $res = CEvent::SendImmediate("VACANCY_FORM", 's1', $fields);
		$res = CEvent::Send("INVEST_FORM", 's1', $fields, 'Y','',[$doc]);
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		// addMessage2Log(print_r($ffile,true));
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => $iblock_id,
			"NAME"           => $fmail,
			"ACTIVE"         => "Y",
			);

		if($ordId = $el->Add($info)){
			return $ordId;
		}else{
			addMessage2Log(print_r($el->LAST_ERROR,true));
			return 0;
		}
		return 0;
	}

	public static function sendRealtor($iblock_id, $mail, $agent, $yur, $inn, $phone)
	{
		$fields = [];
		$fields['THEME'] = 'Заявка на сторудничество';
		$fields['SEND_TO'] = $mail;
		$fields['AGENT'] = $agent;
		$fields['YUR'] = $yur;
		$fields['INN'] = $inn;
		$fields['PHONE'] = $phone;
		// $res = CEvent::SendImmediate("VACANCY_FORM", 's1', $fields);
		$res = CEvent::Send("REALTOR_FORM", 's1', $fields, 'Y','');
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		// addMessage2Log(print_r($ffile,true));
		$prop = [];
		$prop[217] = $yur;
		$prop[218] = $inn;
		$prop[219] = $phone;
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => $iblock_id,
			"NAME"           => $agent,
			"ACTIVE"         => "Y",
			"PROPERTY_VALUES"=> $prop,
			);

		if($ordId = $el->Add($info)){
			return $ordId;
		}else{
			addMessage2Log(print_r($el->LAST_ERROR,true));
			return 0;
		}
		return 0;
	}

	public static function sendChange($iblock_id, $mail, $phone, $apart_giv, $apart_nid)
	{
		$fields = [];
		$fields['THEME'] = 'Заявка на консультацию по программе обмена';
		$fields['SEND_TO'] = $mail;
		$fields['PHONE'] = $phone;
		$fields['APARTGIV'] = $apart_giv;
		$fields['APARTNID'] = $apart_nid;
		// $res = CEvent::SendImmediate("VACANCY_FORM", 's1', $fields);
		$res = CEvent::Send("PROGRAM_CHANGE", 's1', $fields, 'Y','');
		$el = new CIBlockElement;
		$userId = $USER ? $USER->GetID() : null;
		// addMessage2Log(print_r($ffile,true));
		$info = Array(
			"MODIFIED_BY"    => $userId,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => $iblock_id,
			"NAME"           => $phone,
			"ACTIVE"         => "Y",
			"PREVIEW_TEXT"    => $apart_giv,
			"DETAIL_TEXT"    => $apart_nid,
			);

		if($ordId = $el->Add($info)){
			return $ordId;
		}else{
			addMessage2Log(print_r($el->LAST_ERROR,true));
			return 0;
		}
		return 0;
	}

	public static function sendAddLead($fio, $phone, $comment)
	{
		$ch = curl_init();
		curl_setopt_array($ch, [
			\CURLOPT_URL => "https://bik-servis.bitrix24.ru/rest/320/jt5q5b3g1vshllwc/crm.lead.add",
			\CURLOPT_HEADER => false,
			\CURLOPT_RETURNTRANSFER => true,
			\CURLOPT_SSL_VERIFYPEER => false,
			\CURLOPT_POST => true,
			\CURLOPT_POSTFIELDS => http_build_query([
				'fields' => [
					'TITLE' => "{$fio} - {$phone}",
					'NAME' => $fio,
					'STATUS_ID' => 'NEW',
					'OPENED' => 'Y',
					'ASSIGNED_BY_ID' => '242',
					'COMMENTS' => $comment,
					'PHONE' => [
						[
							'VALUE' => $phone,
							'VALUE_TYPE' => 'WORK',
						],
					],
				],
				'params' => array("REGISTER_SONET_EVENT" => "Y")
			]),
		]);
		$rs = curl_exec($ch);
		curl_close($ch);
		$rs = json_decode($rs, 1);
		if (array_key_exists('error', $rs)) {
			addMessage2Log("Ошибка при сохранении лида: " . print_r($rs['error_description'],true));
		}
	}
};
