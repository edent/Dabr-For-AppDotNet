<?php
$dabr_start = microtime(1);

header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

require 'config.php';
require 'common/browser.php';
require 'common/menu.php';
require 'common/user.php';
require 'common/theme.php';
require 'common/dabr.php';
require 'common/settings.php';

require_once 'EZAppDotNet.php';

//	Set Up the default menu
menu_register(array (
	'about' => array (
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
function about_page() {
	$content .= //file_get_contents('about.html');
		'<div id="about" >
			<h3>What is Dabr for AppDotNet?</h3>
		<ul>
			<li>A mobile web interface for AppDotNet</li>
			<li>Created by <a href="https://alpha.app.net/edent">Terence Eden</a></li>
			<li><a href="https://github.com/edent/Dabr-For-AppDotNet">Open Source on GitHub</a></li>
			<li>Based on <a href="http://code.google.com/p/dabr/">Dabr for Twitter</a> originally by <a href="http://twitter.com/davidcarrington">@davidcarrington</a>, <a href="http://twitter.com/whatleydude">@whatleydude</a>, and <a href="http://shkspr.mobi/blog/index.php/tag/dabr/">Terence Eden</a></li>
		</ul>

		<p>If you have any comments, suggestions or questions then feel free to <a href="http://edent.tel/">get in touch</a>.</p>

        </div>';  

       return $content;
        //theme('page', 'About', $content); 
 }

function sign_in() 
{
	$app = new EZAppDotNet();
	$url = $app->getAuthUrl();
	$url = htmlspecialchars($url);
	$content = "<a href=\"$url\"><h2>Sign in using App.net</h2></a>";

	$content .= about_page();

	return $content;
}

menu_execute_active_handler();

$app = new EZAppDotNet();

// check that the user is signed in
if ($app->getSession()) {
/*
	// get the current user as JSON
	$data = $app->getUser();

	// accessing the user's cover image
	echo '<body style="background:url('.$data['cover_image']['url'].')">';
	echo '<div style="background:#fff;opacity:0.8;padding:20px;margin:10px;border-radius:15px;">';
	echo '<h1>Welcome to <a target="_blank" href="https://github.com/jdolitsky/AppDotNetPHP">';
	echo 'AppDotNetPHP</a></h1>';

	// accessing the user's name
	echo '<h3>'.$data['name'].'</h3>';
	
	// accessing the user's avatar image
	echo '<img style="border:2px solid #000;" src="'.$data['avatar_image']['url'].'" /><br>';
	
	echo '<a href="signout.php"><h2>Sign out</h2></a>';

	echo '<pre style="font-weight:bold;font-size:16px">';
	print_r($data);
	echo '</pre>';
	echo '</div></body>';
*/
// otherwise prompt to sign in
} else {

	$url = $app->getAuthUrl();
	//$content = $app->getSession();
	$content = "<a href=\"$url\"><h2>Sign in using App.net</h2></a>".
				"Remember me <input type=\"checkbox\" id=\"rem\" value=\"1\" checked/>".
				"<script>
					document.getElementById('rem').onclick = function(e){
						if (document.getElementById('rem').value=='1') {
							window.location='?rem=2';
						} else {
							window.location='?rem=1';
						};
					}
				</script>";
	about_page();

}