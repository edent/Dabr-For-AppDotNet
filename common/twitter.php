<?php

require 'Autolink.php';
require 'Extractor.php';
require 'Embedly.php';
require 'Emoticons.php';
require_once 'menu.php';
		
menu_register(array(
	'' => array(
		'callback' => 'twitter_home_page',
	),
	'status' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_status_page',
	),
	'update' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_update',
	),
	'twitter-retweet' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_retweet',
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
/*	'directs' => array(
		'security' => true,
		'callback' => 'twitter_directs_page',
		'accesskey' => '2',
	),
*/	'search' => array(
		'security' => true,
		'callback' => 'dabr_search_page',
	),
	'user' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_user_page',
	),
	'follow' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_follow_page',
	),
	'unfollow' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_follow_page',
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
	'block' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_block_page',
	),
	'unblock' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_block_page',
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
	'spam' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_spam_page',
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
		'callback' => 'twitter_delete_page',
	),
/*	'deleteDM' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'twitter_deleteDM_page',
	),
*/	'retweet' => array(
		'hidden' => true,
		'security' => true,
		'callback' => 'dabr_retweet_page',//'twitter_retweet_page',
	),
	'hash' => array(
		'security' => true,
		'hidden' => true,
		'callback' => 'dabr_hashtag_page',
	),
