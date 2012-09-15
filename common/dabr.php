<?php

require 'Autolink.php';
require 'Extractor.php';
require 'Embedly.php';
require 'Emoticons.php';
require_once 'menu.php';
		
menu_register(array(
	'' => array(
		'callback' => 'dabr_home_page',
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
	),
	'global' => array(
		'security' => true,
		'callback' => 'dabr_global_page',
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
	),
	'followers' => array(
		'security' => true,
		'callback' => 'dabr_users_page',
	),
	'friends' => array(
		'security' => true,
		'callback' => 'dabr_users_page',
	),
	'muted' => array(
		'hidden' => false,
		'security' => true,
		'callback' => 'dabr_users_page',
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
	'hyper' => array(
		'security' => true,
		'hidden' => true,
		'callback' => 'dabr_hyper_page',
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
function updateCount() {
var remaining = ' . $length . ' - document.getElementById("' . $name . '").value.length;
document.getElementById("remaining").innerHTML = remaining;
if(remaining < 0) {
 var colour = "#FF0000";
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
		$out = Twitter_Autolink::create($out)->setExternal(false)->setNoFollow(false)->setTarget(false)->addLinksToHashtags();
		$out = Twitter_Autolink::create($out)->setExternal(false)->setNoFollow(false)->setTarget(false)->addLinksToUsernamesAndLists();
		
		//	URLs are external links
		$out = Twitter_Autolink::create($out)->setExternal(true)->setNoFollow(true)->setTarget(true)->addLinksToURLs();
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
				$userURL = '@' . chr(7). 'a href="' . BASE_URL . 'user/' . $username . '"' .chr(27) . 
								$username . 
							chr(7). '/a'. chr(27);	// Using ASCII controll characters so our < & > don't get eaten later
				
				$newPosition = ($position + $offset);

				$out = utf8_substr_replace($out, $userURL, $newPosition, $length);

				//	Calculate the new offset		
				$offset += mb_strlen($userURL, "UTF-8") - $length;
			} else if($item['text']) //	A url
			{
				$display = $item['text'];
				$position = $item['pos'];
				$length = $item['len'];
				$url = $item['url'];
				$linkURL = 	chr(7) . "a href=\"{$url}\" rel=\"external\" target=\"_blank\"" . chr(27) .
								"{$display}" . 
							chr(7) . "/a" . chr(27);	// Using ASCII controll characters so our < & > don't get eaten later
				
				$newPosition = ($position + $offset);
				
				$out = utf8_substr_replace($out, $linkURL, $newPosition, $length);

				//	Calculate the new offset
				$offset += mb_strlen($linkURL, "UTF-8") - $length;
			} else {
				$hashtag = $item['name'];	//	A hashtag
				$position = $item['pos'];
				$length = $item['len'];
				$hashtagURL = 	'#' . chr(7) . 'a href="' . BASE_URL . 'hash/' . $hashtag . '"' . chr(27) . 
									$hashtag . 
								chr(7) . '/a' . chr(27);	// Using ASCII controll characters so our < & > don't get eaten later
				
				$newPosition = ($position + $offset);
				
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
	$out = nl2br($out);

	//Return the completed string
	return $out;
}

function format_interval($timestamp, $granularity = 2) {
	$units = array(
	'year' => 31536000,
	'day'  => 86400,
	'hour' => 3600,
	'min'  => 60,
	'sec'  => 1
	);
	$output = '';
	foreach ($units as $key => $value) {
		if ($timestamp >= $value) {
			$output .= ($output ? ' ' : ''). pluralise($key, floor($timestamp / $value), true);
			$timestamp %= $value;
			$granularity--;
		}
		if ($granularity == 0) {
			break;
		}
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
			$post = $app->getPost($id, array('include_annotations' => 1));	

			//	Get the Thread 
			$thread_id = $post['thread_id'];
			
			//	Grab the text before it gets formatted
			$text = $post['text'];	

			$feed[] = $post;
			$content = theme('timeline', $feed);

			//	Show a link to the original post		
			$username = $post['user']['username'];
			$content .= '<p><a href="https://alpha.app.net/' . $username . '/post/' . $id . '" target="'. get_target() . '">View orginal post on AppDotNet</a> | ';
			
			//	Translate the post
			$content .= '<a href="http://translate.google.com/m?hl=en&sl=auto&ie=UTF-8&q=' . urlencode($text) . '" target="'. get_target() . '">Translate this post</a></p>';
			
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

			//	If this isn't the head of the thread, show the thread. If this is the head of the thread, only show if there are replies
			if ($thread_id != $id || $post['num_replies'] > 0) 
			{
				$thread = array_reverse($app->getPostReplies($thread_id, 
												array(
													'count'=>setting_fetch('perPage', 20),
													'include_annotations' => 1
													)
												)
										);
				$content .= '<p>And the conversation view...</p>'.theme('timeline', $thread);
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
			$deleted = $app->deletePost($id);
		}


		dabr_refresh('user/'.user_current_username());
	}
}

function dabr_ensure_post_action() {
	// This function is used to make sure the user submitted their action as an HTTP POST request
	// It slightly increases security for actions such as Delete, Block and Spam
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		die('Error: Invalid HTTP request method for this action.');
	}
}

function dabr_follow_page($query) {

	$app = new EZAppDotNet();
	
	if ($app->getSession())
	{
		$user = $query[1];
	
		if ($user) 
		{
			if($query[0] == 'follow')
			{
				$app->followUser($user);
			} else {
				$app->unfollowUser($user);
			}
			dabr_refresh('friends');
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
	}
}


function dabr_confirmation_page($query)
{
	// the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
	$action = $query[1];
	$target = $query[2];	//The name of the user we are doing this action on

	switch ($action) {
		case 'mute':
			$content = "<p>Are you really sure you want to <strong>$action $target</strong>?</p>";
			$content .= "<ul>
							<li>You won't see any of their posts in your timeline</li>
							<li>They won't appear in your replies</li>
							<li>They <em>will still be able to follow you and see your posts</em></li>
							<li>You <em>can</em> unmute them later</li>
						</ul>";
			break;
		case 'unmute':
			$content = "<p>Are you really sure you want to <strong>$action $target</strong>?</p>";
			$content .= "<ul>
							<li>You will see their posts in your timeline if you follow them.</li>
							<li>Their posts will appear in your replies</li>
							<li>You <em>can</em> mute them later</li>
						</ul>";
			break;
		case 'delete':
			$content = "<p>Are you really sure you want to delete your post?</p>";
			$content .= "<ul>
							<li>Post ID: <strong>$target</strong></li>
							<li>There is <strong>no way to undo this action</strong>.</li>
						</ul>";
			break;

		case 'spam':
			$content  = "<p>Are you really sure you want to report <strong>$target</strong> as a spammer?</p>";
			$content .= "<p>They will also be blocked from following you.</p>";
			break;
	}

	$content .= "<form action='$action/$target' method='post'>
					<input type='submit' value='Yes please' />
				</form>";
	
	theme('Page', 'Confirm', $content);
}

function dabr_confirmed_page($query)
{
        // the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
        $action = $query[1]; // The action. block, unblock, spam
        $target = $query[2]; // The username of the target
	
		switch ($action) {
			case 'mute':
				$content  = "<p>
								<span class='avatar'>
									<img src='images/dabr.png' width='48' height='48' alt='Dabr Muted Icon' />
								</span>
								<span class='status shift'>
									Shhhhhh $target! You are now <strong>muted</strong>.
								</span>
							</p>";
				break;
			case 'unmute':
				$content  = "<p>
								<span class='avatar'>
									<img src='images/dabr.png' width='48' height='48' alt='Dabr Unmuted Icon' />
								</span>
								<span class='status shift'>
									Hello again $target - you have been <strong>unmuted</strong>.
								</span>
							</p>";
				break;
			case 'spam':
				$content = "<p>
								<span class='avatar'>
									<img src='images/dabr.png' width='48' height='48' alt='Dabr Spam Icon'/>
								</span>
								<span class='status shift'>
									Yum! Yum! Yum! Delicious spam! Goodbye @$target.
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
		
		switch ($page_type) {
			case "friends":
				$users = $app->getFollowing($username);
				break;
			case "followers":
				$users = $app->getFollowers($username);
				break;
			case "starred":
				$users = $app->getStars($id);
				break;
			case "muted":
				$users = $app->getMuted(); // Can only get the current user's muted list
				break;
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

		$users = $app->getStars($id);
	
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;

		// Format the output
		$content = theme('users', $users);
		theme('page', $page_type, $content);
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

		$users = $app->getReposters($id);
	
		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;

		// Format the output
		$content = theme('users', $users);
		theme('page', $page_type, $content);
	}
}

function dabr_update() {
	dabr_ensure_post_action();
	$status = stripslashes(trim($_POST['status']));
	if ($status) {
		$app = new EZAppDotNet();
		if ($app->getSession()) 
		{
			$in_reply_to_id = (string) $_POST['in_reply_to_id'];

			// Geolocation parameters
			list($lat, $long) = explode(',', $_POST['location']);
			$annotations;
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

				$app->createPost($status,array('reply_to' => $in_reply_to_id, 'annotations' =>$annotations));
			}
			else{
				$app->createPost($status,array('reply_to' => $in_reply_to_id));	
			}
			
		}
	}
	dabr_refresh($_POST['from'] ? $_POST['from'] : '');
}

function dabr_replies_page() 
{
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
	
		// get the current user as JSON
		//$data = $app->getUser();

		$stream = $app->getUserMentions('me', array(
												'count'=>$perPage,
												'before_id'=>$before_id,
												'since_id'=>$since_id,
												'include_annotations' => 1
												)
										);

		//	Track how long the API call took
		$api_time += microtime(1) - $api_start;
		
		//print_r($stream);
		$content .= theme('timeline', $stream);
			
		theme('page', 'Replies', $content);
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

		$stream = $app->getStarred($username, array('count'=>$perPage,'before_id'=>$before_id,'since_id'=>$since_id, 'include_annotations'=>1));

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
		$stream = $app->getPublicPosts(array('count'=>$perPage,'before_id'=>$before_id,'since_id'=>$since_id,'include_annotations'=>1));

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

	// Geolocation parameters
	list($lat, $long) = explode(',', $_GET['location']);
	$loc = $_GET['location'];
	$radius = $_GET['radius'];

	$content = theme('search_form', $search_query);

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
	
	if ($search_query) 
	{
		$tl = dabr_search($search_query);//, $lat, $long, $radius);

		if ($search_query !== $_COOKIE['search_favourite']) 
		{
			$content .= '<form action="search/bookmark" method="post">
							<input type="hidden" name="query" value="'.$search_query.'" />
							<input type="submit" value="Save as default search" />
						</form>';
		}
		$content .= theme('timeline', $tl);
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
			$content = dabr_post_form("#".$hashtag);//theme('status_form', '#'.$hashtag.' ');
			//	Track how long the API call took
			global $api_time;
			$api_start = microtime(1);

			//	Search for hashtags
			$stream = $app->searchHashtags($hashtag, 
											array('count'=>$perPage,
												'before_id'=>$before_id,
												'since_id'=>$since_id, 
												'include_annotations'=>1
											)
										);

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
	// Parameter checks
	//if (!is_numeric($id) || !$stream) return;

	// Check if the post exists in the timeline given
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
			$found_post = $app->getPost($id, array('include_annotations' => 1));
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
	// Get the ID of the post to which we are replying
	$in_reply_to_id = (string) $query[3];

	$app = new EZAppDotNet();

	// check that the user is signed in
	if ($app->getSession()) 
	{
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);

		// get the current user as JSON
		//$user_id = $app->getIdByUsername($user_name);

		//	Get the user's name
		$user = $app->getUser("@".$user_name);

		//	Start building the status
		$status = "@" . $user_name . " ";

		// get the user stream early, so we can search for reply to all.
		$stream = $app->getUserPosts("@" . $user_name,
										array(
											'count'=>$perPage,
											'before_id'=>$before_id,
											'since_id'=>$since_id, 
											'include_annotations'=>1
										)
									);
		
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
		
			$content .= "<p>In reply to:<br />" . $reply_post['text'] . "</p>";
		}
		
		// Create the form where users can enter text
		$content .= dabr_post_form($status, $in_reply_to_id);//theme('status_form', $status, $in_reply_to_id);

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
		if ($query[0] == 'unstar') 
		{
			$app->unstarPost($id);
		} else {
			$app->starPost($id);
		}	
		dabr_refresh();
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
		$content = dabr_post_form();//theme('status_form');
		
		//	Track how long the API call took
		global $api_time;
		$api_start = microtime(1);
	
		// get the current user as JSON
		//$data = $app->getUser();

		//	get the stream
		$stream = $app->getUserStream(array('count'=>setting_fetch('perPage', 20),'before_id'=>$before_id,'since_id'=>$since_id,'include_annotations' => 1));
		
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
			//print_r($app->getPost($query[1]));
			//$thread = $app->getPost($query[1]);
			//$thread = $app->getPostReplies($query[1],array('count'=>200,'include_annotations' => 1));
			$thread = $app->getPost($query[1],array('include_annotations' => 1));
			echo "<pre>";
				print_r($thread);
			
			//	echo json_encode($thread);
			echo "/<pre>";
		}
	}
}


function dabr_hyper_page($query) 
{
	if (isset($query[1])) 
	{
		$app = new EZAppDotNet();
		if ($app->getSession()) 
		{
			$thread = $app->getPostReplies($query[1],array('count'=>200,'include_annotations' => 1));
			$thread[] = $app->getPost($query[1], array('include_annotations' => 1));

			$json_string = json_encode($thread);

			$data=json_decode($json_string,true);

			$array  = array();

			// first throw everything into an associative array for easy access
			$references = array();
			foreach ($data as $post) 
			{
			    $id = $post['id'];
			    $post['children'] = array();

			    $parent = $post['reply_to'];
			    $text = $post['text'];
			    $user = $post['user']['username'];
			    $name = $post['user']['name'];
			    $avatar = $post['user']['avatar_image']['url'];

			    $references[$id] = array(
			        "id" => $id,
			        "name" => htmlspecialchars($name),
			        "reply_to" => $parent, 
			        "data" => array(
			            "text" => htmlspecialchars($text), 
			            "user" => htmlspecialchars($user), 
			            "avatar" => $avatar
			            )
			        );

			}

			// now create the tree
			$tree = array();
			foreach ($references as &$post) 
			{
			    $id = $post['id'];
			    $parentId = $post['reply_to'];
			    // if it's a top level object, add it to the tree
			    if (!$parentId) 
			    {
			        $tree[] =& $references[$id];
			    }
			    // else add it to the parent
			    else 
			    {
			        $references[$parentId]['children'][] =& $post;
			    }
			    // avoid bad things by clearing the reference
			    unset($post);
			}

			//trim the []
			$output = json_encode($tree);
			$output = substr($output, 1, -1);
			// encode it
			header("Content-type: application/json");
			print $output;
		}
	}
}

function dabr_post_form($text = '', $in_reply_to_id = NULL) 
{

	$geoJS = '<script type="text/javascript">
				started = false;
				chkbox = document.getElementById("geoloc");
				if (navigator.geolocation) {
					geoStatus("Share my location");
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
					geoStatus("Share my <a href=\'http://maps.google.co.uk/?q=" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>location</a>");
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
			$title = "Reply on App.net";
		} else {
			$title = "Post to App.net";
		}
		
		return "<fieldset>
					<legend>
						<strong>&alpha;</strong> {$title}
					</legend>
				
					<form method=\"post\" action=\"update\">
						<textarea id=\"status\" name=\"status\" rows=\"4\" style=\"width:95%; max-width: 400px;\">$text</textarea>
						<div>
							<input name=\"in_reply_to_id\" value=\"$in_reply_to_id\" type=\"hidden\" />
							<input type=\"submit\" value=\"Post\" />
							<span id=\"remaining\">254</span> 
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

			$post = $app->getPost($id,array('include_annotations' => 1));

			//	Track how long the API call took
			$api_time += microtime(1) - $api_start;
		}
	}

	$text = "»@{$post['user']['username']}: {$post['text']}";
	$length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen($text);
	$from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));

	$content .= "<p>Native Repost:</p>
				<form action='repost-native/{$id}' method='post'>
					<input type='submit' value='Repost it!' />
				</form>
				<hr />";

	$content .= "<p>Edit Before Reposting:</p>
					<form action='update' method='post'>
						<input type='hidden' name='from' value='$from' />
						<input name='in_reply_to_id' value='$id' type='hidden' />
						<textarea name='status' style='width:90%; max-width: 400px;' rows='6' id='status'>$text</textarea>
						<br/>
						<input type='submit' value='Repost' />
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
		$app->repost($id);
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

	$bio = "";

	if($user['description']['text'] != "")
			$bio .= dabr_parse_tags($user['description']['text'], $user['description']['entities']) . "<br />";
		
	$bio .= "Joined on " . $date_joined . " - ";
	$bio .= pluralise('post', (int)$user['counts']['posts'], true) . " ";
	$bio .= "(~" . pluralise('post', $posts_per_day, true) . " per day). ";
	
	if ($follows_you && $you_follow)
	{
		$bio .= "YOU ARE BEST FRIENDS! ";			
	}
	else if ($follows_you)
	{
		$bio .= "Follows you. ";
	}
	else if ($you_follow)
	{
		$bio .= "You are following. ";
	}

	if ($you_muted)
	{
		$bio .= " Shhh! Muted. ";
	}

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
		$actions .= "<a href='followers/{$username}'>" . pluralise('follower', $user['counts']['followers'], true) . "</a>";
		$actions .= " | <a href='friends/{$username}'>" . pluralise('friend', $user['counts']['following'], true) . "</a>";
		$actions .= " | <a href='starred/{$username}'>Starred Posts</a>";
		
		//	User cannot perform certain actions on herself
		if (strtolower($username) !== strtolower(user_current_username())) 
		{
		
			if ($you_follow == false) {
				$actions .= " | <a href='follow/{$id}'>Follow</a>";
			}
			else {
				$actions .= " | <a href='unfollow/{$id}'>Unfollow</a>";
			}

			if ($you_muted)
			{
				$actions .= " | <a href='confirm/unmute/{$username}'>Unmute</a>";
			}else
			{
				$actions .= " | <a href='confirm/mute/{$username}'>Mute</a>";
			}
			
			$actions .= " | <a href='confirm/spam/{$username}/{$id}'>Report Spam</a>";
		}
		
		$actions .= " | <a href='search?query=%40{$username}'>Search @{$username}</a>";
	}else
	{
		$actions .= pluralise('follower', $user['counts']['followers'], true);
		$actions .= ". " . pluralise('friend', $user['counts']['following'], true) . ".";
	}	
	return $actions;
}

