<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main;

Loc::loadMessages(__FILE__);

try
{
	if(!Main\Loader::includeModule('iblock'))
		throw new Main\LoaderException(Loc::getMessage('ARTEAST_PLANS_PARAMETERS_IBLOCK_ERROR'));

	$iblockTypes = CIBlockParameters::GetIBlockTypes(Array("-" => " "));
	$iPBlocks = array(0 => " ");
	if(isset($arCurrentValues['PLAN_IBLOCK_TYPE']) && strlen($arCurrentValues['PLAN_IBLOCK_TYPE']))
	{
		$iterator = CIBlock::GetList(array('SORT' => 'ASC'), ['TYPE' => $arCurrentValues['PLAN_IBLOCK_TYPE'], 'ACTIVE' => 'Y']);
		while($iblock = $iterator->GetNext())
			$iPBlocks[$iblock['ID']] = "[" . $iblock["ID"] . "] " . $iblock['NAME'];
	}

	$iABlocks = array(0 => " ");
	if(isset($arCurrentValues['APART_IBLOCK_TYPE']) && strlen($arCurrentValues['APART_IBLOCK_TYPE']))
	{
		$iterator = CIBlock::GetList(array('SORT' => 'ASC'), ['TYPE' => $arCurrentValues['APART_IBLOCK_TYPE'], 'ACTIVE' => 'Y']);
		while($iblock = $iterator->GetNext())
			$iABlocks[$iblock['ID']] = "[" . $iblock["ID"] . "] " . $iblock['NAME'];
	}

	$arComponentParameters =
	[
		"GROUPS" =>
		[
			"SETTINGS" => ["NAME" => 'Настройки'],
		],
		"PARAMETERS" =>
		[
			'PLAN_IBLOCK_TYPE' =>
			[
				'PARENT' => 'BASE',
				'NAME' => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_PLAN_TYPE'),
				'TYPE' => 'LIST',
				'VALUES' => $iblockTypes,
				'DEFAULT' => '',
				'REFRESH' => 'Y'
			],
			"PLAN_ID" =>
			[
				"PARENT" => "BASE",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_PLAN_IBLOCK'),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "Y",
				"VALUES" => $iPBlocks,
				"REFRESH" => "Y"
			],
			'APART_IBLOCK_TYPE' =>
			[
				'PARENT' => 'BASE',
				'NAME' => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_APART_TYPE'),
				'TYPE' => 'LIST',
				'VALUES' => $iblockTypes,
				'DEFAULT' => '',
				'REFRESH' => 'Y'
			],
			"APART_ID" =>
			[
				"PARENT" => "BASE",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_APART_IBLOCK'),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "Y",
				"VALUES" => $iABlocks,
				"REFRESH" => "Y"
			],
			"MAX_ON_PAGE" =>
			[
				"PARENT" => "BASE",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_MAX_ON_PAGE'),
				"TYPE" => "INTEGER",
				"REFRESH" => "N"
			],
			"CATALOG_BTN_TEXT" =>
			[
				"PARENT" => "VISUAL",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_CATALOG_BTN_TEXT'),
				"TYPE" => "STRING",
			],
			"CATALOG_BTN_COLOR" =>
			[
				"PARENT" => "VISUAL",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_CATALOG_BTN_COLOR'),
				"TYPE" => "STRING",
			], 
			"CALL_BTN_TEXT" =>
			[
				"PARENT" => "VISUAL",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_CALL_BTN_TEXT'),
				"TYPE" => "STRING",
			],
			"CALL_BTN_COLOR" =>
			[
				"PARENT" => "VISUAL",
				"NAME" => Loc::getMessage('ARTEAST_PLANS_PARAMETERS_CALL_BTN_COLOR'),
				"TYPE" => "STRING",
			],
			'CACHE_TIME' => array('DEFAULT' => 3600)
		]
	];
}
catch(Main\LoaderException $e)
{
	ShowError($e->getMessage());
}