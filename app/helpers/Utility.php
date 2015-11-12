<?php

namespace helpers;

use Core;

class Utility {

	public $name = "Utility";

	function __construct() {

	}
	private function _date_range_limit($start, $end, $adj, $a, $b, &$result)
	{
		if ($result[$a] < $start) {
			$result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
			$result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
		}

		if ($result[$a] >= $end) {
			$result[$b] += intval($result[$a] / $adj);
			$result[$a] -= $adj * intval($result[$a] / $adj);
		}

		return $result;
	}

	private function _date_range_limit_days(&$base, &$result)
	{
		$days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		$this->_date_range_limit(1, 13, 12, "m", "y", $base);

		$year = $base["y"];
		$month = $base["m"];

		if (!$result["invert"]) {
			while ($result["d"] < 0) {
				$month--;
				if ($month < 1) {
					$month += 12;
					$year--;
				}

				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

				$result["d"] += $days;
				$result["m"]--;
			}
		} else {
			while ($result["d"] < 0) {
				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

				$result["d"] += $days;
				$result["m"]--;

				$month++;
				if ($month > 12) {
					$month -= 12;
					$year++;
				}
			}
		}

		return $result;
	}

	private function _date_normalize($base, $result)
	{
		$result = $this->_date_range_limit(0, 60, 60, "s", "i", $result);
		$result = $this->_date_range_limit(0, 60, 60, "i", "h", $result);
		$result = $this->_date_range_limit(0, 24, 24, "h", "d", $result);
		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

		$result = $this->_date_range_limit_days($base, $result);

		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

		return $result;
	}


	public function date_diff($one, $two)
	{
		$invert = false;
		if ($one > $two) {
			list($one, $two) = array($two, $one);
			$invert = true;
		}

		$key = array("y", "m", "d", "h", "i", "s");
		$a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
		$b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

		$result = array();
		$result["y"] = $b["y"] - $a["y"];
		$result["m"] = $b["m"] - $a["m"];
		$result["d"] = $b["d"] - $a["d"];
		$result["h"] = $b["h"] - $a["h"];
		$result["i"] = $b["i"] - $a["i"];
		$result["s"] = $b["s"] - $a["s"];
		$result["invert"] = $invert ? 1 : 0;
		$result["days"] = intval(abs(($one - $two)/86400));

		if ($invert) {
			$this->_date_normalize($a, $result);
		} else {
			$this->_date_normalize($b, $result);
		}

		return $result;
	}

	public function dateReadable($time) {
		$date = date("j F, G:i", $time);
		return $date;
	}

	public function replaceMentionHashtag($text) {
		$cmv = preg_replace("/@(\w+)/", '<a href="' . BASE_URL . '/user/$1">@$1</a>', $text);
		$cmv = preg_replace("/\#(\w+)/", '<a href="' . BASE_URL . '/hashtag/$1">#$1</a>', $cmv);
		return $cmv;
	}

	public function getMentions($text) {
		$arr = array();
		preg_match_all("/@(\w+)/", $text, $arr);
		return $arr[1];
	}
	public function getHashTags($text) {
		$arr = array();
		preg_match_all("/\#(\w+)/", $text, $arr);
		return $arr[1];
	}

	public function makeUniqueId($length = 13) {
		$str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_";

		$isLong = false;
		if ($length >= 13) {
			$length = $length - 13;
			$isLong = true;
		}

		$res = "";
		for ($i = 0; $i < $length; $i++) {
			$res .= $str[rand(0, strlen($str)-1)];
		}

		if ($isLong)
			return $res . uniqid();
		return $res;
	}

	public function cropImage($destination, $size) {
		$resizeObj = new \resize($destination);

		$resizeObj -> resizeImage($size, $size, 'crop');

		// $ext = strtolower(strrchr($destination, '.'));
		// $newDestination = substr($destination, 0, strrpos($destination, ".")) . "_" . $size . $ext;

		$resizeObj -> saveImage($destination, 80);

		if (file_exists($destination))
			return true;
		return false;
	}
	public function resizeImage($destination, $sizeWidth) {
		$resizeObj = new \resize($destination);

		if ($resizeObj->extension != ".gif") {
			if ($resizeObj->height > $resizeObj->width) {
				$resizeObj -> resizeImage($sizeWidth, $sizeWidth, 'crop');
			} else {
				$resizeObj -> resizeImage($sizeWidth, $sizeWidth, 'landscape');
			}
		}

		$resizeObj -> saveImage($destination, 80);

		if (file_exists($destination))
			return true;
		return false;
	}
	public function intervalTime($date) {
		$interval  = (time() - $date);

		/*
		echo date("Y-m-d H:i:s", $date) . "   ->   " . date("Y-m-d H:i:s", time()) . "\r\n";
		echo $date . "   ->   " . time() . " = " . $interval . "\r\n";
		*/

		$res = 0;
		$res = floor($interval / 1728000);
		if ($res > 0) return date("j M", $date);

		$res = floor($interval / 604800);
		if ($res > 0) return $res . "w";

		$interval = $interval % 604800;
		$res	= floor($interval / 86400);
		if ($res > 0) return $res . "d";

		$interval = $interval % 86400;
		$res	= floor($interval / 3600);
		if ($res > 0) return $res . "h";

		$interval = $interval % 3600;
		$res	= floor($interval / 60);
		if ($res > 0) return $res . "m";

		$interval = $interval % 60;
		$res	= floor($interval);
		if ($res > 0) return $res . "s";

	}

	public function slugify($text)
	{ 
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
		$text = trim($text, '-');
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = strtolower($text);
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
		{
			return 'course';
		}

		return $text;
	}

}

?>