<?php
	class handler {
		public static function path() {
			$page = trim(explode('?', explode('#', $_SERVER['REQUEST_URI'])[0])[0], '/');
			$ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/configs/nav.conf', true);
			if(isset($ini[$page]) && (!isset($ini[$page]['regex']) || !$ini[$page]['regex'])) {
				global $_PAGE, $_CURL;
				if(!isset($ini[$page]['params'])) $ini[$page]['params'] = [];
				if(isset($ini[$page]['params']['no_template']) || isset($_POST['ajax'])) ob_clean();
				$_PAGE = array_merge($ini[$page]['params'], $_PAGE);
				return include_once($_SERVER['DOCUMENT_ROOT'].'/models/'.$ini[$page]['model'].'.php');
			} else {
				foreach($ini as $k => $val) {
					if(!isset($val['regex']) || !$val['regex']) continue;
					$k = str_replace('/', '\/', $k);
					if(preg_match('/^'.$k.'$/', $page)) {
						global $_PAGE, $_POST, $_CURL;
						foreach ($val['params'] as $pk => $pval) $val['params'][$pk] = preg_replace('/^'.$k.'$/', $pval, $page);
						if(isset($val['params']['no_template']) || isset($_POST['ajax'])) ob_clean();
						$_PAGE = array_merge($val['params'], $_PAGE);
						return include_once($_SERVER['DOCUMENT_ROOT'].'/models/'.$val['model'].'.php');
					}
				}
			}

			return self::renderError();
		}
		
		public static function getParams() {
			$page = trim(explode('?', explode('#', $_SERVER['REQUEST_URI'])[0])[0], '/');
			$ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/configs/nav.conf', true);
			if(isset($ini[$page]) && (!isset($ini[$page]['regex']) || !$ini[$page]['regex'])) {
				global $_PAGE;
				if(!isset($ini[$page]['params'])) $ini[$page]['params'] = [];
				return $ini[$page]['params'];
			} else {
				foreach($ini as $k => $val) {
					if(!isset($val['regex']) || !$val['regex']) continue;
					$k = str_replace('/', '\/', $k);
					if(preg_match('/^'.$k.'$/', $page)) {
						foreach ($val['params'] as $pk => $pval) $val['params'][$pk] = preg_replace('/^'.$k.'$/', $pval, $page);
						return $val['params'];
					}
				}
			}
			return [];
		}

		public static function render($view, $params = []) {
			global $_PAGE, $_CURL;
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/views/'.$view.'.php')) {
				foreach($params as $k => $param) {
					$$k = $param;
				}
				include_once($_SERVER['DOCUMENT_ROOT'].'/views/'.$view.'.php');
				if(isset($_PAGE['title'])) echo '<title>'.htmlspecialchars($_PAGE['title']).'</title>';
				return true;
			}
			else return self::renderError();
		}

		public static function renderError() {
			return handler::render('error');
		}
	}
?>