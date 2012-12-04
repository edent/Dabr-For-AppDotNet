<?php
require_once 'Autolink.php';
require_once 'Extractor.php';
require_once 'Embedly.php';
require_once 'Emoticons.php';
require_once 'menu.php';
		
menu_register(array(
	'' => array(
		'callback' => 'dabr_home_page',
		'display'  => _(MENU_HOME)
	),
	'status' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_status_page',
	),
	'update' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_update',
	),
	'replies' => array(
		'security' => true,
		'callback' => 'dabr_replies_page',
		'display'  => _(MENU_REPLIES)
	),
	'global' => array(
		'security' => true,
		'callback' => 'dabr_global_page',
		'display'  => _(MENU_GLOBAL)
	),
	'star' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_star_page',
	),
	'unstar' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_star_page',
	),
	'stars' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_stars_page',
	),
	'search' => array(
		'security' => true,
		'callback' => 'dabr_search_page',
		'display'  => _(MENU_SEARCH)
	),
	'user' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_user_page',
	),
	'follow' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_follow_page',
	),
	'unfollow' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_follow_page',
	),
	'confirm' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_confirmation_page',
	),
	'confirmed' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_confirmed_page',
	),
	'mute' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_mute_page',
	),
	'unmute' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_mute_page',
	),
	'starred' => array(
		'security' => true,
		'callback' =>  'dabr_starred_page',
		'display'  => _(MENU_STARRED)
	),
	'followers' => array(
		'security' => true,
		'callback' => 'dabr_users_page',
		'display'  => _(MENU_FOLLOWERS)
	),
	'friends' => array(
		'security' => true,
		'callback' => 'dabr_users_page',
		'display'  => _(MENU_FRIENDS)
	),
	'muted' => array(
		'hidden' => false,
		'security' => true,
		'callback' => 'dabr_users_page',
		'display'  => _(MENU_MUTED)
	),
	'delete' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_delete_page',
	),
	'repost' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_repost_page',
	),
	'reposters' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_reposters_page',
	),
	'repost-native' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_repost',
	),
	'hash' => array(
		'security' => true,
		'hidden' => true,
		'callback' => 'dabr_hashtag_page',
	),
	'raw' => array(
		'security' => true,
		'hidden' => true,
		'callback' => 'dabr_raw_page',
	),
	'settings' => array(
		'callback' => 'settings_page',
		'display'  => _(MENU_SETTINGS)
	),
	'reset' => array(
		'hidden' => true,
		'callback' => 'cookie_monster',
	),
	'oauth' => array(
		'callback' => 'user_oauth',
		'hidden' => 'true',
	),
	'login' => array(
		'callback' => 'user_login',
		'hidden' => 'true',
	),
	'editprofilepage' => array(
		'callback' => 'dabr_edit_profile_page',
		'display' => _(MENU_EDITPROFILE)
	),
	'editprofile' => array(
		'callback' => 'dabr_edit_profile',
		'hidden' => 'true'
	)
));

// How should external links be opened?
function get_target()
{
	// Kindle doesn't support opening in a new window
	if (stristr($_SERVER['HTTP_USER_AGENT'], "Kindle/"))
	{
		return "_self";
	}
	else
	{
		return "_blank";
	}
}

function long_url($shortURL)
{
	if (!defined('LONGURL_KEY'))
	{
		return $shortURL;
	}
	$url = "http://www.longurlplease.com/api/v1.1?q=" . $shortURL;
	$curl_handle=curl_init();
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl_handle,CURLOPT_URL,$url);
	$url_json = curl_exec($curl_handle);
	curl_close($curl_handle);

	$url_array = json_decode($url_json,true);

	$url_long = $url_array["$shortURL"];

	if ($url_long == null)
	{
		return $shortURL;
	}

	return $url_long;
}

function js_counter($name, $length='256')
{
	$script = '<script type="text/javascript">
function updateCount()
{
	var remaining = ' . $length . ' - document.getElementById("' . $name . '").value.length;
	document.getElementById("remaining").innerHTML = remaining;
	
	if(remaining < 0)
	{
		var colour = "#FF0000";
		var weight = "bold";
	} else if(remaining < 10)
	{
		var colour = "#FFFF00";
		var weight = "bold";
	} else {
 		var colour = "";
 		var weight = "";
	}
	document.getElementById("remaining").style.color = colour;
	document.getElementById("remaining").style.fontWeight = weight;
	setTimeout(updateCount, 400);
}
updateCount();
</script>';
	return $script;
}