/*	'Upload Picture' => array(
		'security' => true,
		'callback' => 'twitter_media_page',
	),
	'Trends' => array(
		'security' => true,
		'callback' => 'twitter_trends_page',
	),
*//*	'retweets' => array(
		'security' => true,
		'callback' => 'twitter_retweets_page',
	),
	'retweeted_by' => array(
		'security' => true,
		'hidden' => true,
		'callback' => 'twitter_retweeters_page',
	),
	'Edit Profile' => array(
		'security' => true,
		'callback' => 'twitter_profile_page',
	),
*/	'raw' => array(
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

//	Edit User Profile
function twitter_profile_page() {
	// process form data
	if ($_POST['name']){

		// post profile update
		$post_data = array(
			"name"			=> stripslashes($_POST['name']),
			"url"				=> stripslashes($_POST['url']),
			"location"		=> stripslashes($_POST['location']),
			"description"	=> stripslashes($_POST['description']),
		);

		$url = API_URL."account/update_profile.json";
		$user = twitter_process($url, $post_data);
		$content = "<h2>Profile Updated</h2>";
	} 
	
	//	http://api.twitter.com/1/account/update_profile_image.format 
	if ($_FILES['image']['tmp_name']){	
		require 'tmhOAuth.php';
		
		list($oauth_token, $oauth_token_secret) = explode('|', $GLOBALS['user']['password']);
		
		$tmhOAuth = new tmhOAuth(array(
			'consumer_key'    => OAUTH_CONSUMER_KEY,
			'consumer_secret' => OAUTH_CONSUMER_SECRET,
			'user_token'      => $oauth_token,
			'user_secret'     => $oauth_token_secret,
		));

		// note the type and filename are set here as well
		$params = array(
			'image' => "@{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}",
		);

		$code = $tmhOAuth->request('POST', 
											$tmhOAuth->url("1/account/update_profile_image"),
											$params,
											true, // use auth
											true // multipart
		);


		if ($code == 200) {
			$content = "<h2>Avatar Updated</h2>";			
		} else {
			$content = "Damn! Something went wrong. Sorry :-("  
				."<br /> code="	. $code
				."<br /> status="	. $status
				."<br /> image="	. $image
				//."<br /> response=<pre>"
				//. print_r($tmhOAuth->response['response'], TRUE)
				. "</pre><br /> info=<pre>"
				. print_r($tmhOAuth->response['info'], TRUE)
				. "</pre><br /> code=<pre>"
				. print_r($tmhOAuth->response['code'], TRUE) . "</pre>";
		}
	}
	
	// Twitter API is really slow!  If there's no delay, the old profile is returned.
	//	Wait for 5 seconds before getting the user's information, which seems to be sufficient
	sleep(5);

	// retrieve profile information
	$user = twitter_user_info(user_current_username());

	$content .= theme('user_header', $user);
	$content .= theme('profile_form', $user);

	theme('page', "Edit Profile", $content);
}

function theme_profile_form($user){
	// Profile form
	$out .= "
				<form name='profile' action='Edit Profile' method='post' enctype='multipart/form-data'>
					<hr />Name:			<input name='name' maxlength='20' value='"						. htmlspecialchars($user->name, ENT_QUOTES) ."' />
					<br />Avatar:		<img src='".theme_get_avatar($user)."' /> <input type='file' name='image' />
					<br />Bio:			<input name='description' size=40 maxlength='160' value='"	. htmlspecialchars($user->description, ENT_QUOTES) ."' />
					<br />Link:			<input name='url' maxlength='100' size=40 value='"				. htmlspecialchars($user->url, ENT_QUOTES) ."' />
					<br />Location:	<input name='location' maxlength='30' value='"					. htmlspecialchars($user->location, ENT_QUOTES) ."' />
					<br /><input type='submit' value='Update Profile' />
				</form>";

	return $out;
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


function friendship_exists($user_a) {
	$request = API_URL.'friendships/show.json?target_screen_name=' . $user_a;
	$following = twitter_process($request);

	if ($following->relationship->target->following == 1) {
		return true;
	} else {
		return false;
	}
}

function friendship($user_a)
{
	$request = API_URL.'friendships/show.json?target_screen_name=' . $user_a;
	return twitter_process($request);
}


function twitter_block_exists($query)
{
	//http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-blocks-blocking-ids
	//Get an array of all ids the authenticated user is blocking
	$request = API_URL.'blocks/blocking/ids.json';
	$blocked = (array) twitter_process($request);

	//bool in_array  ( mixed $needle  , array $haystack  [, bool $strict  ] )
	//If the authenticate user has blocked $query it will appear in the array
	return in_array($query,$blocked);
}

function twitter_trends_page($query)
{
	$woeid = $_GET['woeid'];
	if($woeid == '') $woeid = '1'; //worldwide
	
	//fetch "local" names
	$request = API_URL.'trends/available.json';
	$local = twitter_process($request);
	$header = '<form method="get" action="trends"><select name="woeid">';
	$header .= '<option value="1"' . (($woeid == 1) ? ' selected="selected"' : '') . '>Worldwide</option>';
	
	//sort the output, going for Country with Towns as children
	foreach($local as $key => $row) {
		$c[$key] = $row->country;
		$t[$key] = $row->placeType->code;
		$n[$key] = $row->name;
	}
	array_multisort($c, SORT_ASC, $t, SORT_DESC, $n, SORT_ASC, $local);
	
	foreach($local as $l) {
		if($l->woeid != 1) {
			$n = $l->name;
			if($l->placeType->code != 12) $n = '-' . $n;
			$header .= '<option value="' . $l->woeid . '"' . (($l->woeid == $woeid) ? ' selected="selected"' : '') . '>' . $n . '</option>';
		}
	}
	$header .= '</select> <input type="submit" value="Go" /></form>';
	
	$request = API_URL.'trends/' . $woeid . '.json';
	$trends = twitter_process($request);
	$search_url = 'search?query=';
	foreach($trends[0]->trends as $trend) {
		$row = array('<strong><a href="' . str_replace('http://twitter.com/search/', $search_url, $trend->url) . '">' . $trend->name . '</a></strong>');
		$rows[] = array('data' => $row,  'class' => 'tweet');
	}
	$headers = array($header);
	$content = theme('table', $headers, $rows, array('class' => 'timeline'));
	theme('page', 'Trends', $content);
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

function twitter_media_page($query) 
{
	$content = "";
	$status = stripslashes($_POST['message']);
	
	if ($_POST['message'] && $_FILES['image']['tmp_name']) 
	{
		require 'tmhOAuth.php';
		
		// Geolocation parameters
		list($lat, $long) = explode(',', $_POST['location']);
		if (is_numeric($lat) && is_numeric($long)) {
			$post_data['lat'] = $lat;
			$post_data['long'] = $long;	
		}
		
		list($oauth_token, $oauth_token_secret) = explode('|', $GLOBALS['user']['password']);
		
		$tmhOAuth = new tmhOAuth(array(
			'consumer_key'    => OAUTH_CONSUMER_KEY,
			'consumer_secret' => OAUTH_CONSUMER_SECRET,
			'user_token'      => $oauth_token,
			'user_secret'     => $oauth_token_secret,
		));

		$image = "{$_FILES['image']['tmp_name']};type={$_FILES['image']['type']};filename={$_FILES['image']['name']}";

		$code = $tmhOAuth->request('POST', 'https://upload.twitter.com/1/statuses/update_with_media.json',
											  array(
												 'media[]'  => "@{$image}",
												 'status'   => " " . $status, //A space is needed because twitter b0rks if first char is an @
												 'lat'		=> $lat,
												 'long'		=> $long,
											  ),
											  true, // use auth
											  true  // multipart
										);

		if ($code == 200) {
			$json = json_decode($tmhOAuth->response['response']);
			
			if ($_SERVER['HTTPS'] == "on") {
				$image_url = $json->entities->media[0]->media_url_https;
			}
			else {
				$image_url = $json->entities->media[0]->media_url;
			}

			$text = $json->text;
			
			$content = "<p>Upload success. Image posted to Twitter.</p>
							<p><img src=\"" . IMAGE_PROXY_URL . "x50/" . $image_url . "\" alt='' /></p>
							<p>". twitter_parse_tags($text) . "</p>";
			
		} else {
			$content = "Damn! Something went wrong. Sorry :-("  
				."<br /> code=" . $code
				."<br /> status=" . $status
				."<br /> image=" . $image
				."<br /> response=<pre>"
				. print_r($tmhOAuth->response['response'], TRUE)
				. "</pre><br /> info=<pre>"
				. print_r($tmhOAuth->response['info'], TRUE)
				. "</pre><br /> code=<pre>"
				. print_r($tmhOAuth->response['code'], TRUE) . "</pre>";
		}
	}
	
	if($_POST) {
		if (!$_POST['message']) {
			$content .= "<p>Please enter a message to go with your image.</p>";
		}

		if (!$_FILES['image']['tmp_name']) {
			$content .= "<p>Please select an image to upload.</p>";
		}
	}
	
	$content .=	"<form method='post' action='Upload Picture' enctype='multipart/form-data'>
						Image <input type='file' name='image' /><br />
						Message (optional):<br />
						<textarea name='message' style='width:90%; max-width: 400px;' rows='3' id='message'>" . $status . "</textarea><br>
						<input type='submit' value='Send' />
						<span id='remaining'>119</span>";
	$content .= '	<span id="geo" style="display: none;">
							<input onclick="goGeo()" type="checkbox" id="geoloc" name="location" />
							<label for="geoloc" id="lblGeo"></label>
						</span>
						<script type="text/javascript">
							started = false;
							chkbox = document.getElementById("geoloc");
							if (navigator.geolocation) {
								geoStatus("Tweet my location");
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
								geoStatus("Tweet my <a href=\'https://maps.google.com/maps?q=" + position.coords.latitude + "," + position.coords.longitude + "\' target=' . get_target() . '>location</a>");
								chkbox.value = position.coords.latitude + "," + position.coords.longitude;
							}
					</script>
					</form>';
	$content .= js_counter("message", "119");

	return theme('page', 'Picture Upload', $content);
}

function twitter_process($url, $post_data = false)
{
	if ($post_data === true)
	{
		$post_data = array();
	}

	$status = $post_data['status'];

//	if (user_type() == 'oauth' && ( strpos($url, '/twitter.com') !== false || strpos($url, 'api.twitter.com') !== false || strpos($url, 'upload.twitter.com') !== false))
//	{
		user_oauth_sign($url, $post_data);
//	}
/*
	if (strpos($url, 'api.twitter.com') !== false && is_array($post_data))
	{
		// Passing $post_data as an array to twitter.com (non-oauth) causes an error :(
		$s = array();
		foreach ($post_data as $name => $value)
		$s[] = $name.'='.urlencode($value);
		$post_data = implode('&', $s);
	}
*/
	$api_start = microtime(1);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);

	if($post_data !== false && !$_GET['page'])
	{
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
	}

	//from  http://github.com/abraham/twitteroauth/blob/master/twitteroauth/twitteroauth.php
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
	curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

	$response = curl_exec($ch);
	$response_info=curl_getinfo($ch);
	$erno = curl_errno($ch);
	$er = curl_error($ch);
	curl_close($ch);

	global $api_time;
	global $rate_limit;
	/*
	//	Split that headers and the body
	list($headers, $body) = explode("\n\n", $response, 2);

	//	Place the headers into an array
	$headers = explode("\n", $headers);
	$headers_array;
	foreach ($headers as $header) {
		list($key, $value) = explode(':', $header, 2);
		$headers_array[$key] = $value;
	}
	
	//	Not ever request is rate limited
	if ($headers_array['X-RateLimit-Limit']) {
		$current_time = time();
		$ratelimit_time = $headers_array['X-RateLimit-Reset'];
		 
		$time_until_reset = $ratelimit_time - $current_time;
	
		$minutes_until_reset = round($time_until_reset / 60);
	
		$currentdate = strtotime("now");
	
		$rate_limit = "Rate Limit: " . $headers_array['X-RateLimit-Remaining'] . " / " . $headers_array['X-RateLimit-Limit'] . " for the next $minutes_until_reset minutes";
	}
			 
	//	The body of the request is at the end of the headers
	$body = end($headers);
*/

	$body = $response;
	$api_time += microtime(1) - $api_start;

	switch( intval( $response_info['http_code'] ) )
	{
		case 200:
		case 201:
			$json = json_decode($body);
			if ($json)
			{
				return $json;
			}
			return $body;
		case 401:
			user_logout();
			theme('error', "<p>Error: Login credentials incorrect.</p><p>{$response_info['http_code']}: {$result}</p><hr><p>$url</p>");
		case 0:
			$result = $erno . ":" . $er . "<br />" ;
			/*
			 foreach ($response_info as $key => $value)
			 {
				$result .= "Key: $key; Value: $value<br />";
				}
				*/
			theme('error', '<h2>Twitter timed out</h2><p>Dabr gave up on waiting for Twitter to respond. They\'re probably overloaded right now, try again in a minute. <br />'. $result . ' </p>');
		default:
			$result = json_decode($body);
			$result = $result->error ? $result->error : $body;
			if (strlen($result) > 500)
			{
				$result = 'Something broke on Twitter\'s end.' ;
			/*
			foreach ($response_info as $key => $value)
			{
				$result .= "Key: $key; Value: $value<br />";
			}
			*/	
			}
			else if ($result == "Status is over 256 characters.") {
				theme('error', "<h2>Status was tooooooo loooooong!</h2><p>{$status}</p><hr>");	
				//theme('status_form',$status);
			}
			
			theme('error', "<h2>An error occured while calling the Twitter API</h2><p>{$response_info['http_code']}: {$result}</p><hr>");
	}
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

//	http://dev.twitter.com/pages/tweet_entities
function twitter_get_media($status) {
	if($status->entities->media) {
		
		$media_html = '';
		
		foreach($status->entities->media as $media) {
	
			if ($_SERVER['HTTPS'] == "on") {
				$image = $media->media_url_https;
			} else {
				$image = $media->media_url;
			}
			
			$link = $media->url;

			$width = $media->sizes->thumb->w;
			$height = $media->sizes->thumb->h;

			$media_html .= "<a href=\"" . IMAGE_PROXY_URL . $image . "\" target=\"" . get_target() . "\" >";
			$media_html .= 	"<img src=\"{$image}:thumb\" width=\"{$width}\" height=\"{$height}\" >";
			$media_html .= "</a>";
		}
	
		return $media_html . "<br/>";
	}	
}

function twitter_parse_tags($input, $entities = false) {

	$out = $input;

	//	Linebreaks.  Some clients insert \n for formatting.
	$out = nl2br($out);
	
	//	Hashtags and @ are internal links
	$out = Twitter_Autolink::create($out)->setExternal(false)->setNoFollow(false)->setTarget(false)->addLinksToHashtags();
	$out = Twitter_Autolink::create($out)->setExternal(false)->setNoFollow(false)->setTarget(false)->addLinksToUsernamesAndLists();
	
	//	URLs are external links
	$out = Twitter_Autolink::create($out)->setExternal(true)->setNoFollow(true)->setTarget(true)->addLinksToURLs();

	//Return the completed string
	return $out;
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

	if (!entities)	// Use the Autolink.
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

function twitter_status_page($query) {
	$id = (string) $query[1];
	if (is_numeric($id)) {
		//$request = API_URL."statuses/show/{$id}.json?include_entities=true";
		//$status = twitter_process($request);
		
		$app = new EZAppDotNet();	
		if ($app->getSession()) 
		{	
			//	Track how long the API call took
			global $api_time;
			$api_start = microtime(1);

			$status = $app->getPost($id);
			
			//	Track how long the API call took
			$api_time += microtime(1) - $api_start;

			//print_r($status);

			$text = $status['text'];	//	Grab the text before it gets formatted

			$content = theme('status', $status);

			//	Show a link to the original tweet		
			$username = $status['user']['username'];
			$content .= '<p><a href="https://alpha.app.net/' . $username . '/post/' . $id . '" target="'. get_target() . '">View orginal post on AppDotNet</a> | ';
			
			//	Translate the tweet
			$content .= '<a href="http://translate.google.com/m?hl=en&sl=auto&ie=UTF-8&q=' . urlencode($text) . '" target="'. get_target() . '">Translate this post</a></p>';
			
			if (!$status->user->protected) {
				$thread = twitter_thread_timeline($id);
			}
			if ($thread) {
				$content .= '<p>And the experimental conversation view...</p>'.theme('timeline', $thread);
				$content .= "<p>Don't like the thread order? Go to <a href='settings'>settings</a> to reverse it. Either way - the dates/times are not always accurate.</p>";
			}
			
			theme('page', "Status $id", $content);
		}
		else{
		}
	}
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

			$content = theme('status', $post);

			//	Show a link to the original tweet		
			$username = $post['user']['username'];
			$content .= '<p><a href="https://alpha.app.net/' . $username . '/post/' . $id . '" target="'. get_target() . '">View orginal post on AppDotNet</a> | ';
			
			//	Translate the tweet
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

function twitter_thread_timeline($thread_id) {
	$request = "https://search.twitter.com/search/thread/{$thread_id}";
	//$tl = twitter_standard_timeline(twitter_fetch($request), 'thread');
	//return $tl;
}

function twitter_retweet_page($query) {
	$id = (string) $query[1];
	if (is_numeric($id)) {
		$request = API_URL."statuses/show/{$id}.json?include_entities=true";
		$tl = twitter_process($request);
		$content = theme('retweet', $tl);
		theme('page', 'Retweet', $content);
	}
}

function twitter_refresh($page = NULL) {
	if (isset($page)) {
		$page = BASE_URL . $page;
	} else {
		$page = $_SERVER['HTTP_REFERER'];
	}
	header('Location: '. $page);
	exit();
}

function twitter_delete_page($query) {
	dabr_ensure_post_action();

	$id = (string) $query[1];
	if (is_numeric($id)) {
		/*$request = API_URL."statuses/destroy/{$id}.json?page=".intval($_GET['page']);
		$tl = twitter_process($request, true);
		*/

		$app = new EZAppDotNet();

		// check that the user is signed in
		if ($app->getSession()) 
		{
			$deleted = $app->deletePost($id);
		}


		twitter_refresh('user/'.user_current_username());
	}
}

function twitter_deleteDM_page($query) {
	//Deletes a DM
	twitter_ensure_post_action();

	$id = (string) $query[1];
	if (is_numeric($id)) {
		$request = API_URL."direct_messages/destroy/$id.json";
		twitter_process($request, true);
		twitter_refresh('directs/');
	}
}

function twitter_ensure_post_action() {
	// This function is used to make sure the user submitted their action as an HTTP POST request
	// It slightly increases security for actions such as Delete, Block and Spam
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		die('Error: Invalid HTTP request method for this action.');
	}
}

function dabr_ensure_post_action() {
	// This function is used to make sure the user submitted their action as an HTTP POST request
	// It slightly increases security for actions such as Delete, Block and Spam
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		die('Error: Invalid HTTP request method for this action.');
	}
}

function twitter_follow_page($query) {

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
			twitter_refresh('friends');
		}
	}
}

function twitter_block_page($query) {
	dabr_ensure_post_action();
	$user = $query[1];
	if ($user) {
		if($query[0] == 'block'){
			$request = API_URL."blocks/create/create.json?screen_name={$user}";
			twitter_process($request, true);
	                twitter_refresh("confirmed/block/{$user}");
		} else {
			$request = API_URL."blocks/destroy/destroy.json?screen_name={$user}";
			twitter_process($request, true);
	                twitter_refresh("confirmed/unblock/{$user}");
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
				twitter_refresh("confirmed/mute/{$username}");
			} else 
			{
				$app->unmuteUser($username);
				twitter_refresh("confirmed/unmute/{$username}");
			}
		}
	}
}


