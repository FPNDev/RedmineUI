<?php
	API::require(['id']);
	$id = (int) $_GET['id'];
	$info = tickets::getInfo($id);
	if(!$info) {
		switch(tickets::$error) {
			case 'login': return json::OUT(['error' => ['error_msg' => 'Упс.. Похоже вы не залогинены', 'error_action' => 'logout']]);
			default: return json::OUT(['error' => ['error_msg' => 'Упс.. Такого тикета не существует']]);
		}
	}
	return json::OUT(['success' => true, 'ticket' => $info]);
?>