function dabr_fetch($url)
{
	//	Track how long this is taking	
	global $services_time;

	//	Set up curl
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	//	Set a user agent so that webmasters know where to send complaints :-)
	$user_agent = "Mozilla/5.0 (compatible; dabr; " . BASE_URL . ")";
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

	// Send the authorisation for search
	if(strpos($url,"nanek.net")>1)
	{
		$basic = 'Authorization: Basic ' . SEARCH_KEY;
		curl_setopt($ch,CURLOPT_HTTPHEADER,array($basic));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$fetch_start = microtime(1);
	$response = curl_exec($ch);
	curl_close($ch);

	$services_time += microtime(1) - $fetch_start;
	return $response;
}

function utf8_substr_replace($original, $replacement, $position, $length)
{
	$startString = mb_substr($original, 0, $position, "UTF-8");
	$endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");
	$out = $startString . $replacement . $endString;
	return $out;
}

function dabr_parse_tags($input, $entities = false)
{	
	$out = $input;

	if (!$entities)	// Use the Autolink.
	{
		//	Hashtags and @ are internal links
		$out = Twitter_Autolink::create($out)->
					setExternal(false)->setNoFollow(false)->setTarget(false)->addLinksToHashtags();
		$out = Twitter_Autolink::create($out)->
					setExternal(false)->setNoFollow(false)->setTarget(false)->addLinksToUsernamesAndLists();
		//	URLs are external links
		$out = Twitter_Autolink::create($out)->
					setExternal(true)->setNoFollow(true)->setTarget(true)->addLinksToURLs();
	} else
	{
		$entities_array = array();

		//	Place the mention, hashtag, and link entities in the array. The key will be their position
		$offset = 0;

		foreach ($entities as $entity)
		{
			foreach ($entity as $item)
			{
				$position = $item['pos'];
				$entities_array[$position] = $item;
			}			
		}
		
		//	Sort the array by key. First entity will be first, etc.
		ksort($entities_array);

		foreach ($entities_array as $item)
		{
			if($item['id']) //	A user
			{
				$username = $item['name'];
				$position = $item['pos'];
				$length = $item['len'];
				// Using ASCII controll characters so our < & > don't get eaten later
				$userURL = '@' . chr(7). 'a href="' . BASE_URL . 'user/' . $username . '"' .chr(27) .
								$username .
							chr(7). '/a'. chr(27);
				
				$newPosition = ($position + $offset);

				$out = utf8_substr_replace($out, $userURL, $newPosition, $length);

				//	Calculate the new offset		
				$offset += mb_strlen($userURL, "UTF-8") - $length;
			} else if($item['text']) //	A url
			{
				$display = $item['text'];
				$position = $item['pos'];
				$length = $item['len'];

				if (setting_fetch('gwt') == 'on') // If the user wants links to go via GWT
				{
					$encoded = urlencode($item['url']);
					$url = "http://google.com/gwt/n?u={$encoded}";
				}
				else {
					$url = $item['url'];
				}
				// Using ASCII controll characters so our < & > don't get eaten later
				$linkURL = 	chr(7) . "a href=\"{$url}\" rel=\"external\" target=\"". get_target() . "\"" . chr(27) .
								"{$display}" .
							chr(7) . "/a" . chr(27);
				
				$newPosition = ($position + $offset);
				
				$out = utf8_substr_replace($out, $linkURL, $newPosition, $length);

				//	Calculate the new offset
				$offset += mb_strlen($linkURL, "UTF-8") - $length;
			} else {
				$hashtag = $item['name'];	//	A hashtag
				$position = $item['pos'];
				$length = $item['len'];
				$newPosition = ($position + $offset);
				
				//	Hashtags are stored case insensitive - but are written CaseSensitive
				//	+1 to remove extra #
				$displayHashtag = mb_substr($out, $newPosition+1, $length-1, "UTF-8");

				// Using ASCII controll characters so our < & > don't get eaten later
				$hashtagURL = 	'#' . chr(7) . 'a href="' . BASE_URL . 'hash/' . $hashtag . '"' . chr(27) .
									$displayHashtag .
								chr(7) . '/a' . chr(27);

				$out = utf8_substr_replace($out, $hashtagURL, $newPosition, $length);

				//	Calculate the new offset
				$offset += mb_strlen($hashtagURL, "UTF-8") - $length;
			}		
		}
	}

	//	Make the string safe to display
	$out = htmlspecialchars($out, ENT_NOQUOTES, 'UTF-8');

	//	Replace the control characters so that *our* markup stays present.
	$out = str_replace(chr(7), "<", $out);
	$out = str_replace(chr(27), ">", $out);			

	//	Linebreaks.  Some clients insert \n for formatting.
	$out = nl2br($out, false);

	//	Tabs don't render in HTML
	$out = str_replace("\t","&nbsp;&nbsp;&nbsp;",$out);

	//	Add Emoticons :-)
	if (setting_fetch('emoticons') != 'off') {
		$out = emoticons($out);
	}

	//Return the completed string
	return $out;
}

function format_interval($timestamp, $granularity = 2)
{
	$units = array(
		'year' => 31536000,
		'day'  => 86400,
		'hour' => 3600,
		'min'  => 60,
		'sec'  => 1
	);

	$output = '';
	foreach ($units as $key => $value)
	{
		if ($timestamp >= $value)
		{
			$output .= ($output ? ' ' : ''). pluralise($key, floor($timestamp / $value), true);
			$timestamp %= $value;
			$granularity--;
		}
		if ($granularity == 0)
			break;
	}

	return $output ? $output : '0 sec';
}

function dabr_status_page($query)
{
	$id = (string) $query[1];
	
	if (is_numeric($id)) // Are all IDs numeric?
	{
		$app = new EZAppDotNet();	
		if ($app->getSession())
		{	
			//	Track how long the API call took
			global $api_time;
			$api_start = microtime(1);

			//	Get the post
			try
			{	
				$post = $app->getPost($id, array('include_annotations' => 1));	
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}

			//	Get the Thread
			$thread_id = $post['thread_id'];
			
			//	Grab the text before it gets formatted
			$text = $post['text'];	

			$feed[] = $post;
			$content = theme('timeline', $feed);

			//	Show a link to the original post		
			$username = $post['user']['username'];
			$content .= '<p>
							<a href="https://alpha.app.net/' . $username . '/post/' . $id . '"
								target="'. get_target() . '" class="button">'._(LINK_VIEW_ORIGINAL).'</a> ';
			
			//	Translate the post
			$content .= 	'<a href="http://translate.google.com/?hl=en&amp;sl=auto&amp;ie=UTF-8&amp;vi=m&amp;q=' . 
								urlencode($text) . 
								'" target="'. get_target() . '" class="button" >'._(LINK_TRANSLATE).'</a>
						</p>';
			
			//	Add the reply box
			//	Text to pre-populate
			$status = "@" . $post['user']['username'] . " ";

			//	Reply all by default
			foreach ($post['entities']['mentions'] as $mention)
			{
				//	Don't add ourselves in the reply all
				if (strtolower($mention['name']) !== strtolower(user_current_username()))
					$status .= "@" . $mention['name'] . " ";	
			}

			// Add in the hashtags they've used
			foreach ($post['entities']['hashtags'] as $hashtag)
			{
				$status .= "#" . $hashtag['name'] . " ";
			}

			// Create the form where users can enter text
			$content .= dabr_post_form($status, $id);

			//	If this isn't the head of the thread, show the thread.
			//	If this is the head of the thread, only show if there are replies
			if ($thread_id != $id || $post['num_replies'] > 0)
			{
				try
				{
					$thread = $app->getPostReplies($thread_id,
												array(
													'count'=>setting_fetch('perPage', 20),
													'include_annotations' => 1
													)
												);
				}
				catch (Exception $e)
				{
					theme_error($e->getMessage());
				}

				$thread = array_reverse($thread);
				$content .= '<p>'._(CONVERSATION_VIEW).'</p>'.theme('timeline', $thread);
			}
			
			//	Track how long the API call took
			$api_time += microtime(1) - $api_start;

			theme('page', "Status $id", $content);
		}
	}
}


function dabr_refresh($page = NULL) {
	if (isset($page)) {
		$page = BASE_URL . $page;
	} else {
		$page = $_SERVER['HTTP_REFERER'];
	}
	header('Location: '. $page);
	exit();
}

function dabr_delete_page($query) {
	dabr_ensure_post_action();

	$id = (string) $query[1];
	if (is_numeric($id)) {
		
		$app = new EZAppDotNet();

		// check that the user is signed in
		if ($app->getSession())
		{
			try
			{
				$deleted = $app->deletePost($id);
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}
		}

		dabr_refresh('user/'.user_current_username());
	}
}

function dabr_ensure_post_action() {
	// This function is used to make sure the user submitted their action as an HTTP POST request
	// It slightly increases security for actions such as Delete, Block and Spam
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		theme_error(_(ERROR_INVALID_METHOD));
	}
}