function twitter_spam_page($query)
{
	//http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-report_spam
	//We need to post this data
	twitter_ensure_post_action();
	$user = $query[1];

	//The data we need to post
	$post_data = array("screen_name" => $user);

	$request = API_URL."report_spam.json";
	twitter_process($request, $post_data);

	//Where should we return the user to?  Back to the user
	twitter_refresh("confirmed/spam/{$user}");
}


function dabr_confirmation_page($query)
{
	// the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
	$action = $query[1];
	$target = $query[2];	//The name of the user we are doing this action on
//echo $action . " " . $target;
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
									<img src='images/dabr.png' width='48' height='48' />
								</span>
								<span class='status shift'>
									Shhhhhh $target! You are now <strong>muted</strong>.
								</span>
							</p>";
				break;
			case 'unmute':
				$content  = "<p>
								<span class='avatar'>
									<img src='images/dabr.png' width='48' height='48' />
								</span>
								<span class='status shift'>
									Hello again $target - you have been <strong>unmuted</strong>.
								</span>
							</p>";
				break;
			case 'spam':
				$content = "<p><span class='avatar'><img src='images/dabr.png' width='48' height='48' /></span><span class='status shift'>Yum! Yum! Yum! Delicious spam! Goodbye @$target.</span></p>";
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


function twitter_update() {
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



	

		/*

		$request = API_URL.'statuses/update.json';
		$post_data = array('source' => 'dabr', 'status' => $status);
		$in_reply_to_id = (string) $_POST['in_reply_to_id'];
		if (is_numeric($in_reply_to_id)) {
			$post_data['in_reply_to_status_id'] = $in_reply_to_id;
		}
		// Geolocation parameters
		list($lat, $long) = explode(',', $_POST['location']);
		$geo = 'N';
		if (is_numeric($lat) && is_numeric($long)) {
			$geo = 'Y';
			$post_data['lat'] = $lat;
			$post_data['long'] = $long;
			// $post_data['display_coordinates'] = 'false';
	  			
		}
		setcookie_year('geo', $geo);
		$b = twitter_process($request, $post_data);
	}
	*/
	twitter_refresh($_POST['from'] ? $_POST['from'] : '');
	
}

function twitter_get_place($lat, $long) {
	//	http://dev.twitter.com/doc/get/geo/reverse_geocode
	//	http://api.twitter.com/version/geo/reverse_geocode.format 
	
	//	This will look up a place ID based on lat / long.
	//	Not needed (Twitter include it automagically
	//	Left in just incase we ever need it...
	$request = API_URL.'geo/reverse_geocode.json';
	$request .= '?lat='.$lat.'&long='.$long.'&max_results=1';
	
	$locations = twitter_process($request);
	$places = $locations->result->places;
	foreach($places as $place)
	{
		if ($place->id) 
		{
			return $place->id;
		}
	}
	return false;
}

function twitter_retweet($query) {
	twitter_ensure_post_action();
	$id = $query[1];
	if (is_numeric($id)) {
		$request = API_URL.'statuses/retweet/'.$id.'.xml';
		twitter_process($request, true);
	}
	twitter_refresh($_POST['from'] ? $_POST['from'] : '');
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
		$url = $app->getAuthUrl();
		$content = '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
		$content .= about_page();
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
		$url = $app->getAuthUrl();
		$content = '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
		$content .= about_page();
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
		$url = $app->getAuthUrl();
		$content = '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
		$content .= about_page();
	}

}


