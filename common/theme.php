<?php
require_once ("common/advert.php");

$current_theme = false;

function theme() 
{
	global $current_theme;
	$args = func_get_args();
	$function = array_shift($args);
	$function = 'theme_'.$function;

	if ($current_theme) {
		$custom_function = $current_theme.'_'.$function;
		if (function_exists($custom_function))
		$function = $custom_function;
	} else {
		if (!function_exists($function))
		return "<p>Error: theme function <b>$function</b> not found.</p>";
	}
	return call_user_func_array($function, $args);
}

function theme_csv($headers, $rows) {
	$out = implode(',', $headers)."\n";
	foreach ($rows as $row) {
		$out .= implode(',', $row)."\n";
	}
	return $out;
}

function theme_list($items, $attributes) {
	if (!is_array($items) || count($items) == 0) {
		return '';
	}
	$output = '<ul'.theme_attributes($attributes).'>';
	foreach ($items as $item) {
		$output .= "<li>$item</li>\n";
	}
	$output .= "</ul>\n";
	return $output;
}

function theme_options($options, $selected = NULL) {
	if (count($options) == 0) return '';
	$output = '';
	foreach($options as $value => $name) {
		if (is_array($name)) {
			$output .= '<optgroup label="'.$value.'">';
			$output .= theme('options', $name, $selected);
			$output .= '</optgroup>';
		} else {
			$output .= '<option value="'.$value.'"'.($selected == $value ? ' selected="selected"' : '').'>'.$name."</option>\n";
		}
	}
	return $output;
}


function theme_radio($options, $name, $selected = NULL) 
{
	if (count($options) == 0) return '';
	$output = '';

	foreach($options as $value => $description) 
	{
		if ($name == "fonts") 
		{
			$style = "style='font-family:  ". urldecode($value) . ", sans;'";
		}

		$output .= '<label for="'.$value.'" '.$style.'>
						<input 
							type="radio" 
							name="'.$name.'"
							id="'.$value.'" 
							value="'.$value.'" '.($selected == $value ? 'checked="checked"' : '').' />'; 
		$output .= ' ' . $description . '</label>
		<br>';
	}
	return $output;
}

function theme_info($info) {
	$rows = array();
	foreach ($info as $name => $value) {
		$rows[] = array($name, $value);
	}
	return theme('table', array(), $rows);
}

function theme_table($headers, $rows, $attributes = NULL) {
	$out = '<div'.theme_attributes($attributes).'>';
	if (count($headers) > 0) {
		$out .= '<thead><tr>';
		foreach ($headers as $cell) {
			$out .= theme_table_cell($cell, TRUE);
		}
		$out .= '</tr></thead>';
	}
	if (count($rows) > 0) {
		$out .= theme('table_rows', $rows);
	}
	$out .= '</div>';
	return $out;
}

function theme_table_rows($rows) {
	$i = 0;
	foreach ($rows as $row) {
		if ($row['data']) {
			$cells = $row['data'];
			unset($row['data']);
			$attributes = $row;
		} else {
			$cells = $row;
			$attributes = FALSE;
		}
		$attributes['class'] .= ($attributes['class'] ? ' ' : '') . ($i++ %2 ? 'even' : 'odd');
		$out .= '<div'.theme_attributes($attributes).'>';
		foreach ($cells as $cell) {
			$out .= theme_table_cell($cell);
		}
		$out .= "</div>\n";
	}
	return $out;
}

function theme_attributes($attributes) {
	if (!$attributes) return;
	foreach ($attributes as $name => $value) {
		$out .= " $name=\"$value\"";
	}
	return $out;
}

function theme_table_cell($contents, $header = FALSE) {
	$celltype = $header ? 'th' : 'td';
	if (is_array($contents)) {
		$value = $contents['data'];
		unset($contents['data']);
		$attributes = $contents;
	} else {
		$value = $contents;
		$attributes = false;
	}
	return "<span".theme_attributes($attributes).">$value</span>";
}


function theme_error($message) 
{
	$message = str_replace("Bad Request: ", "", $message);
	$errorMessage = "<h3>" . theme_get_logo(57) . _(ERROR_MESSAGE) . " " . $message . "</h3>";

	//	Authentication error. User has either revoked access or changed password.
	if (strpos($message, "authentication") !== false)
	{
		$errorMessage .= "It looks like you changed your password. Please <a href=\"logout\">logout</a> and then reauthenticate.";
	}

	theme_page('Error', $errorMessage);
}


