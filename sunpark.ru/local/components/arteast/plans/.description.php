<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription =
[
	"NAME" => Loc::getMessage('ARTEAST_PLANS_DESCRIPTION_NAME'),
	"DESCRIPTION" => Loc::getMessage('ARTEAST_PLANS_DESCRIPTION_DESCRIPTION'),
	"PATH" =>
	[
		"ID" => 'arteast',
		"NAME" => Loc::getMessage('ARTEAST_DESCRIPTION_GROUP'),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => 'sunpark',
			"NAME" => Loc::getMessage('ARTEAST_DESCRIPTION_DIR'),
			"SORT" => 10
		)
	]
];