function twitter_retweets_page() {
	$perPage = setting_fetch('perPage', 20);
	$request = API_URL.'statuses/retweets_of_me.json?page='.intval($_GET['page']).'&include_entities=true&count='.$perPage;
	$tl = twitter_process($request);
	$tl = twitter_standard_timeline($tl, 'retweets');
	$content = theme('status_form');
	$content .= theme('timeline',$tl);
	theme('page', 'Retweets', $content);
}

function twitter_directs_page($query) {
	$perPage = setting_fetch('perPage', 20);
	
	$action = strtolower(trim($query[1]));
	switch ($action) {
		case 'create':
			$to = $query[2];
			$content = theme('directs_form', $to);
			theme('page', 'Create DM', $content);

		case 'send':
			twitter_ensure_post_action();
			$to = trim(stripslashes($_POST['to']));
			$message = trim(stripslashes($_POST['message']));
			$request = API_URL.'direct_messages/new.json';
			twitter_process($request, array('user' => $to, 'text' => $message));
			twitter_refresh('directs/sent');

		case 'sent':
			$request = API_URL.'direct_messages/sent.json?page='.intval($_GET['page']).'&include_entities=true&count='.$perPage;
			$tl = twitter_standard_timeline(twitter_process($request), 'directs_sent');
			$content = theme_directs_menu();
			$content .= theme('timeline', $tl);
			theme('page', 'DM Sent', $content);

		case 'inbox':
		default:
			$request = API_URL.'direct_messages.json?page='.intval($_GET['page']).'&include_entities=true&count='.$perPage;
			$tl = twitter_standard_timeline(twitter_process($request), 'directs_inbox');
			$content = theme_directs_menu();
			$content .= theme('timeline', $tl);
			theme('page', 'DM Inbox', $content);
	}
}

