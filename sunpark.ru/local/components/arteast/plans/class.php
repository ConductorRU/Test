<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Компонент для блока с планировками
 * @property integer $iApartId - id инфоблока с квартирами
 * @property integer $iPlanId - id инфоблока с генпланом
 * @property integer $maxOnPage - максимальное количество планировок на странице
 * @property array $errors - массив ошибок
 */
class PlansComponent extends CBitrixComponent
{
	public $iApartId = 0;
	public $iPlanId = 0;
	public $maxOnPage = 16;
	protected $errors = array();

	/**
	 * Подключение файлов локализации
	 */
	public function onIncludeComponentLang()
	{
		Loc::loadMessages(__FILE__);
	}

	/**
	 * Подготовка данных запроса
	 * 
	 * @param array[string]mixed $arParams - входные параметры
	 * @return array[string]int - обработанные параметры 
	 */
	public function onPrepareComponentParams($arParams)
	{
		$arParams["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ? (int)$arParams["CACHE_TIME"]: 36000000;
		$arParams["APART_ID"] = (int)$arParams["APART_ID"];
		$arParams["PLAN_ID"] = (int)$arParams["PLAN_ID"];
		$arParams["MAX_ON_PAGE"] = (int)$arParams["MAX_ON_PAGE"];
		return $arParams;
	}

	/**
	 * Выполнение основного кода компонента
	 */
	public function executeComponent()
	{
		try
		{
			$this->checkModules();
			$this->getResult();
			$this->includeComponentTemplate();
		}
		catch (SystemException $e)
		{
			ShowError($e->getMessage());
		}
	}

	/**
	 * Подключение и проверка модулей
	 */
	protected function checkModules()
	{
		if(!Loader::includeModule('iblock'))
			throw new SystemException(Loc::getMessage('CPS_MODULE_NOT_INSTALLED', array('#NAME#' => 'iblock')));
	}

	/**
	 * Возвращает список с возможным количеством комнат
	 * 
	 * @return array[int]string - ассоциативный массив [id_комнаты] = Количество комнат
	 */
	protected function getRoomList()
	{
		$ar = [];
		$rows = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID" => $this->iPlanId, "CODE" => "ROOMS"));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["VALUE"];
		return $ar;
	}

	/**
	 * Возвращает минимальную стоимость квартиры указанной планировки
	 * 
	 * @param integer $plan_id - id планировки
	 * @return integer|null - стоимость
	 */
	protected function getMinPlanPrice($plan_id)
	{
		//получаем минимальную цену
		$el = CIBlockElement::GetList(['PROPERTY_PRICE' => 'ASC'], array('IBLOCK_ID' => $this->iApartId, 'PROPERTY_PLAN' => $plan_id, 'ACTIVE' => 'Y', '>=PROPERTY_PRICE' => '0'), array('MAX' => 'PROPERTY_PRICE'), false, array('ID', 'IBLOCK_ID'))->GetNext();
		if($el)
			return (int)$el['PROPERTY_PRICE_VALUE'];
		return null;
	}

	/**
	 * Возвращает минимальную стоимость квартиры указанной планировки
	 * 
	 * @param integer $plan_id - id планировки
	 * @return integer|null - стоимость
	 */
	protected function getApartUrlByPlan($plan_id)
	{
		//проверяем, есть ли хотя бы одна квартира со статусом "Свободно"
		$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => $this->iApartId, 'PROPERTY_PLAN' => $plan_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'PROPERTY_STATUS' => 7), false, Array("nPageSize"=> 1), array('ID', 'DETAIL_PAGE_URL'))->GetNext();
		//если нет, тогда берем квартиру с любым другим статусом
		if(!$res)
			$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => $this->iApartId, 'PROPERTY_PLAN' => $plan_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y'), false, Array("nPageSize"=> 1), array('ID', 'DETAIL_PAGE_URL'))->GetNext();
		
		//возвращаем ссылку на квартиру
		if($res)
			return $res['DETAIL_PAGE_URL'];
		return null;
	}

	/**
	 * Получает данные планировок
	 * 
	 * @param integer $room_id - id свойства, указывающего количество комнат
	 * @return array[array] - массив данных планировок
	 */
	protected function getPlans($room_id)
	{
		$plans = [];
		$res = CIBlockElement::GetList(Array('RAND' => 'ASC'), Array('IBLOCK_ID' => $this->iPlanId, 'PROPERTY_ROOMS' => $room_id, 'PROPERTY_ON_MAIN' => 28), false, Array("nPageSize" => $this->maxOnPage));
		while($ob = $res->GetNextElement())
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			$pl = [];
			$pl['id'] = $row['ID'];
			$pl['name'] = $row['NAME'];
			$pl['rooms_id'] = $props['ROOMS']['VALUE_ENUM_ID'];
			$pl['rooms'] = $props['ROOMS']['VALUE'];
			$pl['family_id'] = $props['FAMILY']['VALUE_ENUM_ID'];
			$pl['family'] = $props['FAMILY']['VALUE'];
			$pl['square'] = $props['SQUARE']['VALUE'];
			$img = CFile::GetFileArray($row['PREVIEW_PICTURE']);
			$pl['thumbnail'] = $img ? $img['SRC'] : '';
			$img = CFile::GetFileArray($row['DETAIL_PICTURE']);
			$pl['image'] = $img ? $img['SRC'] : '';
			$pl['minprice'] = $this->getMinPlanPrice($pl['id']);
			$pl['url'] = $this->GetApartUrlByPlan($pl['id']);
			$plans[] = $pl;
		}
		return $plans;
	}

	/**
	 * Результат обработки запроса
	 */
	protected function getResult()
	{
		if(count($this->errors))
			throw new SystemException(current($this->errors));
		$arParams = $this->arParams;
		$this->iPlanId = $arParams["PLAN_ID"];
		$this->iApartId = $arParams["APART_ID"];
		$this->maxOnPage = $arParams["MAX_ON_PAGE"];

		$arResult = [];
		$additionalCacheID = false;
		if($this->startResultCache($arParams['CACHE_TIME'], $additionalCacheID))
		{
			$plans = [];
			$rooms = $this->GetRoomList();
			$names = [3 => '1 комнатные', 4 => '2 комнатные', 5 => '3 комнатные'];
			foreach($rooms as $n => $v)
			{
				$p = [];
				$p['id'] = $n;
				$p['name'] = $v;
				if(isset($names[$n]))
					$p['name'] = $names[$n];
				
				//получаем список планировок с текущим количеством комнат
				$p['plans'] = $this->getPlans($n);
				if(count($p['plans']))
					$plans[] = $p;
			}
			$arResult['plans'] = $plans;

			$this->arResult = $arResult;
		}
	}
}