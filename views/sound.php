<?php
	header('Content-Type: audio/mpeg');
	readfile($_SERVER['DOCUMENT_ROOT'].'/assets/sounds/'.$file);
	exit;
?>