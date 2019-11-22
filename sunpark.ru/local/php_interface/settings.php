<?
if(!CModule::IncludeModule("iblock"))
	return;
class Settings
{
	public static $iblockId = 2;
	public static $sets = [];
	public static function Get()
	{
		if(!count(static::$sets))
		{
			$ob = CIBlockElement::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => static::$iblockId, 'ACTIVE' => 'Y', "ACTIVE_DATE"=>"Y"])->GetNextElement();
			if($ob)
			{
				$row = $ob->GetFields();
				$props = $ob->GetProperties();
				static::$sets['blockId'] = static::$iblockId;
				static::$sets['id'] = $row['ID'];
				static::$sets['socialFB'] = $props['SOCIAL_FACEBOOK']['VALUE'];
				static::$sets['socialIN'] = $props['SOCIAL_INSTAGRAM']['VALUE'];
				static::$sets['socialVK'] = $props['SOCIAL_VK']['VALUE'];
				static::$sets['socialYT'] = $props['SOCIAL_YOUTUBE']['VALUE'];
				static::$sets['map_scale'] = $props['MAP_SCALE']['VALUE'];
				static::$sets['slider_pos'] = $props['SLIDER_POS']['VALUE_ENUM_ID'];
				static::$sets['genplan_title'] = $props['GENPLAN_TITLE']['VALUE'];
				static::$sets['email'] = $props['EMAIL']['VALUE'];
				static::$sets['profit'] = $props['PROFIT_URL']['VALUE'];
				static::$sets['excurse'] = $props['EX_TEXT']['~VALUE']['TEXT'];
				static::$sets['butExcurse'] = $props['EX_BUTTON']['VALUE'];
				static::$sets['butSlider'] = $props['BUT_SLIDER']['VALUE'];
				static::$sets['butSun'] = $props['BUT_SUN']['VALUE'];
				static::$sets['butTypes'] = $props['BUT_TYPES']['VALUE'];
				static::$sets['butCost'] = $props['BUT_COST']['VALUE'];
				static::$sets['butOrder'] = $props['BUT_ORDER']['VALUE'];
				static::$sets['checked'] = (int)($props['CHECKBOX']['VALUE_ENUM_ID'] == 36);
				static::$sets['excurse_name'] = (int)($props['EX_NAME']['VALUE_ENUM_ID'] == 37);
				static::$sets['remove_cost'] = (int)($props['REMOVE_COST']['VALUE_ENUM_ID'] == 38);
				static::$sets['blocksOrder'] = $props['BLOCKS_ORDER']['VALUE'];
				
				static::$sets['yandexMetric'] = $props['YANDEX_METRIC']['~VALUE']['TEXT'];
				static::$sets['yandexCall'] = $props['YANDEX_CALL']['VALUE'];
				static::$sets['yandexExcurse'] = $props['YANDEX_EXCURSE']['VALUE'];
				static::$sets['yandexOrder'] = $props['YANDEX_ORDER']['VALUE'];
				static::$sets['yandexSubscribe'] = $props['YANDEX_SUBSCRIBE']['VALUE'];
				static::$sets['yandexPrice'] = $props['YANDEX_PRICE']['VALUE'];
				static::$sets['yandexConsul'] = $props['YANDEX_CONSUL']['VALUE'];
				static::$sets['yandexCallSend'] = $props['YANDEX_CALL_SEND']['VALUE'];
				static::$sets['yandexExcurseSend'] = $props['YANDEX_EXCURSE_SEND']['VALUE'];
				static::$sets['yandexOrderSend'] = $props['YANDEX_ORDER_SEND']['VALUE'];
				static::$sets['yandexSubscribeSend'] = $props['YANDEX_SUBSCRIBE_SEND']['VALUE'];
				static::$sets['yandexPriceSend'] = $props['YANDEX_PRICE_SEND']['VALUE'];
				static::$sets['yandexConsulSend'] = $props['YANDEX_CONSUL_SEND']['VALUE'];
				static::$sets['yandexTextTypes'] = $props['TEXT_TYPES']['VALUE'];
				static::$sets['planText'] = $props['PLAN_TEXT']['~VALUE']['TEXT'];
				static::$sets['infraText'] = $props['INFR_TEXT']['~VALUE']['TEXT'];
				static::$sets['footerText'] = $props['FOOTER_TEXT']['~VALUE']['TEXT'];
				
				static::$sets['hide_demo'] = ($props['DEMO_APARTS']['VALUE_ENUM_ID'] == 12);
				static::$sets['demos'] = [];
				$demos = $props['DEMO_PHOTO']['VALUE'];
				foreach($demos as $demo)
				{
					$cfile = CFile::GetFileArray($demo);
					if($cfile)
						static::$sets['demos'][] = $cfile['SRC'];
				}
				$mImg = $props['MAIN_PHOTO']['VALUE'];
				$cfile = CFile::GetFileArray($mImg);
				static::$sets['main_photo'] = $cfile ? $cfile['SRC'] : '';
			}
		}
		return static::$sets;
	}
	public static function SetMeta($blockId, $id, $img)
	{
		global $APPLICATION;
		$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($blockId, $id);
		$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
		if($arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])
		{
			$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
			$APPLICATION->SetPageProperty("og:title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
		}
		if($arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"])
		{
			$APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]);
			$APPLICATION->SetPageProperty("og:keywords", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]);
		}
		if($arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
		{
			$APPLICATION->SetPageProperty("description", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
			$APPLICATION->SetPageProperty("og:description", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
		}
		if($img)
			$APPLICATION->SetPageProperty("og:image", 'https://' . $_SERVER['HTTP_HOST'] . $img);
		else
			$APPLICATION->SetPageProperty("og:image", 'https://sunpark45.ru/local/templates/sunpark/img/logod.png');
	}
	public static function SetMetas($title, $desc, $keys, $img)
	{
		global $APPLICATION;
		if($title)
		{
			$APPLICATION->SetPageProperty("title", $title);
			$APPLICATION->SetPageProperty("og:title", $title);
		}
		if($arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"])
		{
			$APPLICATION->SetPageProperty("keywords", $keys);
			$APPLICATION->SetPageProperty("og:keywords", $keys);
		}
		if($desc)
		{
			$APPLICATION->SetPageProperty("description", $desc);
			$APPLICATION->SetPageProperty("og:description", $desc);
		}
		if($img)
			$APPLICATION->SetPageProperty("og:image", $img);
		else
			$APPLICATION->SetPageProperty("og:image", 'https://sunpark45.ru/local/templates/sunpark/img/logod.png');
	}
};