function dabr_follow_page($query) 
{
	$app = new EZAppDotNet();
	
	if ($app->getSession())
	{
		$user = $query[1];
	
		if ($user)
		{
			try{
				if($query[0] == 'follow')
				{
					$app->followUser($user);
					
				} else {
					$app->unfollowUser($user);
				}
				dabr_refresh('friends');
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}
		}
	}
}

function dabr_mute_page($query) {
	dabr_ensure_post_action();
	$username = $query[1];

	if ($username)
	{
		$app = new EZAppDotNet();
		$username = "@" . $username;
		if ($app->getSession())
		{
			try
			{
				if($query[0] == 'mute')
				{
					$app->muteUser($username);
					dabr_refresh("confirmed/mute/{$username}");
				} else
				{
					$app->unmuteUser($username);
					dabr_refresh("confirmed/unmute/{$username}");
				}
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}
		}
	}
}


function dabr_confirmation_page($query)
{
	// the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
	$action = $query[1];
	$target = $query[2];	//The name of the user we are doing this action on

	$content = theme_get_logo() . '<br>';

	switch ($action) {
		case 'mute':
			$content .= "<p>" . sprintf(_('Are you sure you want to mute %s?'),$target) . "</p>";
			$content .= "<ul>
							<li>"._(MUTE_1)."</li>
							<li>"._(MUTE_2)."</li>
							<li>"._(MUTE_3)."</li>
							<li>"._(MUTE_4)."</li>
						</ul>";
			break;
		case 'unmute':
			$content .= "<p>"._(ARE_YOU_SURE)." <strong>$action $target</strong>?</p>";
			$content .= "<ul>
							<li>"._(UNMUTE_1)."</li>
							<li>"._(UNMUTE_2)."</li>
							<li>"._(UNMUTE_3)."</li>
						</ul>";
			break;
		case 'delete':
			$content .= "<p>"._(ARE_YOU_SURE_DELETE)."</p>";
			$content .= "<ul>
							<li>Post ID: <strong>$target</strong></li>
							<li><strong>"._(NO_UNDO)."</strong>.</li>
						</ul>";
			break;

		case 'spam':
			$content .= "<p>".sprintf(_('SPAM_1 %s'),$target)."</p>";
			$content .= "<p>"._(SPAM_2)."</p>";
			break;
	}

	$content .= "<form action='$action/$target' method='post'>
					<input type='submit' value='"._(CONFIRM_BUTTON)."' />
				</form>";
	
	theme('Page', 'Confirm', $content);
}

function dabr_confirmed_page($query)
{
		// the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
		$action = $query[1]; // The action. block, unblock, spam
		$target = $query[2]; // The username of the target

		$content = theme_get_logo() . '<br>';
	
		switch ($action) {
			case 'mute':
				$content  .= "<p>
								<span class='status shift'>"
									.sprintf(_('MUTED_1 %s'),$target)."
								</span>
							</p>";
				break;
			case 'unmute':
				$content  .= "<p>
								<span class='status shift'>"
									.sprintf(_('UNMUTED_1 %s'),$target)."									
								</span>
							</p>";
				break;
			case 'spam':
				$content .= "<p>
								<span class='status shift'>"
									.sprintf(_('SPAMMER_1 %s'),$target)."
								</span>
							</p>";
				break;
		}
 	theme ('Page', 'Confirmed', $content);
}

function dabr_users_page($query) {
	
	//friends, followers, something else?
	$page_type = $query[0];

	// Which user are looking for?
	$username = $query[1];

	// Belt & Braces :-)
	$id = $query[2];
	
	$perPage = setting_fetch('perPage', 20);	
	$before_id = $_GET['before_id'];
	$since_id = $_GET['since_id'];

	$app = new EZAppDotNet();
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		if ($username == null)
		{
			// Must be the logged in user.
			$username = "me"; // NOTE! Not "@me"!
		}else
		{
			$username = "@" . $username;
		}
		
		try
		{
			switch ($page_type) {
				case "friends":
					$users = $app->getFollowing($username);
					break;
				case "followers":
					$users = $app->getFollowers($username);
					break;
				case "muted":
					$users = $app->getMuted(); // Can only get the current user's muted list
					break;
				}
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}

		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;

		// Format the output
		$content = theme('users', $users);

		theme('page', $page_type, $content);
	}
}


