<?php
	class HTML {
		public static $no_closing = [
			'img',
			'br',
			'meta',
			'link',
			'input'
		];
		public static function parse($str) {
			$str = preg_replace('/<!--.*?-->/', '', $str);
			preg_match_all('/<(\w+)[ \t]*.*?\/?>/', $str, $t_matches);
			$tags = [];
			$html = $str;
			$c = count($t_matches[1]);
			for ($i = $c - 1; $i >= 0; $i--) { 
				$t_pos = strrpos($str, $t_matches[0][$i]);
				$t_end = in_array($t_matches[1][$i], self::$no_closing) ? $t_pos + strlen($t_matches[0][$i]) - strlen('</'.$t_matches[1][$i].'>') : strpos($str, '</'.$t_matches[1][$i].'>', $t_pos);
				$tag = substr($str, $t_pos, $t_end + strlen('</'.$t_matches[1][$i].'>') - $t_pos);
				$str = substr_replace($str, '{{::parser_'.$i.'::}}', $t_pos, strlen($tag));
				$tags[] = $tag;
			}

			$tags = array_reverse($tags);

			$t = [];

			self::findChilds($str, $str, $tags, $t);

			$t = self::array_map(function($a) {
				if(is_string($a)) {
					preg_match('/<(\w+)([ \t]*.*?)>/', $a, $m);
					$attributes = [];
					while(preg_match('/^[ \t]+(\w+)([ \t]*=[ \t]*)?/', $m[2], $match)) {
						$m[2] = preg_replace('/^[ \t]+\w+([ \t]*=[ \t]*)?/', '', $m[2]);
						$quote = substr($m[2], 0, 1);
						if($quote != "'" && $quote != '"') $quote = '';
						preg_match('/'.$quote.'(.*?)'.($quote ? $quote : '[ \/>]').'/', $m[2], $match_value);
						$attribute = [$match[1] => $match_value[1]];
						$attributes[] = $attribute;
						$m[2] = substr($m[2], strlen($match_value[0]));
					}
					$a = preg_replace('/{{::parser_[0-9]+::}}/', '', $a);
					$a = ['tag' => $m[1], 'attributes' => $attributes, 'innertext' => substr($a, strlen($m[0]), strlen($a) - (in_array($m[1], self::$no_closing) ? 0 : strlen('</'.$m[1].'>')) - strlen($m[0]))];
				}
				return $a;
			}, $t);

			return new DOM($t);
		}

		private function findChilds($str, $s, $tags, &$curData) {
			if(preg_match('/{{::parser_([0-9]+)::}}/', $s, $m)) {
				$s = str_replace('{{::parser_'.$m[1].'::}}', '', $s);
				$str = str_replace('{{::parser_'.$m[1].'::}}', $tags[(int) $m[1]], $str);
				$curData[] = ['value' => $tags[(int) $m[1]], 'children' => []];
				if(preg_match('/{{::parser_([0-9]+)::}}/', $s)) return self::findChilds($str, $s, $tags, $curData);
				else if(preg_match('/{{::parser_([0-9]+)::}}/', $str, $m2)) {
					foreach ($curData as $key => $value) {
						if(strpos($value['value'], $m2[0])) {
							return self::findChilds($str, $str, $tags, $curData[$key]['children']);
						}
					}
				} return true;
			} else return true;
		}

		private function array_map($callback, $array)
		{
			$func = function ($item) use (&$func, &$callback) {
				return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
			};

			return array_map($func, $array);
		}
	}

	class DOM {

		private $structure;

		function __construct(array $structure) {
			$this->structure = $structure;
		}
	}
?>