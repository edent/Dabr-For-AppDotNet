<?php

/*
Syntax is 
'Name|links,bodybg,bodyt,small,odd,even,replyodd,replyeven,menubg,menut,menua',

Assembled in theme_css()
*/

$GLOBALS['colour_schemes'] = array(
	0 => 'Pretty In Pink|c06,fcd,623,c8a,fee,fde,ffa,dd9,c06,fee,fee',
	1 => 'Ugly Orange|b50,ddd,111,555,fff,eee,ffa,dd9,e81,c40,fff',
	2 => 'Twytter Blue|2674B2,BFDBE6,111,999,F7FCF6,D8ECF7,A9D0DF,8CBED5,002031,88D0DE,88D0DE',
	3 => 'Sickly Green|293C03,ccc,000,555,fff,eee,CCE691,ACC671,495C23,919C35,fff',
	4 => 'Kris\' Purple|d5d,000,ddd,999,222,111,202,101,909,222,000,000',
	5 => '#red|d12,ddd,111,555,fff,eee,ffa,dd9,c12,fff,fff',
	6 => 'Fazebook Blue|3B5998,F7F7F7,111,555,D8DFEA,EEE,FFA,DD9,3B5998,FFF,FFF',
	
);

menu_register(array(
	'settings' => array(
		'callback' => 'settings_page',
	),
	'reset' => array(
		'hidden' => true,
		'callback' => 'cookie_monster',
	),
));

function cookie_monster() {
	$cookies = array(
		'modes',
		'menustyle',
		'settings',
		'utc_offset',
		'search_favourite',
		'perPage',
		'USER_AUTH',
	);
	$duration = time() - 3600;
	foreach ($cookies as $cookie) {
		setcookie($cookie, NULL, $duration, '/');
		setcookie($cookie, NULL, $duration);
	}
	return theme('page', 'Cookie Monster', '<p>The cookie monster has logged you out and cleared all settings. Try logging in again now.</p>');
}

function setting_fetch($setting, $default = NULL) {
	$settings = (array) unserialize(base64_decode($_COOKIE['settings']));
	if (array_key_exists($setting, $settings)) {
		return $settings[$setting];
	} else {
		return $default;
	}
}

function setcookie_year($name, $value) {
	$duration = time() + (3600 * 24 * 365);
	setcookie($name, $value, $duration, '/');
}