function dabr_stars_page($query)
{	
	// Which post are looking for?
	$id = $query[1];

	$app = new EZAppDotNet();
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		try
		{
			$users = $app->getStars($id);
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}

		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;

		// Format the output
		$content = theme('users', $users);
		theme('page', "Starrers", $content);
	}
}

function dabr_reposters_page($query)
{	
	// Which post are looking for?
	$id = $query[1];

	$app = new EZAppDotNet();
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		try{
			$users = $app->getReposters($id);
		}catch (Exception $e)
		{
			theme_error($e->getMessage());
		}
	
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;

		// Format the output
		$content = theme('users', $users);
		theme('page', "Reposters", $content);
	}
}

function dabr_edit_profile()
{
	dabr_ensure_post_action();

	$app = new EZAppDotNet();

	if ($app->getSession())
	{
		//	Set the array of annotations which will passed to the update
		$annotations = array();

		//	Get the posted variables
		$name =			(string) $_POST['name'];
		$description =	(string) $_POST['description'];
		$timezone =		(string) $_POST['timezone'];
		$locale =		(string) $_POST['locale'];
		$homepage =		(string) $_POST['homepage'];
		$blog =			(string) $_POST['blog'];
		$twitter =		(string) $_POST['twitter'];
		$facebook =		(string) $_POST['facebook'];
		$description =	(string) $_POST['description'];
		
		//	Geolocation parameters
		list($lat, $long) = explode(',', $_POST['location']);
		if (is_numeric($lat) && is_numeric($long))
		{
			$post_data['lat'] = $lat;
			$post_data['long'] = $long;
		
			$locationValues = array(
									"latitude" => $lat,
									"longitude" => $long,
									"altitude" => 0,
									"horizontal_accuracy" => 0,
									"vertical_accuracy" => 0
								);

			$annotations[] =  array("type" => "net.app.core.geolocation",
									"value" => $locationValues);
		}else{
			$annotations[] =  array("type" => "net.app.core.geolocation");
		}

		//	All the other parameters
		if ($blog != "")
		{
			$annotations[] = array(	"type" => "net.app.core.directory.blog",
						"value" => array("url" => $blog));
		}else{
			$annotations[] = array(	"type" => "net.app.core.directory.blog");
		}

		if ($twitter != "")
		{
			$annotations[] = array(	"type" => "net.app.core.directory.twitter",
							"value" => array("username" => $twitter));
		}else{
			$annotations[] = array(	"type" => "net.app.core.directory.twitter");
		}
		
		if ($language != "")
		{
			$annotations[] = array(	"type" => "net.app.core.language",
									"value" => array("language" => $language));
		} else {
			$annotations[] = array(	"type" => "net.app.core.language");
		}
		
		if ($homepage != "")
		{
			$annotations[] = array(	"type" => "net.app.core.directory.homepage",
									"value" => array("url" => $homepage)
								);
		} else {
			$annotations[] = array(	"type" => "net.app.core.directory.homepage");
		}

		if ($facebook != "")
		{
			$annotations[] = array(	"type" => "net.app.core.directory.facebook",
						"value" => array("id" => $facebook)
					);
		} else {
			$annotations[] = array(	"type" => "net.app.core.directory.facebook");
		}

		//	Do the update
		try
		{
			$app->updateUserData(
					array(
						"name"			=> $name,
						"timezone"		=> $timezone,
						"description"	=> array("text" => $description),
						"locale"		=> $locale,
						"annotations"	=> $annotations
					)
				);
		}
		catch (Exception $e)
		{
			$response = $app->getLastResponse();
			$response = explode("\r\n\r\n",$response,2);
			$headers = $response[0];
			if (isset($response[1])) {
				$content = $response[1];
			}
			else {
				$content = null;
			}
			$response = json_decode($content,true);
			
			theme_error($response['error']['message']);
		}	
	}

	dabr_refresh($_POST['from'] ? $_POST['from'] : '');
}

