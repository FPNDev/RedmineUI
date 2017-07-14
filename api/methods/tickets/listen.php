<?php
	set_time_limit(60);
	for($i = 0; $i < 10; $i++) {
		$cookie = cookie::get();
		if(!$cookie && isset($_COOKIE['secure'])) return json::OUT(['error' => ['error_msg' => 'Упс.. Похоже, ваша сессия истекла', 'error_action' => 'logout']]);
		if(($tickets = tickets::get($cookie)) !== false) {
			$tickets = tickets::idAsKey($tickets);
			if($tickets) {
				$old = json::GET('tickets/'.md5($cookie['url'].':'.$cookie['username']));
				if(!$old) $old = [];
				$ret = [];
				foreach($old as $ticket) {
					if(!in_array($ticket, $tickets)) {
						$i = $ticket['id'];
						if(!isset($tickets[$i]))
							$ret[] = array_merge($ticket, ['action' => 'delete']);
						else {
							foreach($tickets[$i] as $k => $t) {
								if($t != $ticket[$k]) $ret[] = ['id' => $i, 'field' => $k, 'value' => $t, 'action' => 'edit'];
							}
						}
					}
				}
				foreach($tickets as $ticket) {
					if(!in_array($ticket, $old)) {
						$i = $ticket['id'];
						if(!isset($old[$i]))
							$ret[] = array_merge($ticket, ['action' => 'add']);
					}
				}
				if($ret) {
					json::SET('tickets/'.md5($cookie['url'].':'.$cookie['username']), $tickets);
					return json::OUT(['success' => true, 'changes' => $ret]);
				}
			}
			sleep(2);
		} else return json::OUT(['error' => ['error_msg' => 'Упс.. Похоже ваша сессия истекла', 'error_action' => 'logout']]);
	}
	return json::OUT(['success' => true, 'changes' => []]);
?>