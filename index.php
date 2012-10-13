<?php
$dabr_start = microtime(1);

// I18N support information here
$language = "en_GB";
putenv("LANG=" . $language); 
setlocale(LC_ALL, $language);

// Set the text domain as "messages"
$domain = "messages";
bindtextdomain($domain, "Locale"); 
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

require 'config.php';
require 'common/menu.php';
require 'common/user.php';
require 'common/theme.php';
require 'common/dabr.php';
require 'common/settings.php';

require_once 'EZAppDotNet.php';

//	Set Up the default menu
menu_register(array (
	'about' => array (
		'security' => true,
		'callback' => 'about_page',
		'display'  => _(MENU_ABOUT)
	),
	'logout' => array (
		'security' => true,
		'callback' => 'logout_page',
		'display'  => _(MENU_LOGOUT)
	),
));

//	Log out the user
function logout_page() {
	user_logout();
	header("Location: " . BASE_URL); /* Redirect browser */
	exit;
}


function about_page()
{
	theme('page', _(ABOUT_TITLE), theme_about_page());
}

function sign_in() 
{
	$app = new EZAppDotNet();
	$url = $app->getAuthUrl();
	$url = htmlspecialchars($url);
	$sign_in = "<a href=\"$url\">
					<img src=\"images/ConnectButton_240x50.png\" width=\"250\" height=\"50\" alt=\"Sign in button\" />
					<h2>Sign in using App.net</h2>
				</a>";

	$about = theme_about_page();

	return $sign_in . $about;
}

menu_execute_active_handler();

$app = new EZAppDotNet();

// check that the user is signed in
if ($app->getSession()) 
{
// otherwise prompt to sign in
} else {
  	theme('page', "Sign In To Dabr", sign_in());
}