function dabr_update() 
{
	dabr_ensure_post_action();

	//	Check for double posting
	$postedToken = $_POST['token'];
	if(($postedToken))
	{
		if(isTokenValid($postedToken))
		{
			//	Continue as normal
		}
		else{
			//	Do something about the error
			theme_error("Double Post. Double Post.  Slow down, partner!");
		}
	}

	$annotations = array();
	$status = stripslashes(trim($_POST['status']));

	// Convert linebreaks to be a single character
	$status = str_replace(array("\r\n", "\r", "\n"), "\n", $status);

	if ($_FILES['image']['tmp_name'])
	{
		$user_name = user_current_username();
		$user_url = "http://alpha.app.net/" . $user_name;

		$imgur_url = "http://api.imgur.com/2/upload.json";

		$image = "@{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}";
		
		$imgur_key = IMGUR_API_KEY;

		$imgur_array = array(	'key' => $imgur_key,
								'image' => $image,
								'caption' => " " . $status . " - from " . $user_url . " via #Dabr");
		$timeout = 30;
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $imgur_url);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $imgur_array);

		$imgur_json = curl_exec($curl);

		curl_close ($curl);

		/*
		{
			"type": "net.app.core.oembed",
			"value": {
				"version": "1.0",
				"type": "photo",
				"width": 240,
				"height": 160,
				"title": "ZB8T0193",
				"url": "http://farm4.static.flickr.com/3123/2341623661_7c99f48bbf_m.jpg",
				"author_name": "Bees",
				"author_url": "http://www.flickr.com/photos/bees/",
				"provider_name": "Flickr",
				"provider_url": "http://www.flickr.com/",
				"embeddable_url": "http://www.flickr.com/photos/bees/2341623661/"
				}
			}
		*/

		$imgur_json = json_decode($imgur_json,true);

		$imgur_width = $imgur_json["upload"]["image"]["width"];
		$imgur_height = $imgur_json["upload"]["image"]["height"];
		$imgur_title = $imgur_json["upload"]["image"]["title"];
		$imgur_original = $imgur_json["upload"]["links"]["original"];
		$imgur_page = $imgur_json["upload"]["links"]["imgur_page"];

		$oembed = array(
				"type" => "net.app.core.oembed",
				"value" => array(
					"version"	=> "1.0",
					"type"		=> "photo",
					"width"		=> $imgur_width,
					"height"	=> $imgur_height,
					"title"		=> $imgur_title,
					"url"		=> $imgur_original,
					"author_name"	=> $user_name,
					"author_url"	=> $user_url,
					"provider_name"	=> "imgur",
					"provider_url"	=> "http://imgur.com/",
					"embeddable_url"=> $imgur_page
				)
			);

		$annotations[] = $oembed;

		//	If there is space, add the string. If not, just the annotation
		//	URL + Space
		if (strlen($status) <= (256 - strlen($imgur_page) -1))
		{
			$status .= "\n" . $imgur_page;
		} //	Drop the http://
		else if (strlen($status) <= (256 - (strlen($imgur_page)-7) -1))
		{
			$status .= "\n" .  substr($imgur_page,7);
		}
	}

	if ($status) {
		$app = new EZAppDotNet();
		if ($app->getSession())
		{
			$in_reply_to_id = (string) $_POST['in_reply_to_id'];

			// Geolocation parameters
			list($lat, $long) = explode(',', $_POST['location']);
			if (is_numeric($lat) && is_numeric($long))
			{
				$post_data['lat'] = $lat;
				$post_data['long'] = $long;
			
				$locationValues = array(
										"latitude" => $lat,
										"longitude" => $long,
										"altitude" => 0,
										"horizontal_accuracy" => 0,
										"vertical_accuracy" => 0
									);

				$locationAnnotation =  array(	"type" => "net.app.core.geolocation",
												"value" => $locationValues
											);

				$annotations[] = $locationAnnotation;	
			}
			
			try
			{
				$app->createPost($status,array(
						'reply_to' => $in_reply_to_id, 
						'annotations' =>$annotations
				));
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}	
		}
	}
	dabr_refresh($_POST['from'] ? $_POST['from'] : '');
}

function dabr_replies_page($query)
{
	// Which user's replies are we looking for?
	$username = $query[1];

	if ($username == null)
	{
		// Must be the logged in user.
		$username = "me"; // NOTE! Not "@me"!
	}else
	{
		$username = "@" . $username;
	}

	$perPage = setting_fetch('perPage', 20);	
	$before_id = $_GET['before_id'];
	$since_id = $_GET['since_id'];
	
	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		// Create the form where users can enter text
		$content = dabr_post_form();//theme('status_form');
	
		try
		{
			$stream = $app->getUserMentions($username, array(
												'count'=>$perPage,
												'before_id'=>$before_id,
												'since_id'=>$since_id,
												'include_annotations' => 1
												)
											);

		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}
		
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		//print_r($stream);
		$content .= theme('timeline', $stream);
			
		theme('page', 'Replies to ' . $username, $content);
	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}

}

function dabr_edit_profile_page($query)
{
	// Must be the logged in user.
	$username = "me"; // NOTE! Not "@me"!

	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		try
		{
			$user = $app->getUser($username, array('include_annotations' => 1));
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}
		
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		$content .= theme('edit_profile', $user, $query[1]=="updated");
			
		theme('page', _(MENU_EDITPROFILE), $content);
	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}
}

function dabr_starred_page($query)
{
	$perPage = setting_fetch('perPage', 20);	
	$before_id = $_GET['before_id'];
	$since_id = $_GET['since_id'];
	
	$username = $query[1];

	if ($username == null)
	{
		// Must be the logged in user.
		$username = "me"; // NOTE! Not "@me"!
	}else
	{
		$username = "@" . $username;
	}

	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		try {
			$stream = $app->getStarred($username, array(
				'count'=>$perPage,
				'before_id'=>$before_id,
				'since_id'=>$since_id,
				'include_annotations'=>1
			));
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}

		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		//print_r($stream);
		$content .= theme('timeline', $stream);
			
		theme('page', 'Posts Starred by ' . $username, $content);
	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}
}

function dabr_global_page()
{
	//	How many posts to get
	$perPage = setting_fetch('perPage', 20);
	$before_id = $_GET['before_id'];
	$since_id = $_GET['since_id'];
	
	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		// Create the form where users can enter text
		$content = dabr_post_form();
	
		// get the latest public posts
		try
		{
			$stream = $app->getPublicPosts(	array('count'=>$perPage,
													'before_id'=>$before_id,
													'since_id'=>$since_id,
													'include_annotations'=>1
												)
										);			
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}

		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		//print_r($stream);
		$content .= theme('timeline', $stream);
			
		theme('page', 'Global', $content);
	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}
}

function dabr_search_page()
{
	$search_query = $_GET['query'];
	$search_type = $_GET['type'];

	// Geolocation parameters
	list($lat, $long) = explode(',', $_GET['location']);
	$loc = $_GET['location'];
	$radius = $_GET['radius'];

	if (isset($_POST['query']))
	{
		$duration = time() + (3600 * 24 * 365);
		setcookie('search_favourite', $_POST['query'], $duration, '/');
		dabr_refresh('search');
	}
	
	if (!isset($search_query) && array_key_exists('search_favourite', $_COOKIE))
	{
		$search_query = $_COOKIE['search_favourite'];
	}

	$content = theme('search_form', $search_query, $search_type);

	if ($search_query)
	{
		if ($search_type == "users")
		{
			$app = new EZAppDotNet();
			if ($app->getSession())
			{
				//	Track how long the API call took
				global $api_time;
				$api_start = microtime(1);

				//	Search for users
				try
				{
					$users = $app->searchUsers($search_query);
				}
				catch (Exception $e)
				{
					theme_error($e->getMessage());
				}

				//	Track how long the API call took
				$api_time += microtime(1) - $api_start;

				$content .= theme('users', $users);

				theme('page',
					'Users matching '.stripslashes(htmlentities($search_query,ENT_QUOTES,"UTF-8")),
					$content);
			}
		}
		else	//	Normal search for posts
		{
			$tl = dabr_search($search_query);//, $lat, $long, $radius);

			if ($search_query !== $_COOKIE['search_favourite'])
			{
				$content .= '<form action="search/bookmark" method="post">
								<input type="hidden" name="query" value="'.$search_query.'" />
								<input type="submit" value="'._(SAVE_DEFAULT_SEARCH).'" />
							</form>';
			}
			$content .= theme('timeline', $tl);
		}
	}

	theme('page', 'Search', $content);
}

