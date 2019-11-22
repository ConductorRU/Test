<?
CModule::IncludeModule("iblock");
$dbIBlock = CIBlock::GetList(array("sort" => "asc"), array("ACTIVE" => "Y"));
$r = [];
while ($arIBlock = $dbIBlock->Fetch())
	$r[$arIBlock["ID"]] = "[".$arIBlock["ID"]."] ".$arIBlock["NAME"];
$arComponentParameters =
[
	"GROUPS" =>
	[
		"SETTINGS" => ["NAME" => 'Настройки']
	],
	"PARAMETERS" =>
	[
		"IBLOCK_ID" =>
		[
			 "PARENT" => "BASE",
			 "NAME" => 'Инфоблок',
			 "TYPE" => "LIST",
			 "ADDITIONAL_VALUES" => "Y",
			 "VALUES" => $r,
			 "REFRESH" => "Y"
		],
	]
];