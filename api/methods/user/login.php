<?php
	API::require(['login', 'password', 'url']);
	$login = $_GET['login'];
	$password = $_GET['password'];
	$url = trim(explode('?', $_GET['url'])[0], '/');
	$request = Request::send('GET', $url.'/login');
	if(!$request) return json::OUT(['error' => ['error_msg' => 'Сервер указан неверно!']]);
	$auth_token = str_get_html($request)->find('[name="authenticity_token"]', 0);
	if(!$auth_token) return json::OUT(['error' => ['error_msg' => 'Не могу найти данные для произведения входа на странице '.htmlspecialchars($url).'/login']]);
	$auth_token = $auth_token->getAttribute('value');
	$ans = Request::send('POST', $url.'/login', [], ['authenticity_token' => $auth_token, 'utf8' => '✓', 'username' => $_GET['login'], 'password' => $_GET['password'], 'autologin' => 1]);
	$headers = json_encode($_CURL['headers']);
	if(strpos($headers, 'Set-Cookie: autologin=;')) return json::OUT(['error' => ['error_msg' => 'Логин или пароль введены неверно']]);
	$autologin = preg_replace('/.*autologin=(.*?);.*/', '$1', $headers);
	$ans = Request::send('GET', $url.'/', ['Cookie' => 'autologin='.urlencode($autologin)]);
	preg_match('/<div id="loggedas">.*?<a href=".*?">(.*?)<\/a><\/div>/', $ans, $username);
	$username = $username[1];
	return cookie::set(['autologin' => $autologin, 'url' => $url, 'username' => $username]) && json::OUT(['success' => true]);
?>