function dabr_search($search_query)
{
	$perPage = setting_fetch('perPage', 20);
	$page = (int) $_GET['page'];
	if ($page == 0) $page = 1;
	
	$request =	'https://api.nanek.net/search?per_page='.'20'.//$perPage.
				'&q=' . urlencode($search_query).
				'&page='.$page;
	$tl = json_decode(dabr_fetch($request),true);

	return $tl['results'];
}

function dabr_hashtag_page($query)
{
	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession())
	{
		if (isset($query[1]))
		{
			$hashtag = $query[1];
			$perPage = setting_fetch('perPage', 20);
			$before_id = $_GET['before_id'];
			$since_id = $_GET['since_id'];

			// Create the form where users can enter text prepopulated with the hashtag
			$content = dabr_post_form("#".$hashtag);
			//	Track how long the API call took
			global $api_time;
			$api_start = microtime(1);

			//	Search for hashtags
			try
			{
				$stream = $app->searchHashtags($hashtag,
											array('count'=>$perPage,
												'before_id'=>$before_id,
												'since_id'=>$since_id,
												'include_annotations'=>1
											)
										);
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}

			//	Track how long the API call took
			$api_time += microtime(1) - $api_start;


			$content .= theme('timeline', $stream);
			theme('page', '#'.$hashtag, $content);
		}
		else
		{
			theme('page', 'Hashtag', 'Hash hash!');
		}
	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}

	theme('page', $page_title, $content);
}

function dabr_find_post_in_timeline($id, $stream)
{
	//	Check if the post exists in the timeline given
	//	Look through the stream & see if the post we're replying to is in there.
	foreach ($stream as $post)
	{
		if ($post['id'] == $id)
		{	
			$found_post = $post;
		}
	}

	//	If it wasn't found, grab it directly
	if (!$reply_post)
	{
		$app = new EZAppDotNet();

		// Not found, fetch it specifically from the API
		if ($app->getSession())
		{
			try
			{
				$found_post = $app->getPost($id, array('include_annotations' => 1));
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}
		}
	}
	return $found_post;
}

function dabr_user_page($query)
{
	$user_name = $query[1];
	$before_id = $_GET['before_id'];
	$since_id = $_GET['since_id'];
	$subaction = $query[2];
	$perPage = setting_fetch('perPage', 20);

	// Get the ID of the post to which we are replying
	$in_reply_to_id = (string) $query[3];

	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession())
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		//	Get the user's name
		try
		{
			$user = $app->getUser("@".$user_name, array('include_annotations' => 1));
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}

		//	Start building the status
		$status = "@" . $user_name . " ";

		// get the user stream early, so we can search for reply to all.
		try
		{
			$stream = $app->getUserPosts("@" . $user_name,
										array(
											'count'=>$perPage,
											'before_id'=>$before_id,
											'since_id'=>$since_id,
											'include_annotations'=>1
										)
									);
		}catch (Exception $e)
		{
			theme_error($e->getMessage());
		}

		if ($subaction == "reply" || $subaction == "replyall")
		{
			$reply_post = dabr_find_post_in_timeline($in_reply_to_id,$stream);

			// Add in the hashtags they've used
			foreach ($reply_post['entities']['hashtags'] as $hashtag)
			{
				$status .= "#" . $hashtag['name'] . " ";
			}

			//	Is this a reply all?		
			if ($subaction == "replyall")
			{
				foreach ($reply_post['entities']['mentions'] as $mention)
				{
					$status .= "@" . $mention['name'] . " ";	
				}
			}
		
			$content .= "<p>"._(IN_REPLY_TO)."<br>" . $reply_post['text'] . "</p>";
		}
		
		// Create the form where users can enter text
		$content .= dabr_post_form($status, $in_reply_to_id);

		$content .= theme('user_header', $user);
		
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		$content .= theme('timeline', $stream);
			
		$page_title = "@" . $user_name;

		theme('page', $page_title, $content);
	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}

	theme('page', "User {$screen_name}", $content);
}

function dabr_star_page($query)
{
	$id = (string) $query[1];

	$app = new EZAppDotNet();
	
	if ($app->getSession())
	{
		try
		{
			if ($query[0] == 'unstar')
			{
				$app->unstarPost($id);
			} else {
				$app->starPost($id);
			}	

			dabr_refresh();
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}
	}
}

function dabr_home_page()
{
	$before_id = $_GET['before_id'];
	$since_id = $_GET['since_id'];

	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession()) {
	
		// Create the form where users can enter text
		$content = dabr_post_form();
		
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		//	get the stream
		try
		{	
			$stream = $app->getUserStream(
								array(
									'count'=>setting_fetch('perPage', 20),
									'before_id'=>$before_id,
									'since_id'=>$since_id,
									'include_annotations' => 1,
									'include_directed_posts' => setting_fetch('nondirected', 0)
									)
								);
		}catch (Exception $e)
		{
			theme_error($e->getMessage());
		}
		
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		//print_r($stream);
		$content .= theme('timeline', $stream);

	// otherwise prompt to sign in
	} else {
		$content = sign_in();
	}

	theme('page', 'Home', $content);
}

function dabr_raw_page($query) {
	if (isset($query[1]))
	{
		$app = new EZAppDotNet();
		if ($app->getSession())
		{
			// Dump the post to screen
			$thread = $app->getPost($query[1],array('include_annotations' => 1));
			echo "<pre>";
				print_r($thread);
			echo "</pre>";
		}
	}
}