function theme_directs_menu() {
	return '<p><a href="directs/create">Create</a> | <a href="directs/inbox">Inbox</a> | <a href="directs/sent">Sent</a></p>';
}

function theme_directs_form($to) {
	if ($to) {

		if (friendship_exists($to) != 1)
		{
			$html_to = "<em>Warning</em> <b>" . $to . "</b> is not following you. You cannot send them a Direct Message :-(<br/>";
		}
		$html_to .= "Sending direct message to <b>$to</b><input name='to' value='$to' type='hidden'>";
	} else {
		$html_to .= "To: <input name='to'><br />Message:";
	}
	$content = "<form action='directs/send' method='post'>$html_to<br><textarea name='message' style='width:90%; max-width: 400px;' rows='3' id='message'></textarea><br><input type='submit' value='Send'><span id='remaining'>256</span></form>";
	$content .= js_counter("message");
	return $content;
}

function twitter_search_page() {

	$search_query = $_GET['query'];
	
	// Geolocation parameters
	list($lat, $long) = explode(',', $_GET['location']);
	$loc = $_GET['location'];
	$radius = $_GET['radius'];
	//echo "the lat = $lat, and long = $long, and $loc";
	$content = theme('search_form', $search_query);
	if (isset($_POST['query'])) {
		$duration = time() + (3600 * 24 * 365);
		setcookie('search_favourite', $_POST['query'], $duration, '/');
		twitter_refresh('search');
	}
	if (!isset($search_query) && array_key_exists('search_favourite', $_COOKIE)) {
		$search_query = $_COOKIE['search_favourite'];
		}
	if ($search_query) {
		$tl = twitter_search($search_query, $lat, $long, $radius);
		if ($search_query !== $_COOKIE['search_favourite']) {
			$content .= '<form action="search/bookmark" method="post"><input type="hidden" name="query" value="'.$search_query.'" /><input type="submit" value="Save as default search" /></form>';
		}
		$content .= theme('timeline', $tl);
	}

	theme('page', 'Search', $content);
}

