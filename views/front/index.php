<?php $_PAGE['title'] = 'Вход'; ?>
<div class="middle-size align-center" sAnim="fadeInUp" sDuration="2" style="margin-top: 180px;">
	Для начала работы с системой Redmine введите свои данные.
</div>
<form class="login-form" sAnim="fadeInDown" sDuration="2" onsubmit="return log.in(this, event)">
	<input type="text" id="server-field" class="input background-lightgrey block" placeholder="URL редмайн системы, с http://">
	<input type="text" id="username-field" class="input background-lightgrey block" placeholder="Имя пользователя">
	<input type="password" id="password-field" class="input background-lightgrey block" placeholder="Пароль">
	<button class="input background-grey cursor-pointer iblock width25 fl-right" onclick="return log.in(this, event)">Войти</button>
	<input type="submit" style="display: none">
</form>