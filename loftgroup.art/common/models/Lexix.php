<?php
namespace common\models;
use Yii;
class Lexix
{
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
	static public function WordCase($n, $pre, $word, $suffix = '')
	{
		switch($n)
		{
			case 6:
			{
				if($suffix == 'край')
					$suffix = 'крае';
				if($suffix == 'обл')
					$suffix = 'области';
				$val =
				[
					'а' => 'е',
					'ци' => 'цах',
					'[б|в|г|д|ж|з|к|л|м|н|п|р|с|т|х|ц|ч|ш|щ]' => '$3е',
					'ь' => 'и',
					'нкте' => 'нкт',
					'кий' => 'ком',
					'ый' => 'ом',
					'ий' => 'ем',
					'ая' => 'ой',
					'ай' => 'ае',
					'яя' => 'ей',
					'ое' => 'ом',
					'ее' => 'ем',
					'ые' => 'ых',
					'ие' => 'их',
				];
				$suf = [];
				$rep = [];
				foreach($val as $k => $v)
				{
					$suf[] = '/([А-Я])([а-я]+)(' . $k . ')( |-|$)/u';
					$rep[] = '$1$2' . $v . '$4';
				}
				$word = preg_replace($suf, $rep, $word);
				if($pre == 'в' && preg_match('/^' . $pre . '[б|в|г|д|ж|з|й|к|л|м|н|п|р|с|т|ф|х|ц|ч|ш|щ]/iu', $word))
					$pre .= 'о';
				return $pre . ' ' . $word . ' ' . $suffix;
			}
			default:
				return $word . ' ' . $suffix;
		}
		return $word . ' ' . $suffix;
	}
	static public function GetLocalDate($date)
	{
		$d = new \DateTime($date);
		$t = Yii::$app->session->get('timezone');
		if(!$t)
			return $d;
		$time = $d->getTimestamp() - ($t + 3*60*60);
		$d->setTimestamp($time);
		return $d;
	}
	static public function GetDateName($date)
	{
		$d = static::GetLocalDate($date);
		$cur = static::GetLocalDate(date('Y-m-d H:i:s'));
		$tom = static::GetLocalDate(date('Y-m-d H:i:s', time() - 60*60*24));
		if($d->format('Y-m-d') == $cur->format('Y-m-d'))
			return 'сегодня, ' . $d->format('H:i');
		if($d->format('Y-m-d') == $tom->format('Y-m-d'))
			return 'вчера, ' . $d->format('H:i');
		$monthsX = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
		if($cur->format('Y') == $cur->format('Y'))
			return $d->format('d') . ' '. $monthsX[((int)$d->format('m')) - 1] . ' ' . $d->format('H:i');
		return $d->format('d') . ' ' . $monthsX[((int)$d->format('m')) - 1] . ' ' . $d->format('d') . 'г. ' . $d->format('H:i');
	}
	static public function SocialMeta($view, $name, $description, $img)
	{
		$imgUrl = 'http://smith24.ru' . $img;
		
		$view->registerMetaTag(['property' => 'og:title', 'content' => $name]);
		$view->registerMetaTag(['property' => 'og:description', 'content' => $description]);
		$view->registerMetaTag(['property' => 'og:image', 'content' => $imgUrl]);
		
		$view->registerMetaTag(['name' => 'twitter:title', 'content' => $name]);
		$view->registerMetaTag(['name' => 'twitter:description', 'content' => $description]);
		$view->registerMetaTag(['name' => 'twitter:image', 'content' => $imgUrl]);
	}
	private static function rus2translit($string)
	{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
	}
	public static function translit($str)
	{
    $str = self::rus2translit($str);
    $str = strtolower($str);
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    $str = trim($str, '-');
    return $str;
	}
	public static function GetSiteId()
	{
		if(Yii::$app->request->serverName == 'tyumen.smith24.ru')
			return 1;
		return 0;
	}
	function generateRandomString($length = 10)
	{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++)
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    return $randomString;
	}
}