function twitter_search($search_query, $lat = NULL, $long = NULL, $radius = NULL) {
	$perPage = setting_fetch('perPage', 20);
	$page = (int) $_GET['page'];
	if ($page == 0) $page = 1;
	
	$request = 'https://search.twitter.com/search.json?rpp='.$perPage.'&result_type=recent&q=' . urlencode($search_query).'&page='.$page.'&include_entities=true';
	
	if ($lat && $long)
	{
		$request .= "&geocode=$lat,$long,";
		
		if ($radius)
		{
			$request .="$radius";
		} else
		{
			$request .="1km";
		}

	}
	
	$tl = twitter_process($request);
	$tl = twitter_standard_timeline($tl->results, 'search');
	return $tl;
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
		twitter_refresh('search');
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
		$url = $app->getAuthUrl();
		$content = '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
		$content .= about_page();
	}

	theme('page', $page_title, $content);
}

function twitter_find_tweet_in_timeline($tweet_id, $tl) {
	// Parameter checks
	if (!is_numeric($tweet_id) || !$tl) return;

	// Check if the tweet exists in the timeline given
	if (array_key_exists($tweet_id, $tl)) {
		// Found the tweet
		$tweet = $tl[$tweet_id];
	} else {
		// Not found, fetch it specifically from the API
		$request = API_URL."statuses/show/{$tweet_id}.json?include_entities=true";
		$tweet = twitter_process($request);
	}
	return $tweet;
}

function dabr_find_post_in_timeline($id, $stream) 
{
	// Parameter checks
	//if (!is_numeric($id) || !$stream) return;

	// Check if the tweet exists in the timeline given
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

function twitter_user_page($query)
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
		$url = $app->getAuthUrl();
		$content = '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
		$content .= about_page();
	}

	theme('page', "User {$screen_name}", $content);
}

function twitter_favourites_page($query) {
	$screen_name = $query[1];
	if (!$screen_name) {
		user_ensure_authenticated();
		$screen_name = user_current_username();
	}
	$request = API_URL."favorites/{$screen_name}.json?page=".intval($_GET['page']).'&include_entities=true';
	$tl = twitter_process($request);
	$tl = twitter_standard_timeline($tl, 'favourites');
	$content = theme('status_form');
	$content .= theme('timeline', $tl);
	theme('page', 'Favourites', $content);
}

function twitter_mark_favourite_page($query) {
	$id = (string) $query[1];
	if (!is_numeric($id)) return;
	if ($query[0] == 'unfavourite') {
		$request = API_URL."favorites/destroy/$id.json";
	} else {
		$request = API_URL."favorites/create/$id.json";
	}
	twitter_process($request, true);
	twitter_refresh();
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
		twitter_refresh();
	}
}

function twitter_home_page() 
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
		$url = $app->getAuthUrl();
		$content = '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
		$content .= about_page();
	}

	theme('page', 'Home', $content);
}