function settings_page($args) {
	if ($args[1] == 'save') {
		$settings['modes']       = $_POST['modes'];
		$settings['menustyle']   = $_POST['menustyle'];
		$settings['perPage']     = $_POST['perPage'];
		$settings['gwt']         = $_POST['gwt'];
		$settings['colours']     = $_POST['colours'];
		$settings['timestamp']   = $_POST['timestamp'];
		$settings['hide_inline'] = $_POST['hide_inline'];
		$settings['utc_offset']  = (float)$_POST['utc_offset'];
		$settings['emoticons']   = $_POST['emoticons'];
		$settings['font_size']   = $_POST['font_size'];
				
		setcookie_year('settings', base64_encode(serialize($settings)));
		dabr_refresh('');
	}

	$modes = array(
		'bigtouch'	=> 'Big Icons <img src="images/replyL.png" alt="Big Icon"/>',
		'touch'		=> 'Small Icons <img src="images/reply.png" alt="Small Icon"/>',
		'text'		=> 'Text only @',
	);
	
	$font_size = array(
		'0.5' => "<span style=\"font-size:50%\">Small</span>", 
		'1' => "<span style=\"font-size:100%\">Normal</span>", 
		'1.5' => "<span style=\"font-size:150%\">Large</span>", 
		'2' => "<span style=\"font-size:200%\">Huge</span>", 
	);

	$menustyle = array(
		'smart'	=> 'Smart Menu',
		'old'	=> 'Old Style Menu (For older web browsers)'
	);

	$perPage = array(
		  '5'	=>   '5 Posts Per Page',
		 '10'	=>  '10 Posts Per Page',
		 '20'	=>  '20 Posts Per Page',
		 '30'	=>  '30 Posts Per Page',
		 '40'	=>  '40 Posts Per Page',
		 '50'	=>  '50 Posts Per Page',
		'100' 	=> '100 Posts Per Page',
		'150' 	=> '150 Posts Per Page',
		'200' 	=> '200 Posts Per Page',
	);

	$gwt = array(
		'off' => 'direct',
		'on' => 'via GWT',
	);
	
	$emoticons = array(
		'on' => 'ON',
		'off' => 'OFF',
	);

	$colour_schemes = array();
	foreach ($GLOBALS['colour_schemes'] as $id => $info) {
		list($name, $colours) = explode('|', $info);
		$colour_schemes[$id] = $name;
	}
	
	$utc_offset = setting_fetch('utc_offset', 0);

	if ($utc_offset > 0) {
		$utc_offset = '+' . $utc_offset;
	}

	$content .= '<h1>Settings</h1>';
	$content .= theme_get_logo();
	$content .= '<br>This is where you can set your personal preferences! Have fun changing the colour schemes - my favourite is PINK!';

	$content .= '<form action="settings/save" method="post" style="clear:both">
					<h3>Colour scheme:<br>
						<select name="colours">';
	$content .= theme('options', $colour_schemes, setting_fetch('colours', 0));
	$content .= '		</select>
					</h3>';

	$content .= '<h3>Mode:</h3>';

	$content .= theme_radio($modes, "modes", setting_fetch('modes', 'bigtouch'));

	$content .= '<h3>Font Size:</h3>';

	$content .= theme_radio($font_size, "font_size", setting_fetch('font_size', '1'));


	$content .= '	<h3>Menu Bar:</h3>
						<select name="menustyle">';
	$content .= theme('options', $menustyle, setting_fetch('menustyle', 'smart'));
	$content .= '		</select>';

	$content .= '	<h3>Posts Per Page:</h3>
						<select name="perPage">';
	$content .= theme('options', $perPage, setting_fetch('perPage', 20));
	$content .= '		</select>';

	$content .= '	<h3>Emoticons - show :-) as <img src="images/emoticons/icon_smile.gif" alt=":-)" /></h3>
						<select name="emoticons">';
	$content .= theme('options', $emoticons, setting_fetch('emoticons', 'on'));
	$content .= '		</select>';

	$content .=	'	<h3>External links go:</h3>
						<select name="gwt">';
	$content .= theme('options', $gwt, setting_fetch('gwt', 'off'));
	$content .= '		</select>
						<small><br>Google Web Transcoder (GWT) converts third-party sites into small, speedy pages suitable for older phones and people with less bandwidth.</small>';
	$content .= '	<h3>Others:</h3>
					<p>
						<label>
							<input type="checkbox" name="timestamp" value="yes" '. (setting_fetch('timestamp') == 'yes' ? ' checked="checked" ' : '') .' /> Show the timestamp ' . dabr_date('H:i') . ' instead of 25 sec ago
						</label>
					</p>';
	$content .= '	<p>
						<label>
							<input type="checkbox" name="hide_inline" value="yes" '. (setting_fetch('hide_inline') == 'yes' ? ' checked="checked" ' : '') .' /> Hide inline media (eg image thumbnails &amp; embedded videos)
						</label>
					</p>';
	$content .= '	<p>
						<label>The time in UTC is currently ' . gmdate('H:i') . ', by using an offset of <input type="text" name="utc_offset" value="'. $utc_offset .'" size="3" /> we display the time as ' . dabr_date('H:i') . '.<br>
							It is worth adjusting this value if the time appears to be wrong.
						</label>
					</p>';
	$content .= '	<p>
						<input type="submit" value="Save" />
					</p>
				</form>';
	$content .= '<hr />
				<p>Visit <a href="reset">Reset</a> if things go horribly wrong - it will log you out and clear all settings.</p>';

	return theme('page', 'Settings', $content);
}