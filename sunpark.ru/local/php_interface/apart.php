<?
if(!CModule::IncludeModule("iblock"))
	return;
class Apart
{
	private static $apartId = 4;
	private static $planId = 6;
	private static $newsId = 12;
	private static $baloonId = 17;
	private static $plans = [];
	private static $houses = [];
	static public function NumberCase($n, $one, $two, $five)
	{
		if($n >= 5 && $n <= 20)
			return $five;
		if($n%10 == 1)
			return $one;
		if($n%10 >= 2 && $n%10 <= 4)
			return $two;
		return $five;
	}
	public static function GetMinPlanPrice($id)
	{
		$works = CIBlockElement::GetList(['PROPERTY_PRICE' => 'ASC'], array('IBLOCK_ID' => static::$apartId, 'PROPERTY_PLAN' => $id, 'ACTIVE' => 'Y', '>=PROPERTY_PRICE' => '0'), array('MAX' => 'PROPERTY_PRICE'), false, array('ID', 'IBLOCK_ID'))->GetNext();
		if($works)
			return (int)$works['PROPERTY_PRICE_VALUE'];
		return null;
	}
	public static function GetMinPrice()
	{
		$works = CIBlockElement::GetList(['PROPERTY_PRICE' => 'ASC'], array('IBLOCK_ID' => static::$apartId, 'ACTIVE' => 'Y', '>=PROPERTY_PRICE' => '0'), array('MAX' => 'PROPERTY_PRICE'), false, array('ID', 'IBLOCK_ID'))->GetNext();
		if($works)
			return (int)$works['PROPERTY_PRICE_VALUE'];
		return null;
	}
	public static function GetMaxPrice()
	{
		$works = CIBlockElement::GetList(['PROPERTY_PRICE' => 'DESC'], array('IBLOCK_ID' => static::$apartId, 'ACTIVE' => 'Y', '>=PROPERTY_PRICE' => 0), array('MAX' => 'PROPERTY_PRICE'), false, array('ID', 'IBLOCK_ID'))->GetNext();
		if($works)
			return (int)$works['PROPERTY_PRICE_VALUE'];
		return null;
	}
	public static function GetMinSquare()
	{
		$works = CIBlockElement::GetList(['PROPERTY_SQUARE' => 'ASC'], array('IBLOCK_ID' => static::$planId, 'ACTIVE' => 'Y', '>=PROPERTY_SQUARE' => '0'), array('MAX' => 'PROPERTY_SQUARE'), false, array('ID', 'IBLOCK_ID'))->GetNext();
		if($works)
			return (int)$works['PROPERTY_SQUARE_VALUE'];
		return null;
	}
	public static function GetMaxSquare()
	{
		$works = CIBlockElement::GetList(['PROPERTY_SQUARE' => 'DESC'], array('IBLOCK_ID' => static::$planId, 'ACTIVE' => 'Y', '>=PROPERTY_SQUARE' => 0), array('MAX' => 'PROPERTY_SQUARE'), false, array('ID', 'IBLOCK_ID'))->GetNext();
		if($works)
			return (int)$works['PROPERTY_SQUARE_VALUE'];
		return null;
	}
	public static function GetRoomList()
	{
		$ar = [];
		$rows = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>static::$planId, "CODE"=>"ROOMS"));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["VALUE"];
		return $ar;
	}
	public static function GetDecorList()
	{
		$ar = [];
		$rows = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>static::$apartId, "CODE"=>"DECOR"));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["VALUE"];
		return $ar;
	}
	public static function GetStatusList()
	{
		$ar = [];
		$rows = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>static::$apartId, "CODE"=>"STATUS"));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["VALUE"];
		return $ar;
	}
	public static function GetFamilyList()
	{
		$ar = [];
		$rows = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>static::$planId, "CODE"=>"FAMILY"));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["VALUE"];
		return $ar;
	}
	public static function GetDeadlineList()
	{
		$ar = [];
		$rows = CUserFieldEnum::GetList(array("SORT"=>"ASC"), array("NAME" => "UF_DEADLINE"));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["VALUE"];
		return $ar;
	}
	public static function GetHouseList()
	{
		$ar = [];
		$rows = CIBlockSection::GetList(array("SORT"=>"ASC"), array('IBLOCK_ID' => static::$apartId, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y'), false, array('ID', 'NAME'));
		while($row = $rows->GetNext())
			$ar[$row["ID"]] = $row["NAME"];
		return $ar;
	}
	public static function GetHouseListEx()
	{
		$ar = [];
		$rows = CIBlockSection::GetList(array("SORT"=>"ASC"), array('IBLOCK_ID' => static::$apartId), false, array('ID', 'NAME', 'UF_PROFITBASE'));
		while($row = $rows->GetNext())
			$ar[$row["UF_PROFITBASE"]] = [$row["ID"], $row["NAME"]];
		return $ar;
	}
	public static function GetFloorList()
	{
		$ar = [];
		$rows = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$apartId, 'ACTIVE' => 'Y'), false, false, array('ID', 'PROPERTY_FLOOR'));
		while($row = $rows->GetNext())
		{
			$num = (int)$row['PROPERTY_FLOOR_VALUE'];
			if($num && !isset($ar[$num]))
				$ar[$num] = $num . ' этаж';
		}
		ksort($ar);
		return $ar;
	}
	public static function GetApartInfo($row, $props)
	{
		$plan = Apart::GetPlan($props['PLAN']['VALUE']);
		$house = Apart::GetHouse($row['IBLOCK_SECTION_ID']);
		$p = [];
		$p['id'] = $row['ID'];
		$p['blockId'] = $row['IBLOCK_ID'];
		$p['sectionId'] = $row['IBLOCK_SECTION_ID'];
		$p['plan_id'] = $plan['id'];
		$p['num'] = $props['NUMBER']['VALUE'];
		$p['floor_id'] = $props['FLOOR_ID']['VALUE'];
		$p['price'] = $props['PRICE']['VALUE'];
		$p['ipo'] = $props['PRICE_MONTH']['VALUE'];
		$p['img'] = $plan['thumbnail'];
		$p['image'] = $plan['image'];
		$p['rooms'] = $plan['rooms'];
		$p['name'] = $p['rooms'] . ' №' . $p['num'];
		$p['fullname'] = $p['rooms'] . ' квартира №' . $p['num'];
		$p['house'] = $house['num'];
		$p['pays'] = $house['pays'];
		$p['house_id'] = $house['id'];
		$p['deadline'] = $house['deadline'];
		$p['floor'] = (int)$props['FLOOR']['VALUE'];
		$p['square'] = $plan['square'];
		$p['decor'] = $props['DECOR']['VALUE'];
		$p['floors'] = $house['floors'];
		$p['url'] = $row['DETAIL_PAGE_URL'];
		$p['floor_text'] = $p['floor'] . '/' . $p['floors'];
		$p['floor_texta'] = $p['floor'] . ' из ' . $p['floors'];
		$p['status'] = $props['STATUS']['VALUE'];
		$p['status_id'] = $props['STATUS']['VALUE_ENUM_ID'];
		$p['is_buy'] = (int)($p['status_id'] == 7);
		$p['is_soon'] = (int)($p['is_buy'] && !$house['active']);
		$p['slider_img'] = $props['GALLERY']['VALUE'];
		$p['video'] = $props['YOUTUB']['~VALUE']['TEXT'];
		$p['slider_video'] = $props['SLIDE_VIDEO']['VALUE'];
		if($p['is_soon'])
			$p['is_buy'] = 0;
		return $p;
	}
	public static function GetPlanInfo($row, $props)
	{
		$p = [];
		$p['id'] = $row['ID'];
		$p['name'] = $row['NAME'];
		$p['rooms_id'] = $props['ROOMS']['VALUE_ENUM_ID'];
		$p['rooms'] = $props['ROOMS']['VALUE'];
		$p['family_id'] = $props['FAMILY']['VALUE_ENUM_ID'];
		$p['family'] = $props['FAMILY']['VALUE'];
		$p['square'] = $props['SQUARE']['VALUE'];
		$img = CFile::GetFileArray($row['PREVIEW_PICTURE']);
		$p['thumbnail'] = $img ? $img['SRC'] : '';
		$img = CFile::GetFileArray($row['DETAIL_PICTURE']);
		$p['image'] = $img ? $img['SRC'] : '';
		return $p;
	}
	public static function GetApart($id)
	{
		$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$apartId, 'ID' => $id), false);
		if($ob = $res->GetNextElement())
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			return Apart::GetApartInfo($row, $props);
		}
		return null;
	}
	public static function GetApartUrlByPlan($plan_id)
	{
		$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$apartId, 'PROPERTY_PLAN' => $plan_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'PROPERTY_STATUS' => 7), false, Array("nPageSize"=> 1), array('ID', 'DETAIL_PAGE_URL'))->GetNext();
		if(!$res)
			$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$apartId, 'PROPERTY_PLAN' => $plan_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y'), false, Array("nPageSize"=> 1), array('ID', 'DETAIL_PAGE_URL'))->GetNext();
		if($res)
			return $res['DETAIL_PAGE_URL'];
		return null;
	}
	public static function GetSimilar($plan_id, $id, $count = 4)
	{
		$ar = [];
		$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$apartId, 'PROPERTY_PLAN' => $plan_id, '!ID' => $id), false, ["nPageSize"=>$count]);
		while($ob = $res->GetNextElement())
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			$ar[] = Apart::GetApartInfo($row, $props);
		}
		return $ar;
	}
	public static function GetPlan($id)
	{
		if(static::$plans[$id])
			return static::$plans[$id];
		$ob = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$planId, 'ID' => $id), false)->GetNextElement();
		if($ob)
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			static::$plans[$id] = Apart::GetPlanInfo($row, $props);
			return static::$plans[$id];
		}
		return null;
	}
	public static function GetBaloons()
	{
		$res = [];
		$r = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$baloonId, 'ACTIVE' => 'Y'), false);
		while($ob = $r->GetNextElement())
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			$b = [];
			$b['id'] = $row['ID'];
			$b['name'] = $row['NAME'];
			$b['text'] = $row['DETAIL_TEXT'];
			$b['x'] = $props['X']['VALUE'];
			$b['y'] = $props['Y']['VALUE'];
			$b['x2'] = $props['X2']['VALUE'];
			$b['y2'] = $props['Y2']['VALUE'];
			$b['angle1'] = ($props['ANGLE']['VALUE_ENUM_ID'] == 39);
			$b['angle2'] = ($props['ANGLE2']['VALUE_ENUM_ID'] == 40);
			$res[] = $b;
		}
		return $res;
	}
	public static function GetHouse($id)
	{
		if(static::$houses[$id])
			return static::$houses[$id];
		$ob = CIBlockSection::GetList(array(), array('IBLOCK_ID' => static::$apartId, 'ID' => $id), false, array("UF_*"))->GetNextElement();
		if($ob)
		{
			$row = $ob->GetFields();
			$p = [];
			$p['id'] = $row['ID'];
			$p['name'] = $row['NAME'];
			$p['active'] = (int)($row['ACTIVE'] == 'Y');
			$props = CUserFieldEnum::GetList(array(), array("ID" => $row['UF_DEADLINE'], "NAME" => "UF_DEADLINE"));
			$p['deadline'] = (($prop = $props->GetNext()) ? $prop["VALUE"] : '');
			$p['deadline_id'] = $row["UF_DEADLINE"];
			$p['floors'] = $row['UF_FLOOR_MAX'];
			$p['pays'] = $row['UF_PAYS'];
			$p['num'] = $row['UF_NUMBER'];
			static::$houses[$id] = $p;
			return $p;
		}
		return null;
	}
	public static function GetFastAction()
	{
		$p = null;
		$ob = CIBlockElement::GetList(Array('RAND' => 'ASC'),  Array('IBLOCK_ID' => static::$newsId, 'PROPERTY_IS_ACTION' => '24'), false)->GetNextElement();
		if($ob)
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			$p = [];
			$p['name'] = $row['NAME'];
			$p['url'] = $row['DETAIL_PAGE_URL'];
			$img = CFile::GetFileArray($props['ICON']['VALUE']);
			$p['icon'] = $img ? $img['SRC'] : null;
		}
		return $p;
	}
	public static function GetMobileAction()
	{
		$p = null;
		$ob = CIBlockElement::GetList(Array('RAND' => 'ASC'),  Array('IBLOCK_ID' => static::$newsId, 'PROPERTY_IS_ACTION' => '24'), false)->GetNextElement();
		if($ob)
		{
			$row = $ob->GetFields();
			$props = $ob->GetProperties();
			$p = [];
			$p['name'] = $props['MOBILE_TITLE']['VALUE'];
			$p['url'] = $row['DETAIL_PAGE_URL'];
		}
		return $p;
	}
	public static function GetFloorPlan($house_id, $floor)
	{
		$ob = CIBlockSection::GetList(array(), array('IBLOCK_ID' => static::$apartId, 'ID' => $house_id), false, array("UF_*"))->GetNextElement();
		if($ob)
		{
			$row = $ob->GetFields();
			$p = [];
			$p['id'] = $row['ID'];
			$p['name'] = $row['NAME'];
			if($floor == 1)
			{
				$img = CFile::GetFileArray($row['UF_PLAN1']);
				$p['plan'] = $img ? $img['SRC'] : '';
			}
			else
			{
				$img = CFile::GetFileArray($row['UF_PLAN2']);
				$p['plan'] = $img ? $img['SRC'] : '';
			}

			$ar = [];
			$res = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => static::$apartId, 'IBLOCK_SECTION_ID' => $row['ID'], 'PROPERTY_FLOOR' => $floor), false);
			while($op = $res->GetNextElement())
			{
				$arow = $op->GetFields();
				$aprops = $op->GetProperties();
				$a = [];
				$a['id'] = (int)$arow['ID'];
				$a['num'] = (int)$aprops['FLOOR_ID']['VALUE'];
				$a['rooms'] = 0;
				$plan = Apart::GetPlan($aprops['PLAN']['VALUE']);
				if($plan)
					$a['rooms'] = (int)$plan['rooms_id'];
				$a['status'] = $aprops['STATUS']['VALUE'];
				$a['status_id'] = (int)$aprops['STATUS']['VALUE_ENUM_ID'];
				$ar[] = $a;
			}
			$p['aparts'] = $ar;
			return $p;
		}
		return null;
	}
};