function theme_header($title)
{
	switch ($title) {
		case 'muted':
			$header  .= "<h3>" . theme_get_logo(57) . "This is everyone you have muted</h3>";
			break;
		case 'friends':
			$header  .= "<h3>" . theme_get_logo(57) . "Wow! You follow some really interesting folk!</h3>";
			break;
		case 'followers':
			$header  .= "<h3>" . theme_get_logo(57) . "These lovely people hang on your every word</h3>";
			break;
		case 'Search':
			$header  .= "<h3>" . theme_get_logo(57) . "Let's go exploring!</h3>";
			break;
		case 'Starrers':
			$header  .= "<h3>" . theme_get_logo(57) . "People who starred that post</h3>";
			break;
		case 'Reposters':
			$header  .= "<h3>" . theme_get_logo(57) . "They came, they saw, they reposted</h3>";
			break;
		default :
			$header ="";
	}

	if (strpos($title, "Users matching") !== false)
	{
		$header  .= "<h3>" . theme_get_logo(57) . "Who have we found?</h3>";
	}

	if (strpos($title, "Posts Starred") !== false)
	{
		$header  .= "<h3>" . theme_get_logo(57) . "OMG! It's full of stars!</h3>";
	}

	return $header;
		
}

function theme_page($title, $content) {
	$body = theme('menu_top');
	$body .= theme_header($title)  . "<section>" . $content . "</section>";
	$body .= theme('google_analytics');
	if (DEBUG_MODE == 'ON') 
	{
		global $dabr_start, $api_time, $services_time, $rate_limit;
		$time = microtime(1) - $dabr_start;
		$body .= '<p>
					Processed in '.
					round($time, 4).' seconds ('.
					round(($time - $api_time - $services_time) / $time * 100).'% Dabr, '.
					round($api_time / $time * 100).'% app.net API, '.
					round($services_time / $time * 100).'% other services). '.
					$rate_limit.
				'.</p>';
	}
	if ($title == 'Login') 
	{
		$title = 'Dabr - mobile app.net Login';
		$meta = '<meta name="description" content="Free open source alternative to mobile App.net, bringing you the complete AppDotNet experience to your phone." />';
	}
	ob_start('ob_gzhandler');
	header('Content-Type: text/html; charset=utf-8');
	$html =	'<!DOCTYPE html>
				<html>
					<head>
						<meta charset="utf-8" />
						<meta name="viewport" content="width=device-width, initial-scale=1.0" />
						<title>Dabr - ' . $title . '</title>
						<base href="'.BASE_URL.'" />
						<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
						<link rel="apple-touch-icon" href="images/dabr-57.png" />
						<link rel="apple-touch-icon" sizes="72x72" href="images/dabr-72.png" />
						<link rel="apple-touch-icon" sizes="114x114" href="images/dabr-114.png" />';
	if ($title == "Settings")
	{
		$html .=		'<link href="http://fonts.googleapis.com/css?family=Schoolbell|Ubuntu+Mono|Droid+Sans|Lora|Open+Sans:300" rel="stylesheet" type="text/css">';
	}

	$html .=			'<link href="http://fonts.googleapis.com/css?family='.(setting_fetch("fonts","Open Sans")).'" rel="stylesheet" type="text/css">
						'.$meta.theme('css').'
					</head>
					<body>';
	//echo 				"<div id=\"advert\">" . show_advert() . "</div>";
	$html .= 			$body;
	if (setting_fetch('colours') == null)
	{
		//	If the cookies haven't been set, remind the user that they can set how Dabr looks
		$html .=		'<p>Think Dabr looks ugly? <a href="settings">Change the colours!</a></p>';
	}
	$html .=		'</body>
				</html>';
	echo $html;
	exit();
}

function theme_colours() {
	$info = $GLOBALS['colour_schemes'][setting_fetch('colours', 0)];
	list($name, $bits) = explode('|', $info);
	$colours = explode(',', $bits);
	return (object) array(
		'links'		=> trim($colours[0]),
		'bodybg'	=> trim($colours[1]),
		'bodyt'		=> trim($colours[2]),
		'small'		=> trim($colours[3]),
		'odd'		=> trim($colours[4]),
		'even'		=> trim($colours[5]),
		'replyodd'	=> trim($colours[6]),
		'replyeven'	=> trim($colours[7]),
		'menubg'	=> trim($colours[8]),
		'menut'		=> trim($colours[9]),
		'menua'		=> trim($colours[10]),
	);
}

