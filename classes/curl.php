<?php
	class Request {
		public static function send($type = 'GET', $url = '', $headers = [], $data = []) {
			$ch = curl_init();
			$data = http_build_query($data);
			$c = cookie::get();
			if(isset($c['autologin'])) $headers = array_merge(['Cookie' => 'autologin='.urlencode($c['autologin'])], $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, self::h2a($headers));
			switch($type) {
				case 'GET':
					curl_setopt($ch, CURLOPT_URL, $url . (strpos($url, '?') ? '&'.$data : '?'.$data));
					break;
				case 'PUT':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					break;
				case 'DELETE':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
				case 'POST':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLINFO_HEADER_OUT, true);
					break;

			}

			$response = curl_exec($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			global $_CURL;
			$_CURL['headers'] = explode("\r\n", substr($response, 0, $header_size));
			$_CURL['content'] = substr($response, $header_size);
			return $_CURL['content'];
		}

		private static function h2a($headers) {
			$result = [];
			foreach($headers as $k => $h) $result[] = $k.': '.$h;
			return $result;
		}
	}
?>