function dabr_post_form($text = '', $in_reply_to_id = NULL)
{
	$geoJS = '<script type="text/javascript">
				started = false;
				chkbox = document.getElementById("geoloc");
				if (navigator.geolocation) {
					geoStatus("'._(SHARE_MY_LOCATION).'");
					if ("'.$_COOKIE['geo'].'"=="Y") {
						chkbox.checked = true;
						goGeo();
					}
				}
				function goGeo(node) {
					if (started) return;
					started = true;
					geoStatus("Locating...");
					navigator.geolocation.getCurrentPosition(geoSuccess, geoStatus , { enableHighAccuracy: true });
				}
				function geoStatus(msg) {
					document.getElementById("geo").style.display = "inline";
					document.getElementById("lblGeo").innerHTML = msg;
				}
				function geoSuccess(position) {
					geoStatus("<a href=\'http://maps.google.co.uk/?q=" + 
						position.coords.latitude + "," + position.coords.longitude + 
						"\' target=\''. get_target() . '\'>'._(SHARE_MY_LOCATION).'</a>");
					chkbox.value = position.coords.latitude + "," + position.coords.longitude;
				}
			</script>';

	if (user_is_authenticated())
	{
		//	adding ?status=foo will automaticall add "foo" to the text area.
		if ($_GET['status'])
		{
			$text = $_GET['status'];
		}

		if ($in_reply_to_id !== NULL)
		{
			$title = _(POST_CAPTION);
		} else {
			$title = _(POST_CAPTION_REPLY);
		}
		
		return "<fieldset>
					<legend>
						<strong>&alpha;</strong> {$title}
					</legend>
				
					<form method=\"post\" action=\"update\" enctype=\"multipart/form-data\">
						<input type=\"hidden\" name=\"token\" value=\"" . getToken() . "\"/>
						<textarea	id=\"status\" 
									name=\"status\" 
									rows=\"4\" 
									style=\"width:95%; 
									max-width: 400px;\">$text</textarea>
												
						<div class=\"fileinputs\">
							Image: <input type=\"file\" accept=\"image/*\" name=\"image\" class=\"file\" />
						</div>

						<div>
							<input name=\"in_reply_to_id\" value=\"$in_reply_to_id\" type=\"hidden\" />
							<input type=\"submit\" value=\"Post\" />
							<span id=\"remaining\">256</span>
							<span id=\"geo\" style=\"display: none;\">
								<input onclick=\"goGeo()\" type=\"checkbox\" id=\"geoloc\" name=\"location\" />
								<label for=\"geoloc\" id=\"lblGeo\"></label>
							</span>
						</div>
					</form>
				</fieldset>" . $geoJS . js_counter('status');
	}
}

function dabr_repost_page($query)
{
	$id = (string) $query[1];
	
	if (is_numeric($id))
	{
		$app = new EZAppDotNet();
		if ($app->getSession())
		{
			//	Track how long the API call took
			global $api_time;
			$api_start = microtime(1);
		
			try
			{
				$post = $app->getPost($id,array('include_annotations' => 1));
			}
			catch (Exception $e)
			{
				theme_error($e->getMessage());
			}

			//	Track how long the API call took
			$api_time += microtime(1) - $api_start;
		}
	}

	$text = "»@{$post['user']['username']}: {$post['text']}";
	$length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen($text);
	$from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));

	$content .= "<p>"._(NATIVE_REPOST)."</p>
				<form action='repost-native/{$id}' method='post'>
					<input type='submit' value='"._(NATIVE_REPOST_BUTTON)."' />
				</form>
				<hr />";

	$content .= "<p>"._(EDIT_BEFORE_REPOST)."</p>
					<form action='update' method='post'>
						<input type='hidden' name='from' value='$from' />
						<input name='in_reply_to_id' value='$id' type='hidden' />
						<textarea	name='status' 
									style='width:90%; max-width: 400px;' 
									rows='6' 
									id='status'>$text</textarea>
						<br>
						<input type='submit' value='"._(NATIVE_REPOST_BUTTON)."' />
						<span id='remaining'>" . (256 - $length) ."</span>
					</form>";
	$content .= js_counter("status");

	theme('page', 'Repost', $content);
}

function dabr_repost($query)
{
	$id = (string) $query[1];
	
	$app = new EZAppDotNet();
	if ($app->getSession())
	{
		try
		{
			$app->repost($id);
		}
		catch (Exception $e)
		{
			theme_error($e->getMessage());
		}
		
		dabr_refresh('');
	}
}

function dabr_posts_per_day($user, $rounding = 1) {
	// Helper function to calculate an average count of posts per day
	$days_since_joined = (time() - strtotime($user['created_at'])) / 86400;
	return round($user['counts']['posts'] / $days_since_joined, $rounding);
}

