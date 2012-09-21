<?php
$dabr_start = microtime(1);

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
	),
	'logout' => array (
		'security' => true,
		'callback' => 'logout_page',
	),
));

//	Log out the user
function logout_page() {
	user_logout();
	header("Location: " . BASE_URL); /* Redirect browser */
	exit;
}

//	Show the about page
function get_about_page() {
	$about = //file_get_contents('about.html');
		'<div id="about" >
			<h3>What is Dabr for AppDotNet?</h3>';
	$about .= theme_get_logo();
	$about .= '<ul>
				<li>A mobile web interface for AppDotNet</li>
				<li>Created by <a href="https://alpha.app.net/edent">Terence Eden</a></li>
				<li>Based on <a href="http://code.google.com/p/dabr/">Dabr for Twitter</a> originally by 
					<a href="http://twitter.com/davidcarrington">@davidcarrington</a>, 
					<a href="http://shkspr.mobi/blog/index.php/tag/dabr/">Terence Eden</a>, and
					<a href="http://twitter.com/whatleydude">@whatleydude</a> 
				</li>
				<li><a href="https://github.com/edent/Dabr-For-AppDotNet">Open Source on GitHub</a></li>
			</ul>

			<p>If you have any comments, suggestions or questions then feel free to <a href="http://edent.tel/">get in touch</a>.</p>
        </div>';  

        return $about;
 }

 function about_page()
 {
  	theme('page', "About Dabr", get_about_page());
 }

function sign_in() 
{
	$app = new EZAppDotNet();
	$url = $app->getAuthUrl();
	$url = htmlspecialchars($url);
	$sign_in = "<a href=\"$url\"><h2>Sign in using App.net</h2></a>";

	$about = get_about_page();

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