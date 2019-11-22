<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock"))
	return;

$arFilter = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID']);
$res = CIBlockElement::GetList(Array(), $arFilter, false);
$arResult['gens'] = [];
$sets = Settings::Get();
$sTitle = $sets['genplan_title'] ? $sets['genplan_title'] : 'Срок строительства:';
while($ob = $res->GetNextElement())
{
	$p = [];
	$row = $ob->GetFields();
	$props = $ob->GetProperties();
	$p['id'] = $row['ID'];
	$p['name'] = $row['NAME'];
	$p['deadline'] = $props['DATE']['VALUE'];
	$p['number'] = $props['NUMBER']['VALUE'];
	$p['tip1'] = $props['PLAN_TOP']['VALUE'];
	$p['tip2'] = $props['PLAN_BOTTOM']['VALUE'];
	
	$cfile = CFile::GetFileArray($props['GEN_PHOTO1']['VALUE']);
	$p['image1'] = $cfile ? $cfile['SRC'] : null;
	$cfile = CFile::GetFileArray($props['GEN_PHOTO2']['VALUE']);
	$p['image2'] = $cfile ? $cfile['SRC'] : null;
	$cfile = CFile::GetFileArray($props['GEN_FLOOR']['VALUE']);
	$p['floor'] = $cfile ? $cfile['SRC'] : null;
	$p['css1'] = $props['GEN_CSS1']['VALUE'];
	$p['css2'] = $props['GEN_CSS2']['VALUE'];
	$p['active'] = (int)($row['ACTIVE'] == 'Y');
	$p['tip'] = '';
	if($p['deadline'])
		$p['tip'] = $sTitle . ' <b>' . $p['deadline'] . '</b>';
	/*if($p['active'])
	{
		$count = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => 4, 'SECTION_ID' => $props['HOUSES']['VALUE'], 'PROPERTY_STATUS' => 7), false)->SelectedRowsCount();
		if($count)
			$p['tip'] = 'Свободно: <b>' . $count . ' ' . Apart::NumberCase($count, 'квартира', 'квартиры', 'квартир') . '</b>';
		else
			$p['tip'] = 'Свободные квартиры отсутствуют';
	}*/
	$arResult['gens'][] = $p;
}
$this->IncludeComponentTemplate();