function theme_user_header($user) 
{
	$name = $user['name']; //theme('full_name', $user);
	$username = $user['username'];
	$id = $user['id'];

	$full_avatar = $user['avatar_image']['url']; //theme_get_full_avatar($user);
	
	$out = "<div class='profile'>";
	$out .= "	<span class='avatar'>";
	$out .=			theme('avatar', $full_avatar, $name);
	$out .= "	</span>";
	$out .= "	<span class='status shift'>
					<b><a href=\"user/$username/$id\">$name (@$username)</a></b>
					<br />";
	$out .= "		<span class='about'>";
	$out .= "			Bio: " . dabr_user_bio($user)."<br />";
	$out .= "		</span>
				</span>";
	$out .= "	<div class='features'>";
	$out .= 		dabr_user_actions($user);
	$out .= "	</div>
			</div>";
	return $out;
}

function theme_avatar($url, $name = "") 
{
	$size = 48;
	return "<img src=\"$url?w=$size\" height='$size' width='$size' alt='$name' />";
}

function theme_status_time_link($status, $is_link = true) {
	$time = strtotime($status['created_at']);
	if ($time > 0) {
		if (dabr_date('dmy') == dabr_date('dmy', $time) && !setting_fetch('timestamp')) {
			$out = format_interval(time() - $time, 1). ' ago';
		} else {
			$out = dabr_date('H:i', $time);
		}
	} else {
		$out = $status['created_at'];
	}
	if ($is_link)
		$out = "<a href='status/{$status['id']}' class='time'>$out</a>";
	return $out;
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

function theme_timeline($feed)
{
	if (count($feed) == 0) return theme('no_posts');
	if (count($feed) < 2) { 
		$hide_pagination = true;
	}
	$rows = array();
	$page = menu_current_page();
	$date_heading = false;
	$first=0;

	// Add the hyperlinks *BEFORE* adding images
	foreach ($feed as &$status)
	{
		if ($status['repost_of'])
		{
			$status['html'] = dabr_parse_tags($status['repost_of']['text'], $status['repost_of']['entities']);
		}
		else {
			$status['html'] = dabr_parse_tags($status['text'], $status['entities']);
		}
	}
	unset($status);
	
	// Only embed images in suitable browsers
	if (!in_array(setting_fetch('browser'), array('text', 'worksafe')))
	{
		if (EMBEDLY_KEY !== '')
		{
			embedly_embed_thumbnails($feed);
		}
	}

	foreach ($feed as $status)
	{
		if (!$status['is_deleted'])	//	Don't display deleted posts
		{
			$time = strtotime($status['created_at']);

			if ($time > 0)
			{
				$date = dabr_date('l jS F Y', strtotime($status['created_at']));
				if ($date_heading !== $date)
				{
					$date_heading = $date;
					$rows[] = array('data'  => array($date), 'class' => 'date');
				}
			}
			else
			{
				$date = $status['created_at'];
			}

			//	Post's text has already been manipulated
			$text = $status['html'];

			//	Deal with reposts.
			if ($status['repost_of'])
			{
				$repost_of = $status['repost_of'];
				$repost_id = $status['id'];
				$reposter_username = $status['user']['username'];
				$reposter_name = $status['user']['name'];
				$reposter_avatar = $status['user']['avatar_image']['url'];
				
				$status = $repost_of;

				$status['dabr_repost_of'] = true;
				$status['dabr_repost_id'] = $repost_id;
				$status['dabr_repost_name'] = $reposter_name;
				$status['dabr_repost_username'] = $reposter_username;
				$status['dabr_repost_avatar'] = $reposter_avatar;
			}

			$actions = theme('action_icons', $status);
			$link = theme('status_time_link', $status, true);

			$avatar = theme('avatar', $status['user']['avatar_image']['url'], $status['user']['name']);
			$source = "<a href=\"{$status['source']['link']}\">{$status['source']['name']}</a>";

			$conversation = "";
			if ($status['reply_to'])
			{
				$conversation .= "| <a href='status/{$status['reply_to']}'>View Conversation</a>";
			}

			if ($status['annotations'])
			{
				$conversation .= " | <a href='raw/{$status['id']}'>View Annotations</a>";
			}

			$repost_info ="";
			if ($status['dabr_repost_of'])
			{
				$repost_info .= "<a href='user/{$status['dabr_repost_username']}'>
					<img src=\"{$status['dabr_repost_avatar']}?w=25\" />Reposted by {$status['dabr_repost_name']}
				</a>
				<br>";
			}

			$html = "<b><a href='user/{$status['user']['username']}'>{$status['user']['name']}</a></b> $actions $link 
					<br />
					{$text}
					<br />
					<small>$repost_info Sent via $source $conversation</small>";
			unset($row);
			$class = 'status';
			
			if ($page != 'user' || $status['dabr_repost_of'])
			{
				$row[] = array('data' => $avatar, 'class' => 'avatar');
				$class .= ' shift';
			}
			
			$row[] = array('data' => $html, 'class' => $class);

			$class = 'tweet';
			if ($page != 'replies' && dabr_is_reply($status))
			{
				$class .= ' reply';
			}
			$row = array('data' => $row, 'class' => $class);

			$rows[] = $row;
		}
	}
	$content = theme('table', array(), $rows, array('class' => 'timeline'));
/*
	if ($page != '' && !$hide_pagination)
	{
		$content .= theme('pagination');
	}
	else if (!$hide_pagination)  // Don't show pagination if there's only one item
	{
*/		//	Get the IDs of the first and last statuses
	if (!$hide_pagination)
	{
		$last = end($feed);
		$first = reset($feed);

		$content .= theme_pagination($last['id'],$first['id']);//'<p>'.implode(' | ', $links).'</p>';
	}

	return $content;
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

function theme_users($feed, $nextPageURL=null) 
{
	$rows = array();
	if (count($feed) == 0 || $feed == '[]') return '<p>No users to display.</p>';

	foreach ($feed as $user) {

		$name = $user['name'];
		$username = $user['username'];
		$follows_you = $user['follows_you'];
		$you_follow = $user['you_follow'];
		$you_muted = $user['you_muted'];

		$posts_per_day = dabr_posts_per_day($user);

		$raw_date_joined = strtotime($user['created_at']);
		$date_joined = date('jS M Y', $raw_date_joined);
	
		$content = "<a href=\"user/$username/$id\">$name (@$username)</a>
					<br />
					<span class='about'>";

		if($user['description']['text'] != "")
			$content .= "Bio: " . dabr_user_bio($user);

		$content .= dabr_user_actions($user,false);		

		$content .= 	"<br />";
		$content .= "</span>";

		$rows[] = 	array(
						'data' => 
							array(
								array(
										'data' => theme('avatar',	$user['avatar_image']['url'], $name), 
										'class' => 'avatar'
									),
								array(
									'data' => $content, 
									'class' => 'status shift')
								),
							'class' => 'tweet'
					);
	}

	$content = theme('table', array(), $rows, array('class' => 'followers'));
	if ($nextPageURL)
		$content .= "<a href='{$nextPageURL}'>Next</a>";
	return $content;
}

function theme_full_name($user) {
	$name = "<a href='user/{$user->screen_name}'>{$user->screen_name}</a>";
	//THIS IF STATEMENT IS RETURNING FALSE EVERYTIME ?!?
	//if ($user->name && $user->name != $user->screen_name) {
	if($user->name != "") {
		$name .= " ({$user->name})";
	}
	return $name;
}

function theme_get_avatar($object) 
{
	$avatar_url = $object['avatar_image']['url'];
	return $avatar_url . "?w=48";
}

function theme_get_full_avatar($object) {
	return $object['avatar_image']['url'];
}

function theme_no_posts() {
	return '<p>No posts to display.</p>';
}

function theme_search_form($query) {
	$query = stripslashes(htmlentities($query,ENT_QUOTES,"UTF-8"));
	return '
	<form action="search" method="get"><input name="query" value="'. $query .'" />
		<input type="submit" value="Search" />
	</form>';
}

function theme_external_link($url, $content = null) {
	//Long URL functionality.  Also uncomment function long_url($shortURL)
	if (!$content)
	{
		//Used to wordwrap long URLs
		//return "<a href='$url' target='_blank'>". wordwrap(long_url($url), 64, "\n", true) ."</a>";
		return "<a href='$url' target='" . get_target() . "'>". long_url($url) ."</a>";
	}
	else
	{
		return "<a href='$url' target='" . get_target() . "'>$content</a>";
	}

}

function theme_pagination($before_id=null,$since_id=null)
{

	if ($since_id || $before_id)  // Don't show pagination if there's only one item
	{
		$links[] = "<a href='{$_GET['q']}?before_id=$before_id'>Older</a>";
		$links[] = "<a href='{$_GET['q']}?since_id=$since_id'>Newer</a>";
		
		return '<p>'.implode(' | ', $links).'</p>';
	}
}


function theme_action_icons($status) 
{
	$from = $status['user']['username'];

	if (setting_fetch('browser') == "bigtouch")
	{
		$L = "L";
	}

	$actions = array();

	//	Reply
	$actions[] = theme('action_icon', "status/{$status['id']}", "images/reply{$L}.png", '@');

	//	Re-post	
	if ($status['dabr_repost_of'])
	{
		$actions[] = theme('action_icon', "repost/{$status['id']}", "images/retweeted{$L}.png", 'RT');
	}
	else {
		$actions[] = theme('action_icon', "repost/{$status['id']}", "images/retweet{$L}.png", 'RT');
	}
	if ($status['num_reposts']>0)
	{
		$actions[] = "<a href=reposters/{$status['id']}>{$status['num_reposts']}</a>";	
	}

	//	Star
	if ($status['you_starred']) 
	{
		$actions[] = theme('action_icon', "unstar/{$status['id']}", "images/star{$L}.png", 'STARRED');
	} else {
		$actions[] = theme('action_icon', "star/{$status['id']}", "images/star_grey{$L}.png", 'UNSTAR');
	}

	if ($status['num_stars']>0)
	{
		$actions[] = "<a href=stars/{$status['id']}>{$status['num_stars']}</a>";	
	}

	// Delete
	if (user_is_current_user($from))
	{
		$actions[] = theme('action_icon', "confirm/delete/{$status['id']}", "images/trash{$L}.png", 'DEL');
	}

	//	Delete the Repost
	if (user_is_current_user($status['dabr_repost_username']))
	{
		$actions[] = theme('action_icon', "confirm/delete/{$status['dabr_repost_id']}", "images/trash{$L}.png", 'DEL');	
	}

	//	Map
	if ($status['annotations'] > 0)
	{
		foreach($status['annotations'] as $annotation) 
		{
			if ($annotation['type'] == "net.app.core.geolocation")
			{
				$lat = $annotation['value']['latitude'];
				$long = $annotation['value']['longitude'];
				$actions[] = theme('action_icon', "https://maps.google.com/maps?q={$lat},{$long}", "images/map{$L}.png", 'MAP');
			}
		}
	}

	//	Search for @ to a user
	$actions[] = theme('action_icon',"search?query=%40{$from}","images/q{$L}.png",'?');

	return implode(' ', $actions);
}

function theme_action_icon($url, $image_url, $text) {
	// alt attribute left off to reduce bandwidth by about 720 bytes per page
	if ($text == 'MAP')
	{
		return "<a href='$url' target='" . get_target() . "'><img src='$image_url' alt='$text' /></a>";
	}

	return "<a href='$url'><img src='$image_url' alt='$text' /></a>";
}

function pluralise($word, $count, $show = FALSE) {
	if($show) $word = number_format($count) . " {$word}";
	return $word . (($count != 1) ? 's' : '');
}

function is_64bit() {
	$int = "9223372036854775807";
	$int = intval($int);
	return ($int == 9223372036854775807);
}
?>
