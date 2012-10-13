<?php
require_once 'AppDotNet.php';

function user_ensure_authenticated() {
	$app = new EZAppDotNet();

	// check that the user is signed in
	if (!$app->getSession()) {
		//if (!user_is_authenticated()) {
		$content = theme('login');
	
		$content .= about_page();
		theme('page', 'Login', $content);
	}
}

function user_logout() 
{
	$app = new EZAppDotNet();
	// log out user
	$app->deleteSession();
	// redirect user after logging out
	header('Location: index.php');
}

function user_is_authenticated() {
	$app = new EZAppDotNet();
	if ($app->getSession()) {
		return true;
	}

	return false;
}

function user_current_username() 
{
	if ($_SESSION['app_username'])
	{
		return $_SESSION['app_username'];
	}

	else
	{
		$app = new EZAppDotNet();

		// check that the user is signed in
		if ($app->getSession())
		{
			try
			{	
				$user = $app->getUser();	
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}
			
			$username = $user['username'];
			
			$_SESSION['app_username'] = $username;
			
			return $username;
		}
	}
}

function user_is_current_user($username) {
	return (strcasecmp($username, user_current_username()) == 0);
}

function user_type() {
	return $GLOBALS['user']['type'];
}

function _user_save_cookie($stay_logged_in = 0) {
	$cookie = _user_encrypt_cookie();
	$duration = 0;
	if ($stay_logged_in) {
		$duration = time() + (3600 * 24 * 365);
	}
	setcookie('USER_AUTH', $cookie, $duration, '/');
}

function _user_encryption_key() {
	return ENCRYPTION_KEY;
}

function _user_encrypt_cookie() {
	$plain_text = $GLOBALS['user']['username'] . ':' . $GLOBALS['user']['password'] . ':' . $GLOBALS['user']['type'];

	$td = mcrypt_module_open('blowfish', '', 'cfb', '');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, _user_encryption_key(), $iv);
	$crypt_text = mcrypt_generic($td, $plain_text);
	mcrypt_generic_deinit($td);
	return base64_encode($iv.$crypt_text);
}

function _user_decrypt_cookie($crypt_text) {
	$crypt_text = base64_decode($crypt_text);
	$td = mcrypt_module_open('blowfish', '', 'cfb', '');
	$ivsize = mcrypt_enc_get_iv_size($td);
	$iv = substr($crypt_text, 0, $ivsize);
	$crypt_text = substr($crypt_text, $ivsize);
	mcrypt_generic_init($td, _user_encryption_key(), $iv);
	$plain_text = mdecrypt_generic($td, $crypt_text);
	mcrypt_generic_deinit($td);

	list($GLOBALS['user']['username'], $GLOBALS['user']['password'], $GLOBALS['user']['type']) = explode(':', $plain_text);
}

function theme_login() {
	$app = new EZAppDotNet();		
	$url = $app->getAuthUrl();
	
	$content = '<div style="margin:1em; font-size: 1.2em">
				<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';	
	$content .='</div>';
	return $content;
}

function theme_logged_out() {
	return '<p>Logged out. <a href="">Login again</a></p>';
}
