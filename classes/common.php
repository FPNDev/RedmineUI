<?php
	class json {
		public static function OUT($json) {
			echo json_encode($json);
			exit;
		}

		public static function GET($identifier) {
			$path = $_SERVER['DOCUMENT_ROOT'].'/assets/json/'.$identifier.'.json';
			return file_exists($path) ? (json_decode(file_get_contents($path)) ? json_decode(file_get_contents($path), true) : false) : false;
		}

		public static function SET($identifier, $data) {
			if(!is_array($data) && !is_object($data) && !json_decode($data)) return false;
			if(is_array($data) || is_object($data)) $data = json_encode($data);
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/assets/json/'.$identifier.'.json', $data);
			return true;
		}
	}

	class cookie {
		public static function get() {
			return isset($_COOKIE['secure']) ? self::decode($_COOKIE['secure'], 'EfxtdchXPV') : false;
		}

		public static function set($i) {
			return setcookie('secure', self::encode($i, 'EfxtdchXPV'), time() + 86400 * 365, '/');
		}

		public static function encode($data, $soil) {
			if(is_array($data)) $data = json_encode($data);
			for($i = 0; $i < strlen($data); $i++) {
				$data[$i] = chr(ord($data[$i]) + ord($soil[$i % strlen($soil)]));
			}
			return $data;
		}

		public static function decode($data, $soil) {
			for($i = 0; $i < strlen($data); $i++) {
				$data[$i] = chr(ord($data[$i]) - ord($soil[$i % strlen($soil)]));
			}
			if($ret = json_decode($data, true)) return $ret;
				return $data;
		}
	}

	class time {
		public static function convertToArray($time) {
			$hours = (int) $time;
			$minutes = ($time - $hours) * 60;
			$seconds = ($minutes - ((int) $minutes)) * 60;
			return ['hours' => $hours, 'minutes' => (int) $minutes, 'seconds' => (int) $seconds];
		}

		public static function asTime($time) {
			if(!is_array($time)) $time = self::convertToArray($time);
			$ret = '';
			if((!$time['hours'] && !$time['minutes'] && !$time['seconds']) || $time['hours']) $ret = sprintf('%02d год.', $time['hours']);
			if($time['minutes']) $ret .= sprintf(' %02d хв.', $time['minutes']);
			if($time['seconds']) $ret .= sprintf(' %02d сек.', $time['seconds']);
			return $ret;
		}
	}

	class API {
		public static function require($vars) {
			global $_GET;
			foreach($vars as $var) {
				if(!isset($_GET[$var]) || strlen((string) $_GET[$var]) == 0) { json::OUT(['error' => ['error_msg' => 'Оу.. Кажется, один из обязательных параметров не был указан']]); exit; }
			}
		}
	}
?>