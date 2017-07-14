<?php
	API::require(['id', 'progress']);
	$id = (int) $_GET['id'];
	$progress = intval($_GET['progress']);
	if($progress % 10 != 0 || $progress < 0 || $progress > 100) return json::OUT(['error' => ['error_msg' => 'Поле прогресса указано не верно']]);
	$time = intval($_GET['time']) ?? 0;
	if(!$form = tickets::getUpdateParams($id)) {
		switch(tickets::$error) {
			case 'login': return json::OUT(['error' => ['error_msg' => 'Упс.. Похоже вы не залогинены', 'error_action' => 'logout']]);
			default: return json::OUT(['error' => ['error_msg' => 'Упс.. Такого тикета не существует']]);
		}
	}

	$form['time_entry[hours]'] = $time / 60;
	$form['issue[done_ratio]'] = $progress;
	$form['issue[status_id]'] = $progress < 100 ? 2 : 3;
	$form['time_entry[activity_id]'] = 9;
	$session = '';

	foreach($_CURL['headers'] as $header) {
		if(preg_match('/^Set-Cookie:\s*_redmine_session=(.*?);/', $header, $m)) {
			$session = $m[1];
		}
	}

	if(!$session) return json::OUT(['error' => ['error_msg' => 'Во время вашего запроса произошла ошибка. Повторите попытку позже']]);

	if(tickets::uploadTicket($id, $form, $session)) return json::OUT(['success' => true, 'id' => $id]);
		else return json::OUT(['error' => ['error_msg' => 'Во время вашего запроса произошла ошибка. Повторите попытку позже']]);
?>