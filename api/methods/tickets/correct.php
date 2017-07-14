<?php
	API::require(['id', 'question']);
	$id = (int) $_GET['id'];

	if(!$form = tickets::getUpdateParams($id)) {
		switch(tickets::$error) {
			case 'login': return json::OUT(['error' => ['error_msg' => 'Упс.. Похоже вы не залогинены', 'error_action' => 'logout']]);
			default: return json::OUT(['error' => ['error_msg' => 'Упс.. Такого тикета не существует']]);
		}
	}

	$form['issue[status_id]'] = 4;
	$form['notes'] = $_GET['question'];
	$session = '';

	foreach($_CURL['headers'] as $header) {
		if(preg_match('/^Set-Cookie:\s*_redmine_session=(.*?);/', $header, $m)) {
			$session = $m[1];
			break;
		}
	}

	if(!$session) return json::OUT(['error' => ['error_msg' => 'Во время вашего запроса произошла ошибка. Повторите попытку позже']]);

	if(tickets::uploadTicket($id, $form, $session)) return json::OUT(['success' => true, 'id' => $id]);
		else return json::OUT(['error' => ['error_msg' => 'Во время вашего запроса произошла ошибка. Повторите попытку позже']]);
?>