function theme_css() {
	$c = theme('colours');
	$font_size = setting_fetch('font_size', '1');

	return "
	<style type='text/css'>
		body
		{	
			background:#{$c->bodybg};
			color:#{$c->bodyt};
			font-size:" . ($font_size * 100) ."%;
			font-family: '". urldecode(setting_fetch("fonts","Open+Sans")) . "', sans;
			margin-left:0px;
			margin-top:0px;
		}
		
		nav ul {margin-top: 0px}

		input, select, textarea {
			font-size:" . ($font_size * 100) ."%;
			font-family: '". urldecode(setting_fetch("fonts","Open+Sans")) . "', sans;
		}

		a{color:#{$c->links}}
		small,small a{
			color:#{$c->small};
		}

		section {
			clear: both; }

		.odd{background:#{$c->odd}}
		.even{background:#{$c->even}}
		.reply{background:#{$c->replyodd}}
		.reply.even{background: #{$c->replyeven}}
		.tweet
		{
			padding:.5em;
			min-height:".(setting_fetch('avatar_size',48) + 2)."px;
		}
		
		.features
		{
			padding:.5em;
			min-height:25px;
		}

		.date
		{
			padding:.5em;
			font-weight:bold;
			color:#{$c->small}
		}
		.about,.time
		{
			font-size:80%;
			color:#{$c->small}
		}
		.avatar
		{
			height:auto; 
			width:auto;
			float:left;
			margin-right: 10px; 
		}
		.embed
		{
			left:0px; 
			display:block;
			overflow-x:auto;
			clear:both;
		}
		.embeded
		{
			left:0px; 
		}
		.status{
			word-wrap:break-word;
		}
		nav ul {
			padding: 0; 
			overflow: auto;
		}

		nav li {
			list-style: none;
			position: relative;
			display: block;
			float: left;
			margin: 0px 0 0 0;
			background:#{$c->menubg};
		}

		nav a {
			display: block; 
			padding: 0.5em; 
			text-decoration: none; 
			border-right: 1px solid #fff; 
			font-size:" . ($font_size * 105) ."%; 
			color:#{$c->menua};
		}

		nav .current {
			font-weight: bold; 
		}

		.prio-beta,
		.prio-gamma,
		.show-nav-less {
			display: none; 
		}

		#prio:target .prio-beta,
		#prio:target .prio-gamma,
		#prio:target .show-nav-less {
			display: block; 
		}

		#prio:target .show-nav-more {
			display: none; 
		}

		@media all and (min-width: 31em) {
		.prio-beta {
			display: block; } 
		}

		@media all and (min-width: 70em) {
			.prio-gamma {
				display: block; }

			.show-nav-more,
			#prio:target .show-nav-less {
				display: none; } 
		}
		
		.logo{float:left;margin-right:15px;}
		
		.bottom{
			text-align:center;
		}
		.button{
			font: bold;
			text-decoration: none;
			background-color: #EEEEEE;
			color: #333333;
			padding: 2px 6px 2px 6px;
		}
	</style>";
}

function theme_google_analytics() {
	global $GA_ACCOUNT;
	if (!$GA_ACCOUNT) return '';
	$googleAnalyticsImageUrl = googleAnalyticsGetImageUrl();
	return "<img src='{$googleAnalyticsImageUrl}' />";
}

function theme_get_logo($size = 128)
{
	if ((setting_fetch('avatar_show', 'on') == 'off'))
	{
		return "";
	}
	return '<img src="images/dabr-'.$size.'.png" height="'.$size.'" width="'.$size.'" alt="The Dabr Bunny" class="logo" />';
}

//	Show the about page
function theme_about_page() {
	$about = 
		'<div id="about" >'.
			'<h3>'._(WHAT_IS).'</h3>';
	$about .= theme_get_logo();
	$about .= 	'<ul>
					<li>'._(ABOUT_1).'</li>
					<li>'._(CREATED_BY).'</li>
				</ul>
				<h2>'._(ABOUT_FEATURES).'</h2>
				<ul>
					<li>'._(ABOUT_2).'</li>
					<li>'._(ABOUT_3).'</li>
					<li>'._(ABOUT_4).'</li>
					<li>'._(ABOUT_5).'</li>
					<li>'._(ABOUT_6).'</li>
					<li>'._(ABOUT_7).'</li>
					<li>'._(ABOUT_8).'</li>
					<li>'._(ABOUT_9).'</li>
					<li>'._(ABOUT_10).'</li>
					<li>'._(ABOUT_11).'</li>
					<li>'._(ABOUT_12).'</li>
					<li>'._(ABOUT_13).'</li>
				</ul>
				<h2>'._(ABOUT_CREDITS).'</h2>
				<ul>
					<li>'._(ABOUT_CREDITS_1).'</a> 
					</li>
					<li>'._(GITHUB_LINK).'</li>
				</ul>
				<p>'._(ABOUT_COMMENTS).'</p>
		</div>'; 

	return $about;
}
