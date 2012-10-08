<?php

/*
Assembled in theme_css()

Syntax is 
			'Name|			links	,bodybg	,bodyt	,small	,odd	,even	,replyodd,replyeven,menubg,menut,menua',
*/

$GLOBALS['colour_schemes'] = array(
	0 => 'Pretty In Pink|	c06		,fcd	,623	,c8a	,fee	,fde	,ffa	,dd9	,c06	,fee	,fee',
	1 => 'Ugly Orange|		b50		,ddd	,111	,555	,fff	,eee	,ffa	,dd9	,e81	,c40	,fff',
	2 => 'Twytter Blue|		2674B2	,BFDBE6	,111	,999	,F7FCF6	,D8ECF7	,A9D0DF	,8CBED5	,002031	,88D0DE	,88D0DE',
	3 => 'Sickly Green|		293C03	,ccc	,000	,555	,fff	,eee	,CCE691	,ACC671	,495C23	,919C35	,fff'	,
	4 => 'Kris\' Purple|	d5d		,000	,ddd	,999	,222	,111	,202	,101	,909	,222	,000'	,
	5 => '#red|				d12		,ddd	,111	,555	,fff	,eee	,ffa	,dd9	,c12	,fff	,fff'	,
	6 => 'Fazebook Blue|	3B5998	,F7F7F7	,111	,555	,D8DFEA	,EEE	,FFA	,DD9	,3B5998	,FFF	,FFF'	,
	7 => 'Night Reading|	FF0000	,000000	,FF0000	,FF0000	,222	,111	,202	,101	,000000	,FF0000	,FF0000'	,
	8 => 'Clear and Simple|	130f30	,ccc	,130f30	,130f30	,fff	,EEE	,FFA	,DD9	,ccc	,130f30	,130f30'	,
	
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
	return theme('page', 'Cookie Monster', 
		'<p>The cookie monster has logged you out and cleared all settings. Try logging in again now.</p>');
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
		$settings['nondirected'] = $_POST['nondirected'];
		$settings['fonts']       = $_POST['fonts'];
		$settings['avatar_show'] = $_POST['avatar_show'];
		$settings['avatar_size'] = $_POST['avatar_size'];
				
		setcookie_year('settings', base64_encode(serialize($settings)));
		dabr_refresh('');
	}

	$modes = array(
		'bigtouch'	=> 'Big Icons <img src="images/replyL.png" alt="Big Icon"/>',
		'touch'		=> 'Small Icons <img src="images/reply.png" alt="Small Icon"/>',
		'text'		=> 'Text only @',
	);
	
	$font_size = array(
		'0.5' => "<span style=\"font-size:50%\">Smallest</span>", 
		'0.75' => "<span style=\"font-size:100%\">Small</span>", 
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

	$avatar_show = array(
		'on' => 'ON',
		'off' => 'OFF',
	);

	$colour_schemes = array();
	foreach ($GLOBALS['colour_schemes'] as $id => $info) {
		list($name, $colours) = explode('|', $info);
		$colour_schemes[$id] = $name;
	}

	$fonts = array(
			'Schoolbell' => 'Schoolbell',
			'Droid+Sans' => 'Droid Sans',
			'Ubuntu+Mono' => 'Ubuntu Mono',
			'Lora'=>'Lora',
			'Open+Sans'=>'Open Sans Light'
		);

	$utc_offset = setting_fetch('utc_offset', 0);

	if ($utc_offset > 0) {
		$utc_offset = '+' . $utc_offset;
	}

	$content .= '<h1>Settings</h1>';
	$content .= theme_get_logo();
	$content .= '<br>This is where you can set your personal preferences! 
					Have fun changing the colour schemes - my favourite is PINK!';

	$content .= '<form action="settings/save" method="post" style="clear:both">
					<h3>Colour scheme:</h3>
						<select name="colours">';
	$content .= theme('options', $colour_schemes, setting_fetch('colours', 0));
	$content .= '		</select>
					</h3>';

	$content .= '<h3>Mode:</h3>';

	$content .= theme_radio($modes, "modes", setting_fetch('modes', 'bigtouch'));

	$content .= '<h3>Font Size:</h3>';

	$content .= theme_radio($font_size, "font_size", setting_fetch('font_size', '1'));

	$content .= '<h3>Font:</h3>';

	$content .= theme_radio($fonts, "fonts", setting_fetch('fonts', 'Lora'));


	$content .= '	<h3>Menu Bar:</h3>
						<select name="menustyle">';
	$content .= theme('options', $menustyle, setting_fetch('menustyle', 'smart'));
	$content .= '		</select>';

	$content .= '	<h3>Posts Per Page:</h3>
						<select name="perPage">';
	$content .= theme('options', $perPage, setting_fetch('perPage', 20));
	$content .= '		</select>';

	$content .= '	<h3>Avatar Size</h3>';
	$content .= '	<input 
						name="avatar_size" 
						type="range" 
						min="12" 
						max="240" 
						value="'.setting_fetch('avatar_size',48).'" 
						step="12" 
						onchange="showValue(this.value)" 
						style="width: 90%;"
					/>';
	$content .= '	<span id="range">'.setting_fetch('avatar_size',48).'</span>
					<br>
					<img 
						id="avatar" 
						src="'.theme_get_full_avatar().'?w='.setting_fetch('avatar_size',48).'" 
						alt="Default Avatar"
					/>
					<script type="text/javascript">
					function showValue(newValue)
					{
						document.getElementById("range").innerHTML=newValue;
						
						document.getElementById("avatar").src = "'.theme_get_full_avatar().'?w="+newValue;
					}
					
					function replaceQueryString(url,param,value) 
					{
						var re = new RegExp("([?|&])" + param + "=.*?(&|$)","i");
						if (url.match(re))
							return url.replace(re,\'$1\' + param + "=" + value + \'$2\');
						else
							return url + \'&\' + param + "=" + value;
					}
					</script>';

	$content .= '	<h3>Work Safe</h3>';
	
	$content .= '	<p>
						<label>
							<input type="checkbox" name="hide_inline" value="yes" ' . 
								(setting_fetch('hide_inline') == 'yes' ? ' checked="checked" ' : '') . 
							' /> Hide inline media (eg image thumbnails &amp; embedded videos)
						</label>
					</p>';

	$content .= '	<p>
						<label>
							<input type="checkbox" name="avatar_show" value="off" ' . 
								(setting_fetch('avatar_show','on') == 'off' ? ' checked="checked" ' : '') .
								' /> Hide avatars
						</label>
					</p>';

	$content .= '	<p>
						<label>
							<input type="checkbox" name="emoticons" value="on" ' . 
								(setting_fetch('emoticons','on') == 'on' ? ' checked="checked" ' : '') .
								' /> show :-) as <img src="images/emoticons/icon_smile.gif" alt=":-)" />
						</label>
					</p>';

	$content .=	'	<h3>Show non-directed replies:</h3>
						<label>
							<input type="checkbox" name="nondirected" value="1" ' . 
								(setting_fetch('nondirected') == '1' ? ' checked="checked" ' : '') .
								' /> You follow Alice, you don\'t follow Bob. 
								Alice sends a post to Bob - do you want to see it?
						</label>';

	$content .=	'	<h3>External links go:</h3>
						<select name="gwt">';
	$content .= theme('options', $gwt, setting_fetch('gwt', 'off'));
	$content .= '		</select>
						Google Web Transcoder (GWT) converts third-party sites into small, speedy pages suitable for older phones and people with less bandwidth.';
	$content .= '	<h3>Others:</h3>
					<p>
						<label>
							<input type="checkbox" name="timestamp" value="yes" ' . 
								(setting_fetch('timestamp') == 'yes' ? ' checked="checked" ' : '') .
								' /> Show the timestamp ' . dabr_date('H:i') . ' instead of 25 sec ago
						</label>
					</p>';
	
	$content .= '	<p>
						<label>The time is currently ' . gmdate('H:i') . ', by using an offset of <input type="text" name="utc_offset" value="'. $utc_offset .'" size="3" /> we display the time as ' . dabr_date('H:i') . '.<br>
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