<?php
	if(!isset($_POST['ajax'])) return handler::renderError();
	return handler::render('modal/'.$_PAGE['modal'], []);
?>