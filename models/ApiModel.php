<?php
	header('Content-Type: application/json');
	return handler::render('../api/methods/'.$_PAGE['group'].'/'.$_PAGE['method'], []);
?>