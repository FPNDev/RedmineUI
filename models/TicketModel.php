<?php
	$c = cookie::get();
	if(isset($c['autologin']) && $ticket = tickets::getInfo($_PAGE['ticket_id'])) {
		$_PAGE['title'] = htmlspecialchars_decode($ticket['title']);
		return handler::render('admin/ticket', ['ticket' => $ticket]);
	} else {
		switch (tickets::$error) {
			case 'login':
				handler::render('front/index');
				break;
			
			default:
				handler::renderError();
				break;
		}
	}
?>	