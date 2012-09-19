<?php

$menu_registry = array();

function menu_register($items) {
	foreach ($items as $url => $item) {
		$GLOBALS['menu_registry'][$url] = $item;
	}
}

function menu_execute_active_handler() {
	$query = (array) explode('/', $_GET['q']);
	$GLOBALS['page'] = $query[0];
	$page = $GLOBALS['menu_registry'][$GLOBALS['page']];
	if (!$page) {
		header('HTTP/1.0 404 Not Found');
		die('404 - Page not found.');
	}



//	if ($page['security'])
//	user_ensure_authenticated();

	if (function_exists('config_log_request'))
	config_log_request();

	if (function_exists($page['callback']))
	return call_user_func($page['callback'], $query);

	return false;
}

function menu_current_page() {
	return $GLOBALS['page'];
}

function menu_visible_items() {
	static $items;
	if (!isset($items)) {
		$items = array();
		foreach ($GLOBALS['menu_registry'] as $url => $page) {
			if ($page['security'] && !user_is_authenticated()) continue;
			if ($page['hidden']) continue;
			$items[$url] = $page;
		}
	}
	return $items;
}

function theme_menu_top() {
	return theme('menu_both', 'top');
}

function theme_menu_bottom() {
	return theme('menu_both', 'bottom');
}

function theme_menu_both($menu) {
	$links = array();

	$menu_item = 0;

	foreach (menu_visible_items() as $url => $page) 
	{
		$title = $url ? $url : 'home';

		if (!$url) $url = BASE_URL; // Shouldn't be required, due to <base> element but some browsers are stupid.
		

		if ($menu_item > 6)
		{
			$class = "prio-gamma";
		} else if ($menu_item > 4)
		{
			$class = "prio-beta";
		} else{
			$class = "prio-alpha";
		}

		$links[] = "<li class=\"$class\"><a href=\"$url\">$title</a></li>";

		$menu_item++;
	}

	if (user_is_authenticated()) {
		$user = user_current_username();
		array_unshift($links, "<li class=\"current prio-beta\"><a href=\"user/$user\">$user</a></li>");
	}

	//	Add the expand, contract button
	$links[] = '<li class="show-nav-more"><a href="'.pageURL().'#prio">+more</a></li>';
	$links[] = '<li class="show-nav-less"><a href="'.pageURL().'#">-less</a></li>';

	return "<nav id=\"prio\"><ul>".implode("\n", $links).'</ul></nav>';
}

function pageURL() 
{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	
	$pageURL .= "://";
	
	if ($_SERVER["SERVER_PORT"] != "80") 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	return $pageURL;
}

?>