function dabr_user_bio($user)
{
	$name = $user['name'];
	$username = $user['username'];
	$follows_you = $user['follows_you'];
	$you_follow = $user['you_follow'];
	$you_muted = $user['you_muted'];

	$posts_per_day = dabr_posts_per_day($user);

	$raw_date_joined = strtotime($user['created_at']);
	$date_joined = date('jS M Y', $raw_date_joined);

	if ($user['annotations'] > 0)
	{
		foreach($user['annotations'] as $annotation)
		{
			if ($annotation['type'] == "net.app.core.geolocation")
			{
				$lat = $annotation['value']['latitude'];
				$long = $annotation['value']['longitude'];
			}

			if ($annotation['type'] == "net.app.core.directory.blog")
			{
				$blog = $annotation['value']['url'];
			}

			if ($annotation['type'] == "net.app.core.directory.twitter")
			{
				$twitter = $annotation['value']['username'];
			}

			if ($annotation['type'] == "net.app.core.language")
			{
				$language = $annotation['value']['language'];
			}

			if ($annotation['type'] == "net.app.core.directory.homepage")
			{
				$homepage = $annotation['value']['url'];
			}

			if ($annotation['type'] == "net.app.core.directory.facebook")
			{
				$facebook = $annotation['value']['id'];
			}
		}
	}

	$links = "";

	if ($blog != "")
	{
		$links .="Blog: <a href=\"$blog\" target=\"". get_target() . "\">$blog</a> ";		
	}
	if ($homepage != "")
	{
		$links .="Homepage: <a href=\"$homepage\" target=\"". get_target() . "\">$homepage</a> ";		
	}
	if ($twitter != "")
	{
		$links .="Twitter: <a href=\"https://twitter.com/".$twitter."\" target=\"". get_target() . "\">@$twitter</a> ";		
	}
	if ($facebook != "")
	{
		$links .="<a href=\"https://facebook.com/".$facebook."\" target=\"". get_target() . "\">Facebook</a> ";		
	}
	if ($lat != "")
	{
		$links = "<a href=\"http://maps.google.co.uk/?q=".$lat.",".$long."\" target=\"".get_target()."\">Location</a>";					
	}

	$bio = "";

	if($user['description']['text'] != "")
		$bio .= dabr_parse_tags($user['description']['text'], $user['description']['entities']) . "<br>";
	
	if($user['timezone'] != "")
		$bio .= _(TIMEZONE) . " " . str_replace("_", " ", $user['timezone']) . "<br>";

	$bio .= _(JOINED) . " " . $date_joined . " - ";
	$bio .= pluralise('post', (int)$user['counts']['posts'], true) . " ";
	$bio .= "(~" . pluralise('post', $posts_per_day, true) . " per day). ";
	
	if ($follows_you && $you_follow)
	{
		$bio .= _(BEST_MATES);			
	}
	else if ($follows_you)
	{
		$bio .= _(FOLLOWS) . " ";
	}
	else if ($you_follow)
	{
		$bio .= _(FOLLOWING) . " ";
	}

	if ($you_muted)
	{
		$bio .= " " . _(SH_MUTED) ." ";
	}

	$bio .= "<br>" . $links;

	return $bio;
}

function dabr_user_actions($user, $link=TRUE)
{
	$username = $user['username'];
	$you_muted = $user['you_muted'];
	$you_follow = $user['you_follow'];
	$id = $user['id'];

	$actions ="";

	if ($link)
	{
		$actions .= "<a href='followers/{$username}'>" . 
						pluralise('follower', $user['counts']['followers'], true) . 
					"</a>";
		$actions .= " | <a href='friends/{$username}'>" . 
							pluralise('friend', $user['counts']['following'], true) . 
						"</a>";
		$actions .= " | <a href='starred/{$username}'>Starred Posts</a>";
		
		//	User cannot perform certain actions on herself
		if (strtolower($username) !== strtolower(user_current_username()))
		{
			if ($you_follow == false) {
				$actions .= " | <a href='follow/{$id}'>"._(FOLLOW)."</a>";
			}
			else {
				$actions .= " | <a href='unfollow/{$id}'>"._(UNFOLLOW)."</a>";
			}

			if ($you_muted)
			{
				$actions .= " | <a href='confirm/unmute/{$username}'>"._(UNMUTE)."</a>";
			}else
			{
				$actions .= " | <a href='confirm/mute/{$username}'>"._(MUTE)."</a>";
			}
			
			$actions .= " | <a href='confirm/spam/{$username}/{$id}'>"._(REPORT_SPAM)."</a>";
		}
		
		$actions .= " | <a href='replies/{$username}'>" . sprintf(_('SEARCH_AT %s'),$username) . "</a>";
	}else
	{
		$actions .= pluralise('follower', $user['counts']['followers'], true);
		$actions .= ". " . pluralise('friend', $user['counts']['following'], true) . ".";
	}	
	return $actions;
}


function dabr_date($format, $timestamp = null)
{
	$offset = setting_fetch('utc_offset', 0) * 3600;
	if (!isset($timestamp)) {
		$timestamp = time();
	}
	return gmdate($format, $timestamp + $offset);
}

function preg_match_one($pattern, $subject, $flags = NULL) {
	preg_match($pattern, $subject, $matches, $flags);
	return trim($matches[1]);
}

function dabr_is_reply($status)
{
	if (!user_is_authenticated()) {
		return false;
	}
	$user = user_current_username();

	//	Use Entities to see if this contains a mention of the user
	if ($status['entities'])	// If there are entities
	{
		if ($status['entities']['mentions'])
		{
			$entities = $status['entities'];
			
			foreach($entities['mentions'] as $mentions)
			{
				if ($mentions['name'] == $user)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	// If there are no entities (for example on a search) do a simple regex
	$found = Twitter_Extractor::create($status->text)->extractMentionedUsernames();
	foreach($found as $mentions)
	{
		// Case insensitive compare
		if (strcasecmp($mentions, $user) == 0)
		{
			return true;
		}
	}
	return false;
}


function pluralise($word, $count, $show = FALSE) 
{
	if($show) $word = number_format($count) . " {$word}";
	return $word . (($count != 1) ? 's' : '');
}

function is_64bit() 
{
	$int = "9223372036854775807";
	$int = intval($int);
	return ($int == 9223372036854775807);
}

//	http://stackoverflow.com/a/4614123/1127699
//	Generate a token.  Used to prevent double form submission
function getToken()
{
	$token = sha1(mt_rand());
	if(!isset($_SESSION['tokens']))
	{
		$_SESSION['tokens'] = array($token => 1);
	}
	else {
		$_SESSION['tokens'][$token] = 1;
	}
	return $token;
}

/**
 * Check if a token is valid. Removes it from the valid tokens list
 * @param string $token The token
 * @return bool
 */
function isTokenValid($token)
{
	if(!empty($_SESSION['tokens'][$token]))
	{
		unset($_SESSION['tokens'][$token]);
		return true;
	}
	return false;
}