function twitter_hashtag_page($query) {
	if (isset($query[1])) {
		$hashtag = '#'.$query[1];
		$content = theme('status_form', $hashtag.' ');
		$tl = twitter_search($hashtag);
		$content .= theme('timeline', $tl);
		theme('page', $hashtag, $content);
	} else {
		theme('page', 'Hashtag', 'Hash hash!');
	}
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
			$thread = $app->getPostReplies($query[1],array('count'=>200,'include_annotations' => 1));
			$thread[] = $app->getPost($query[1],array('include_annotations' => 1));
			echo "<pre>";
				print_r($thread);
			
				echo json_encode($thread);
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

function theme_status_form($text = '', $in_reply_to_id = NULL) 
{
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
						&alpha; {$title}
					</legend>
					<form method='post' action='update'>
						<input name='status' value='{$text}' maxlength='256' />
						<input name='in_reply_to_id' value='{$in_reply_to_id}' type='hidden' />
						<input type='submit' value='Tweet' />
					</form>
				</fieldset>";
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

function theme_status($status) {
	$feed[] = $status;
	$tl = twitter_standard_timeline($feed, 'status');
	$content = theme('timeline', $tl);
	return $content;
}


function dabr_retweet_page($query)
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

	$content .= "<p>Repost:</p>
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


function twitter_tweets_per_day($user, $rounding = 1) {
	// Helper function to calculate an average count of tweets per day
	$days_on_twitter = (time() - strtotime($user['created_at'])) / 86400;
	return round($user['counts']['posts'] / $days_on_twitter, $rounding);
}

function dabr_user_bio($user)
{
	$name = $user['name'];
	$username = $user['username'];
	$follows_you = $user['follows_you'];
	$you_follow = $user['you_follow'];
	$you_muted = $user['you_muted'];

	$tweets_per_day = twitter_tweets_per_day($user);

	$raw_date_joined = strtotime($user['created_at']);
	$date_joined = date('jS M Y', $raw_date_joined);

	$bio = "";

	if($user['description']['text'] != "")
			$bio .= dabr_parse_tags($user['description']['text'], $user['description']['entities']) . "<br />";
		
	$bio .= "Joined on " . $date_joined . " - ";
	$bio .= pluralise('post', (int)$user['counts']['posts'], true) . " ";
	$bio .= "(~" . pluralise('post', $tweets_per_day, true) . " per day). ";
	
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
	$out .=			theme('avatar', $full_avatar);
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

function theme_avatar($url, $force_large = TRUE) 
{
	$size = $force_large ? 48 : 24;

	return "<img src=\"$url?w=$size\" height='$size' width='$size' />";
}

function theme_status_time_link($status, $is_link = true) {
	$time = strtotime($status['created_at']);
	if ($time > 0) {
		if (twitter_date('dmy') == twitter_date('dmy', $time) && !setting_fetch('timestamp')) {
			$out = format_interval(time() - $time, 1). ' ago';
		} else {
			$out = twitter_date('H:i', $time);
		}
	} else {
		$out = $status['created_at'];
	}
	if ($is_link)
		$out = "<a href='status/{$status['id']}' class='time'>$out</a>";
	return $out;
}

function twitter_date($format, $timestamp = null) {
/*
	static $offset;
	if (!isset($offset)) {
		if (user_is_authenticated()) {
			if (array_key_exists('utc_offset', $_COOKIE)) {
				$offset = $_COOKIE['utc_offset'];
			} else {
				$user = twitter_user_info();
				$offset = $user->utc_offset;
				setcookie('utc_offset', $offset, time() + 3000000, '/');
			}
		} else {
			$offset = 0;
		}
	}
*/
	$offset = setting_fetch('utc_offset', 0) * 3600;
	if (!isset($timestamp)) {
		$timestamp = time();
	}
	return gmdate($format, $timestamp + $offset);
}

function twitter_standard_timeline($feed, $source) {
	$output = array();
	if (!is_array($feed) && $source != 'thread') return $output;
	
	//32bit int / snowflake patch
	if (is_array($feed)) {
		foreach($feed as $key => $status) {
			if($status->id_str) {
				$feed[$key]->id = $status->id_str;
			}
			if($status->in_reply_to_status_id_str) {
				$feed[$key]->in_reply_to_status_id = $status->in_reply_to_status_id_str;
			}
			if($status->retweeted_status->id_str) {
				$feed[$key]->retweeted_status->id = $status->retweeted_status->id_str;
			}
		}
	}
	
	switch ($source) {
		case 'status':
		case 'favourites':
		case 'friends':
		case 'replies':
		case 'retweets':
		case 'user':
		/*	foreach ($feed as $status) {
				$new = $status;
				if ($new->retweeted_status) {
					$retweet = $new->retweeted_status;
					unset($new->retweeted_status);
					$retweet->retweeted_by = $new;
					$retweet->original_id = $new->id;
					$new = $retweet;
				}
				$new->from = $new->user;
				unset($new->user);
				$output[(string) $new->id] = $new;
			}
			return $output;
*/			return $feed;
		case 'search':
			foreach ($feed as $status) {
				$output[(string) $status->id] = (object) array(
					'id' => $status->id,
					'text' => $status->text,
					'source' => strpos($status->source, '&lt;') !== false ? html_entity_decode($status->source) : $status->source,
					'from' => (object) array(
						'id' => $status->from_user_id,
						'screen_name' => $status->from_user,
						'profile_image_url' => theme_get_avatar($status),
					),
					'to' => (object) array(
						'id' => $status->to_user_id,
						'screen_name' => $status->to_user,
					),
					'created_at' => $status->created_at,
					'geo' => $status->geo,
					'entities' => $status->entities,
					'in_reply_to_status_id' => $status->in_reply_to_status_id,
					'in_reply_to_status_id_str' => $status->in_reply_to_status_id_str,
					'in_reply_to_screen_name' => $status->to_user,
				);
			}
			return $output;

		case 'directs_sent':
		case 'directs_inbox':
			foreach ($feed as $status) {
				$new = $status;
				if ($source == 'directs_inbox') {
					$new->from = $new->sender;
					$new->to = $new->recipient;
				} else {
					$new->from = $new->recipient;
					$new->to = $new->sender;
				}
				unset($new->sender, $new->recipient);
				$new->is_direct = true;
				$output[$new->id_str] = $new;
			}
			return $output;

		case 'thread':
			// First pass: extract tweet info from the HTML
			$html_tweets = explode('</li>', $feed);
			foreach ($html_tweets as $tweet) {
				$id = preg_match_one('#msgtxt(\d*)#', $tweet);
				if (!$id) continue;
				$output[$id] = (object) array(
					'id' => $id,
					'text' => strip_tags(preg_match_one('#</a>: (.*)</span>#', $tweet)),
					'source' => preg_match_one('#>from (.*)</span>#', $tweet),
					'from' => (object) array(
						'id' => preg_match_one('#profile_images/(\d*)#', $tweet),
						'screen_name' => preg_match_one('#twitter.com/([^"]+)#', $tweet),
						'profile_image_url' => preg_match_one('#src="([^"]*)"#' , $tweet),
					),
					'to' => (object) array(
						'screen_name' => preg_match_one('#@([^<]+)#', $tweet),
					),
					'created_at' => str_replace('about', '', preg_match_one('#info">\s(.*)#', $tweet)),
				);
			}
			// Second pass: OPTIONALLY attempt to reverse the order of tweets
			if (setting_fetch('reverse') == 'yes') {
				$first = false;
				foreach ($output as $id => $tweet) {
					$date_string = str_replace('later', '', $tweet->created_at);
					if ($first) {
						$attempt = strtotime("+$date_string");
						if ($attempt == 0) $attempt = time();
						$previous = $current = $attempt - time() + $previous;
					} else {
						$previous = $current = $first = strtotime($date_string);
					}
					$output[$id]->created_at = date('r', $current);
				}
				$output = array_reverse($output);
			}
			return $output;

		default:
			echo "<h1>$source</h1><pre>";
			print_r($feed); die();
	}
}

function preg_match_one($pattern, $subject, $flags = NULL) {
	preg_match($pattern, $subject, $matches, $flags);
	return trim($matches[1]);
}

function twitter_user_info($username = null) {
	if (!$username)
	$username = user_current_username();
	$request = API_URL."users/show.json?screen_name=$username&include_entities=true";
	$user = twitter_process($request);
	return $user;
}

function theme_timeline($feed)
{
	if (count($feed) == 0) return theme('no_tweets');
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
		$status['html'] = dabr_parse_tags($status['text'], $status['entities']);
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
	/*	if ($first==0)
		{
			$since_id = $status->id;
			$first++;
		}
		else
		{
			$max_id =  $status->id;
			if ($status->original_id)
			{
				$max_id =  $status->original_id;
			}
		}
	*/
		if (!$status['is_deleted'])	//	Don't display deleted posts
		{
			$time = strtotime($status['created_at']);

			if ($time > 0)
			{
				$date = twitter_date('l jS F Y', strtotime($status['created_at']));
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

			$text = $status['html'];
			 //nl2br($status['html']);
	/*		if (!in_array(setting_fetch('browser'), array('text', 'worksafe'))) {
				$media = twitter_get_media($status);
			}
			$link = theme('status_time_link', $status, !$status->is_direct);
			$actions = theme('action_icons', $status);
			$avatar = theme('avatar', theme_get_avatar($status->from));
			$source = $status->source ? " from ".str_replace('rel="nofollo	w"', 'rel="nofollow" target="' . get_target() . '"', preg_replace('/&(?![a-z][a-z0-9]*;|#[0-9]+;|#x[0-9a-f]+;)/i', '&amp;', $status->source)) : ''; //need to replace & in links with &amps and force new window on links
			if ($status->place->name) {
				$source .= " " . $status->place->name . ", " . $status->place->country;
			}
			if ($status->in_reply_to_status_id)	{
				$source .= " <a href='status/{$status->in_reply_to_status_id_str}'>in reply to {$status->in_reply_to_screen_name}</a>";
			}
			if ($status->retweet_count)	{
				$source .= " <a href='retweeted_by/{$status->id}'>retweeted ";
				switch($status->retweet_count) {
					case(1) : $source .= "once</a>"; break;
					case(2) : $source .= "twice</a>"; break;
					//	Twitter are uncapping the retweet count (https://dev.twitter.com/discussions/5129) will need to correctly format large numbers
					case(is_int($status->retweet_count)) : $source .= number_format($status->retweet_count) . " times</a>"; break;
					//	Legacy for old tweets where the retweet count is a string (usually "100+")
					default : $source .= $status->retweet_count . " times</a>";
				}
			}
			if ($status->retweeted_by) {
				$retweeted_by = $status->retweeted_by->user->screen_name;
				$source .= "<br /><a href='retweeted_by/{$status->id}'>retweeted</a> by <a href='user/{$retweeted_by}'>{$retweeted_by}</a>";
			}
	*/		
			//$html = "<b><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></b> $actions $link<br />{$text}<br />$media<small>$source</small>";

			$actions = theme('action_icons', $status);
			$link = theme('status_time_link', $status, true);

			$avatar = theme('avatar', $status['user']['avatar_image']['url']);
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

			$html = "<b><a href='user/{$status['user']['username']}'>{$status['user']['name']}</a></b> $actions $link 
					<br />
					{$text}
					<br />
					<small>Sent via $source $conversation</small>";
			unset($row);
			$class = 'status';
			
			if ($page != 'user' && $avatar)
			{
				$row[] = array('data' => $avatar, 'class' => 'avatar');
				$class .= ' shift';
			}
			
			$row[] = array('data' => $html, 'class' => $class);

			$class = 'tweet';
			if ($page != 'replies' && twitter_is_reply($status))
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

function twitter_is_reply($status) {
	if (!user_is_authenticated()) {
		return false;
	}
	$user = user_current_username();

	//	Use Twitter Entities to see if this contains a mention of the user
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

		$tweets_per_day = twitter_tweets_per_day($user);

		$raw_date_joined = strtotime($user['created_at']);
		$date_joined = date('jS M Y', $raw_date_joined);
	
		$content = "<a href=\"user/$username/$id\">$name (@$username)</a>
					<br />
					<span class='about'>";

		if($user['description']['text'] != "")
			$content .= "Bio: " . dabr_user_bio($user);//twitter_parse_tags($user['description']['text']) . "<br />";

		$content .= dabr_user_actions($user,false);		
/*		$content .= "Info: ";
		$content .= "Joined on " . $date_joined . ". ";
		$content .= pluralise('post', (int)$user['counts']['posts'], true) . " ";
		$content .= "(~" . pluralise('post', $tweets_per_day, true) . " per day), ";
		$content .= pluralise('friend', (int)$user['counts']['following'], true) . ", ";
		$content .= pluralise('follower', (int)$user['counts']['followers'], true) . ", ";

		if ($follows_you && $you_follow)
		{
			$content .= "YOU ARE BEST FRIENDS!";			
		}
		else if ($follows_you)
		{
			$content .= "Follows you.";
		}
		else if ($you_follow)
		{
			$content .= "You are following.";
		}

		if ($you_muted)
		{
			$content .= " Shhh! Muted.";
		}
*/
		$content .= "<br />";
/*		$content .= "Last tweet: ";
		if($user->protected == 'true' && $last_tweet == 0)
			$content .= "Private";
		else if($last_tweet == 0)
			$content .= "Never tweeted";
		else
*/
//			$content .= twitter_date('l jS F Y', $last_tweet);
		$content .= "</span>";

		$rows[] = 	array(
						'data' => 
							array(
								array(
										'data' => theme('avatar',	$user['avatar_image']['url']), 
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

function theme_no_tweets() {
	return '<p>No posts to display.</p>';
}

function theme_search_results($feed) {
	$rows = array();
	foreach ($feed->results as $status) {
		$text = twitter_parse_tags($status->text, $status->entities);
		$link = theme('status_time_link', $status);
		$actions = theme('action_icons', $status);

		$row = array(
		theme('avatar', theme_get_avatar($status)),
      "<a href='user/{$status->from_user}'>{$status->from_user}</a> $actions - {$link}<br />{$text}",
		);
		if (twitter_is_reply($status)) {
			$row = array('class' => 'reply', 'data' => $row);
		}
		$rows[] = $row;
	}
	$content = theme('table', array(), $rows, array('class' => 'timeline'));
	$content .= theme('pagination');
	return $content;
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
	$actions[] = theme('action_icon', "retweet/{$status['id']}", "images/retweet{$L}.png", 'RT');

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
		return "<a href='$url' alt='$text' target='" . get_target() . "'><img src='$image_url' /></a>";
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
