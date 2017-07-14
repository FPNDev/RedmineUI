<?php
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/sounds/'.$_PAGE['file']))
		return ob_clean() && $_PAGE['no_template'] = true && handler::render('sound', ['file' => $_PAGE['file']]);
	else return handler::renderError();
?>