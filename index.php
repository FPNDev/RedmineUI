<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);

	$_PAGE = [];
	$_CURL = [];

	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/curl.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/handler.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/domparser.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/html.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/redmine.php');
	if(!handler::getParams()['no_template'] && !$_POST['ajax'])
		return require_once($_SERVER['DOCUMENT_ROOT'].'/templates/system.php');
	
	return handler::path();
?>