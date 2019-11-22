<?

//класс для поключения библиотеки определения мобильных устройств (используется для подключения мобильного шаблона сайта)
use \Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(null, array(
    // '\Olepro\Classes\Helpers\MobileDetect' => '/local/php_interface/classes/helpers/mobiledetect.php',
    // 'MobileDetect' => '/local/mobile_detect/Mobile_Detect.php',
));

CModule::AddAutoloadClasses('',
[
	'Callback' => '/local/php_interface/callback.php',
	'Settings' => '/local/php_interface/settings.php',
	'Apart' => '/local/php_interface/apart.php',
	'MobileDetect' => '/local/php_interface/mobiledetect.php',
]
);

//импорт статусов и цен квартир из Битрикс24
function importB24()
{
  //addMessage2log("начало обмена с Битрикс24");
  $sets = Settings::Get();
  $txt = file_get_contents($sets['profit']);
  $root = new SimpleXMLElement($txt);
  $i = 0;
  $houses = Apart::GetHouseListEx();
  $stats = Apart::GetStatusList();
  //file_put_contents('log.txt', date());
  foreach($root->offer as $offer)
  {
  	$change = 0;
  	$inId = (int)$offer['internal-id'];
  	$houseId = (int)$offer->house->id;
  	$num = (int)$offer->number;
  	$price = (int)$offer->price->value;
  	$floor = (int)$offer->floor;
  	$status = (string)$offer->status;
  	$name = 'Квартира №' . $num;

  	$pros = array();
  	$pros['NUMBER'] = $num;
  	$pros['PRICE'] = $price;
  	$pros['FLOOR'] = $floor;
  	$pros['STATUS'] = null;
  	switch($status)
  	{
  		case 'UNAVAILABLE': $pros['STATUS'] = 6; break;
  		case 'AVAILABLE': $pros['STATUS'] = 7; break;
  		case 'BOOKED': $pros['STATUS'] = 8; break;
  		case 'EXECUTION': $pros['STATUS'] = 9; break;
  		case 'SOLD': $pros['STATUS'] = 10; break;
  	}
  	$ob = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => 4, 'PROPERTY_PROFITBASE_ID' => $inId, '!ID' => $id), false)->GetNextElement();
  	if(!$ob)
  	{
  		if(isset($houses[$houseId][0]))
  		{
  			$ob = CIBlockElement::GetList(Array(),  Array('IBLOCK_ID' => 4, 'IBLOCK_SECTION_ID' => $houses[$houseId][0], 'PROPERTY_NUMBER' => $num), false)->GetNextElement();
  			if(!$ob)
  				echo '<span style="color:red">Ошибка! Квартира №"' . $num . ' (' . $houses[$houseId][1] . ') не найдена</span><br>';
  		}
  		else
  			echo '<span style="color:red">Ошибка! Дом с идентификатором "' . $houseId . '" не найден</span><br>';
  	}
  	if($ob)
  	{
  		$row = $ob->GetFields();
  		$props = $ob->GetProperties();
  		$pros['PROFITBASE_ID'] = $inId;
  		if($row['NAME'] != $name)
  		{
  			echo '<span style="color:green">Квартира №"' . $props['NUMBER']['VALUE'] . ' (' . $houses[$houseId][1] . '): изменение названия с ' . $row['NAME'] . ' на ' . $name . '</span><br>';
  			$change = 1;
  		}
  		if((int)$props['NUMBER']['VALUE'] != $num)
  		{
  			echo '<span style="color:green">Квартира №"' . $props['NUMBER']['VALUE'] . ' (' . $houses[$houseId][1] . '): изменение номера с ' . $props['NUMBER']['VALUE'] . ' на ' . $num . '</span><br>';
  			$change = 1;
  		}
  		if((int)$props['PRICE']['VALUE'] != $price)
  		{
  			echo '<span style="color:green">Квартира №"' . $num . ' (' . $houses[$houseId][1] . '): изменение цены с ' . $props['PRICE']['VALUE'] . ' на ' . $price . '</span><br>';
  			$change = 1;
  		}
  		if((int)$props['FLOOR']['VALUE'] != $floor)
  		{
  			echo '<span style="color:green">Квартира №"' . $num . ' (' . $houses[$houseId][1] . '): изменение этажа с ' . $props['FLOOR']['VALUE'] . ' на ' . $floor . '</span><br>';
  			$change = 1;
  		}
  		if((int)$props['STATUS']['VALUE_ENUM_ID'] != $pros['STATUS'])
  		{
  			echo '<span style="color:green">Квартира №"' . $num . ' (' . $houses[$houseId][1] . '): изменение статуса с ' . $stats[$props['STATUS']['VALUE_ENUM_ID']] . ' на ' . $stats[$pros['STATUS']] . '</span><br>';
  			$change = 1;
  		}
  		if($change)
  		{
  			$el = new CIBlockElement;
  			if($el->Update($row['ID'], Array("NAME" => $name)))
  			{
  				CIBlockElement::SetPropertyValuesEx($row['ID'], false, $pros);
  				echo '<span style="color:green">Квартира №"' . $num . ' (' . $houses[$houseId][1] . ') успешно обновлена</span><br>';
  			}
  			else
  				echo '<span style="color:red">Квартира №"' . $num . ' (' . $houses[$houseId][1] . ') - ошибка обновления</span><br>';
  		}
  	}
  	//break;
  }
  //addMessage2log("обмен с Битрикс24 завершен");
  return "importB24();";
}
