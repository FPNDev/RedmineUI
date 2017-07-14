<?php
	$cookie = cookie::get();
	if(!isset($cookie['autologin'])) {
		return handler::render('front/index');
	} else {
		if($tickets = tickets::get($cookie)) {
			json::SET('tickets/'.md5($cookie['url'].':'.$cookie['username']), tickets::idAsKey($tickets));
			$categories = ['project' => ['Не выбрано'], 'status' => ['Не выбрано'], 'type' => ['Не выбрано'], 'priority' => ['Не выбрано']];
			foreach($tickets as $k => $r) {
				if(!in_array($r['project'], $categories['project'])) $categories['project'][] = $r['project'];
				if(!in_array($r['status'], $categories['status'])) $categories['status'][] = $r['status'];
				if(!in_array($r['type'], $categories['type'])) $categories['type'][] = $r['type'];
				if(!in_array($r['priority'], $categories['priority'])) $categories['priority'][] = $r['priority'];
			}
			return handler::render('admin/index', ['tickets' => $tickets, 'categories' => $categories]);
		} else {
			setcookie('secure', '', -1, '/');
			return handler::render('front/index');
		}
	}
?>