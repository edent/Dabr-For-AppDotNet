<?php

function embedly_embed_thumbnails(&$feed) 
{
	if (setting_fetch('hide_inline')) 
	{
		return $text;
	}
	
	//	Latest regex available at
	//	http://api.embed.ly/1/services/php
	//	Some are commented out, becuase the thumbnail URL is simple to parse - no need to waste an Embedly call
	$embedly_regex_array = array(
		"#http://.*youtube\\.com/watch.*#i", "#http://.*\\.youtube\\.com/v/.*#i", "#https://.*youtube\\.com/watch.*#i", "#https://.*\\.youtube\\.com/v/.*#i", "#http://youtu\\.be/.*#i", "#http://.*\\.youtube\\.com/user/.*#i", "#http://.*\\.youtube\\.com/.*\\#.*/.*#i", "#http://m\\.youtube\\.com/watch.*#i", "#http://m\\.youtube\\.com/index.*#i", "#http://.*\\.youtube\\.com/profile.*#i", "#http://.*\\.youtube\\.com/view_play_list.*#i", "#http://.*\\.youtube\\.com/playlist.*#i",
		"#http://.*twitch\\.tv/.*#i", "#http://.*justin\\.tv/.*/b/.*#i", "#http://.*justin\\.tv/.*/w/.*#i",
		"#http://.*twitch\\.tv/.*#i", "#http://.*twitch\\.tv/.*/b/.*#i",
		"#http://www\\.ustream\\.tv/recorded/.*#i", "#http://www\\.ustream\\.tv/channel/.*#i", "#http://www\\.ustream\\.tv/.*#i",
		"#http://qik\\.com/video/.*#i", "#http://qik\\.com/.*#i", "#http://qik\\.ly/.*#i",
		"#http://.*revision3\\.com/.*#i",
		"#http://.*\\.dailymotion\\.com/video/.*#i", "#http://.*\\.dailymotion\\.com/.*/video/.*#i",
		"#http://collegehumor\\.com/video:.*#i", "#http://collegehumor\\.com/video/.*#i", "#http://www\\.collegehumor\\.com/video:.*#i", "#http://www\\.collegehumor\\.com/video/.*#i",
		"#http://.*twitvid\\.com/.*#i",
		"#http://vids\\.myspace\\.com/index\\.cfm\\?fuseaction=vids\\.individual&videoid.*#i", "#http://www\\.myspace\\.com/index\\.cfm\\?fuseaction=.*&videoid.*#i",
		"#http://www\\.metacafe\\.com/watch/.*#i", "#http://www\\.metacafe\\.com/w/.*#i",
		"#http://blip\\.tv/.*/.*#i", "#http://.*\\.blip\\.tv/.*/.*#i",
		"#http://video\\.google\\.com/videoplay\\?.*#i",
		"#http://.*revver\\.com/video/.*#i",
		"#http://video\\.yahoo\\.com/watch/.*/.*#i", "#http://video\\.yahoo\\.com/network/.*#i", "#http://sports\\.yahoo\\.com/video/.*#i",
		"#http://.*viddler\\.com/v/.*#i",
		"#http://liveleak\\.com/view\\?.*#i", "#http://www\\.liveleak\\.com/view\\?.*#i",
		"#http://animoto\\.com/play/.*#i",
		"#http://dotsub\\.com/view/.*#i",
		"#http://www\\.overstream\\.net/view\\.php\\?oid=.*#i",
		"#http://www\\.livestream\\.com/.*#i",
		"#http://www\\.worldstarhiphop\\.com/videos/video.*\\.php\\?v=.*#i", "#http://worldstarhiphop\\.com/videos/video.*\\.php\\?v=.*#i",
		"#http://teachertube\\.com/viewVideo\\.php.*#i", "#http://www\\.teachertube\\.com/viewVideo\\.php.*#i", "#http://www1\\.teachertube\\.com/viewVideo\\.php.*#i", "#http://www2\\.teachertube\\.com/viewVideo\\.php.*#i",
		"#http://bambuser\\.com/v/.*#i", "#http://bambuser\\.com/channel/.*#i", "#http://bambuser\\.com/channel/.*/broadcast/.*#i",
		"#http://www\\.schooltube\\.com/video/.*/.*#i",
		"#http://bigthink\\.com/ideas/.*#i", "#http://bigthink\\.com/series/.*#i",
		"#http://sendables\\.jibjab\\.com/view/.*#i", "#http://sendables\\.jibjab\\.com/originals/.*#i", "#http://jibjab\\.com/view/.*#i",
		"#http://www\\.xtranormal\\.com/watch/.*#i",
		"#http://socialcam\\.com/v/.*#i", "#http://www\\.socialcam\\.com/v/.*#i",
		"#http://dipdive\\.com/media/.*#i", "#http://dipdive\\.com/member/.*/media/.*#i", "#http://dipdive\\.com/v/.*#i", "#http://.*\\.dipdive\\.com/media/.*#i", "#http://.*\\.dipdive\\.com/v/.*#i",
		"#http://v\\.youku\\.com/v_show/.*\\.html#i", "#http://v\\.youku\\.com/v_playlist/.*\\.html#i",
		"#http://www\\.snotr\\.com/video/.*#i", "#http://snotr\\.com/video/.*#i",
		"#http://video\\.jardenberg\\.se/.*#i",
		"#http://www\\.clipfish\\.de/.*/.*/video/.*#i",
		"#http://www\\.myvideo\\.de/watch/.*#i",
		"#http://www\\.whitehouse\\.gov/photos-and-video/video/.*#i", "#http://www\\.whitehouse\\.gov/video/.*#i", "#http://wh\\.gov/photos-and-video/video/.*#i", "#http://wh\\.gov/video/.*#i",
		"#http://www\\.hulu\\.com/watch.*#i", "#http://www\\.hulu\\.com/w/.*#i", "#http://www\\.hulu\\.com/embed/.*#i", "#http://hulu\\.com/watch.*#i", "#http://hulu\\.com/w/.*#i",
		"#http://.*crackle\\.com/c/.*#i",
		"#http://www\\.fancast\\.com/.*/videos#i",
		"#http://www\\.funnyordie\\.com/videos/.*#i", "#http://www\\.funnyordie\\.com/m/.*#i", "#http://funnyordie\\.com/videos/.*#i", "#http://funnyordie\\.com/m/.*#i",
		"#http://www\\.vimeo\\.com/groups/.*/videos/.*#i", "#http://www\\.vimeo\\.com/.*#i", "#https://www\\.vimeo\\.com/.*#i", "#http://vimeo\\.com/groups/.*/videos/.*#i", "#http://vimeo\\.com/.*#i", "#https://vimeo\\.com/.*#i", "#http://vimeo\\.com/m/\\#/.*#i",
		"#http://www\\.ted\\.com/talks/.*\\.html.*#i", "#http://www\\.ted\\.com/talks/lang/.*/.*\\.html.*#i", "#http://www\\.ted\\.com/index\\.php/talks/.*\\.html.*#i", "#http://www\\.ted\\.com/index\\.php/talks/lang/.*/.*\\.html.*#i",
		"#http://.*nfb\\.ca/film/.*#i",
		"#http://www\\.thedailyshow\\.com/watch/.*#i", "#http://www\\.thedailyshow\\.com/full-episodes/.*#i", "#http://www\\.thedailyshow\\.com/collection/.*/.*/.*#i",
		"#http://movies\\.yahoo\\.com/movie/.*/video/.*#i", "#http://movies\\.yahoo\\.com/movie/.*/trailer#i", "#http://movies\\.yahoo\\.com/movie/.*/video#i",
		"#http://www\\.colbertnation\\.com/the-colbert-report-collections/.*#i", "#http://www\\.colbertnation\\.com/full-episodes/.*#i", "#http://www\\.colbertnation\\.com/the-colbert-report-videos/.*#i",
		"#http://www\\.comedycentral\\.com/videos/index\\.jhtml\\?.*#i",
		"#http://www\\.theonion\\.com/video/.*#i", "#http://theonion\\.com/video/.*#i",
		"#http://wordpress\\.tv/.*/.*/.*/.*/#i",
		"#http://www\\.traileraddict\\.com/trailer/.*#i", "#http://www\\.traileraddict\\.com/clip/.*#i", "#http://www\\.traileraddict\\.com/poster/.*#i",
		"#http://www\\.escapistmagazine\\.com/videos/.*#i",
		"#http://www\\.trailerspy\\.com/trailer/.*/.*#i", "#http://www\\.trailerspy\\.com/trailer/.*#i", "#http://www\\.trailerspy\\.com/view_video\\.php.*#i",
		"#http://www\\.atom\\.com/.*/.*/#i",
		"#http://fora\\.tv/.*/.*/.*/.*#i",
		"#http://www\\.spike\\.com/video/.*#i",
		"#http://www\\.gametrailers\\.com/video.*#i", "#http://gametrailers\\.com/video.*#i",
		"#http://www\\.koldcast\\.tv/video/.*#i", "#http://www\\.koldcast\\.tv/\\#video:.*#i",
		"#http://mixergy\\.com/.*#i",
		"#http://video\\.pbs\\.org/video/.*#i",
		"#http://www\\.zapiks\\.com/.*#i",
		"#http://tv\\.digg\\.com/diggnation/.*#i", "#http://tv\\.digg\\.com/diggreel/.*#i", "#http://tv\\.digg\\.com/diggdialogg/.*#i",
		"#http://www\\.trutv\\.com/video/.*#i",
		"#http://www\\.nzonscreen\\.com/title/.*#i", "#http://nzonscreen\\.com/title/.*#i",
		"#http://app\\.wistia\\.com/embed/medias/.*#i", "#https://app\\.wistia\\.com/embed/medias/.*#i", "#http://wistia\\.com/.*#i", "#https://wistia\\.com/.*#i", "#http://.*\\.wistia\\.com/.*#i", "#https://.*\\.wistia\\.com/.*#i", "#http://.*\\.wi\\.st/.*#i", "#http://wi\\.st/.*#i", "#https://.*\\.wi\\.st/.*#i", "#https://wi\\.st/.*#i",
		"#http://hungrynation\\.tv/.*/episode/.*#i", "#http://www\\.hungrynation\\.tv/.*/episode/.*#i", "#http://hungrynation\\.tv/episode/.*#i", "#http://www\\.hungrynation\\.tv/episode/.*#i",
		"#http://indymogul\\.com/.*/episode/.*#i", "#http://www\\.indymogul\\.com/.*/episode/.*#i", "#http://indymogul\\.com/episode/.*#i", "#http://www\\.indymogul\\.com/episode/.*#i",
		"#http://channelfrederator\\.com/.*/episode/.*#i", "#http://www\\.channelfrederator\\.com/.*/episode/.*#i", "#http://channelfrederator\\.com/episode/.*#i", "#http://www\\.channelfrederator\\.com/episode/.*#i",
		"#http://tmiweekly\\.com/.*/episode/.*#i", "#http://www\\.tmiweekly\\.com/.*/episode/.*#i", "#http://tmiweekly\\.com/episode/.*#i", "#http://www\\.tmiweekly\\.com/episode/.*#i",
		"#http://99dollarmusicvideos\\.com/.*/episode/.*#i", "#http://www\\.99dollarmusicvideos\\.com/.*/episode/.*#i", "#http://99dollarmusicvideos\\.com/episode/.*#i", "#http://www\\.99dollarmusicvideos\\.com/episode/.*#i",
		"#http://ultrakawaii\\.com/.*/episode/.*#i", "#http://www\\.ultrakawaii\\.com/.*/episode/.*#i", "#http://ultrakawaii\\.com/episode/.*#i", "#http://www\\.ultrakawaii\\.com/episode/.*#i",
		"#http://barelypolitical\\.com/.*/episode/.*#i", "#http://www\\.barelypolitical\\.com/.*/episode/.*#i", "#http://barelypolitical\\.com/episode/.*#i", "#http://www\\.barelypolitical\\.com/episode/.*#i",
		"#http://barelydigital\\.com/.*/episode/.*#i", "#http://www\\.barelydigital\\.com/.*/episode/.*#i", "#http://barelydigital\\.com/episode/.*#i", "#http://www\\.barelydigital\\.com/episode/.*#i",
		"#http://threadbanger\\.com/.*/episode/.*#i", "#http://www\\.threadbanger\\.com/.*/episode/.*#i", "#http://threadbanger\\.com/episode/.*#i", "#http://www\\.threadbanger\\.com/episode/.*#i",
		"#http://vodcars\\.com/.*/episode/.*#i", "#http://www\\.vodcars\\.com/.*/episode/.*#i", "#http://vodcars\\.com/episode/.*#i", "#http://www\\.vodcars\\.com/episode/.*#i",
		"#http://confreaks\\.net/videos/.*#i", "#http://www\\.confreaks\\.net/videos/.*#i",
		"#http://video\\.allthingsd\\.com/video/.*#i",
		"#http://videos\\.nymag\\.com/.*#i",
		"#http://aniboom\\.com/animation-video/.*#i", "#http://www\\.aniboom\\.com/animation-video/.*#i",
		"#http://clipshack\\.com/Clip\\.aspx\\?.*#i", "#http://www\\.clipshack\\.com/Clip\\.aspx\\?.*#i",
		"#http://grindtv\\.com/.*/video/.*#i", "#http://www\\.grindtv\\.com/.*/video/.*#i",
		"#http://ifood\\.tv/recipe/.*#i", "#http://ifood\\.tv/video/.*#i", "#http://ifood\\.tv/channel/user/.*#i", "#http://www\\.ifood\\.tv/recipe/.*#i", "#http://www\\.ifood\\.tv/video/.*#i", "#http://www\\.ifood\\.tv/channel/user/.*#i",
		"#http://logotv\\.com/video/.*#i", "#http://www\\.logotv\\.com/video/.*#i",
		"#http://lonelyplanet\\.com/Clip\\.aspx\\?.*#i", "#http://www\\.lonelyplanet\\.com/Clip\\.aspx\\?.*#i",
		"#http://streetfire\\.net/video/.*\\.htm.*#i", "#http://www\\.streetfire\\.net/video/.*\\.htm.*#i",
		"#http://trooptube\\.tv/videos/.*#i", "#http://www\\.trooptube\\.tv/videos/.*#i",
		"#http://sciencestage\\.com/v/.*\\.html#i", "#http://sciencestage\\.com/a/.*\\.html#i", "#http://www\\.sciencestage\\.com/v/.*\\.html#i", "#http://www\\.sciencestage\\.com/a/.*\\.html#i",
		"#http://link\\.brightcove\\.com/services/player/bcpid.*#i",
		"#http://wirewax\\.com/.*#i", "#http://www\\.wirewax\\.com/.*#i",
		"#http://canalplus\\.fr/.*#i", "#http://www\\.canalplus\\.fr/.*#i",
		"#http://www\\.vevo\\.com/watch/.*#i", "#http://www\\.vevo\\.com/video/.*#i",
		"#http://pixorial\\.com/watch/.*#i", "#http://www\\.pixorial\\.com/watch/.*#i",
		"#http://spreecast\\.com/events/.*#i", "#http://www\\.spreecast\\.com/events/.*#i",
		"#http://showme\\.com/sh/.*#i", "#http://www\\.showme\\.com/sh/.*#i",
		"#http://.*\\.looplogic\\.com/.*#i", "#https://.*\\.looplogic\\.com/.*#i",
		"#http://on\\.aol\\.com/video/.*#i", "#http://on\\.aol\\.com/playlist/.*#i",
		"#http://videodetective\\.com/.*/.*#i", "#http://www\\.videodetective\\.com/.*/.*#i",
		"#http://www\\.godtube\\.com/featured/video/.*#i", "#http://godtube\\.com/featured/video/.*#i", "#http://www\\.godtube\\.com/watch/.*#i", "#http://godtube\\.com/watch/.*#i",
		"#http://www\\.tangle\\.com/view_video.*#i",
		"#http://mediamatters\\.org/mmtv/.*#i",
		"#http://www\\.clikthrough\\.com/theater/video/.*#i",
		"#http://www\\.clipsyndicate\\.com/video/playlist/.*/.*#i",
		"#http://gist\\.github\\.com/.*#i", "#https://gist\\.github\\.com/.*#i",
		"#http://twitter\\.com/.*/status/.*#i", "#http://twitter\\.com/.*/statuses/.*#i", "#http://www\\.twitter\\.com/.*/status/.*#i", "#http://www\\.twitter\\.com/.*/statuses/.*#i", "#http://mobile\\.twitter\\.com/.*/status/.*#i", "#http://mobile\\.twitter\\.com/.*/statuses/.*#i", "#https://twitter\\.com/.*/status/.*#i", "#https://twitter\\.com/.*/statuses/.*#i", "#https://www\\.twitter\\.com/.*/status/.*#i", "#https://www\\.twitter\\.com/.*/statuses/.*#i", "#https://mobile\\.twitter\\.com/.*/status/.*#i", "#https://mobile\\.twitter\\.com/.*/statuses/.*#i",
		"#http://www\\.crunchbase\\.com/.*/.*#i", "#http://crunchbase\\.com/.*/.*#i",
		"#http://www\\.slideshare\\.net/.*/.*#i", "#http://www\\.slideshare\\.net/mobile/.*/.*#i", "#http://slidesha\\.re/.*#i",
		"#http://scribd\\.com/doc/.*#i", "#http://www\\.scribd\\.com/doc/.*#i", "#http://scribd\\.com/mobile/documents/.*#i", "#http://www\\.scribd\\.com/mobile/documents/.*#i",
		"#http://screenr\\.com/.*#i",
		"#http://www\\.5min\\.com/Video/.*#i",
		"#http://www\\.howcast\\.com/videos/.*#i",
		"#http://www\\.screencast\\.com/.*/media/.*#i", "#http://screencast\\.com/.*/media/.*#i", "#http://www\\.screencast\\.com/t/.*#i", "#http://screencast\\.com/t/.*#i",
		"#http://issuu\\.com/.*/docs/.*#i",
		"#http://www\\.kickstarter\\.com/projects/.*/.*#i",
		"#http://www\\.scrapblog\\.com/viewer/viewer\\.aspx.*#i",
		"#http://foursquare\\.com/.*#i", "#http://www\\.foursquare\\.com/.*#i", "#https://foursquare\\.com/.*#i", "#https://www\\.foursquare\\.com/.*#i", "#http://4sq\\.com/.*#i",
		"#http://linkedin\\.com/in/.*#i", "#http://linkedin\\.com/pub/.*#i", "#http://.*\\.linkedin\\.com/in/.*#i", "#http://.*\\.linkedin\\.com/pub/.*#i",
		"#http://ping\\.fm/p/.*#i",
		"#http://chart\\.ly/symbols/.*#i", "#http://chart\\.ly/.*#i",
		"#http://maps\\.google\\.com/maps\\?.*#i", "#http://maps\\.google\\.com/\\?.*#i", "#http://maps\\.google\\.com/maps/ms\\?.*#i",
		"#http://.*\\.craigslist\\.org/.*/.*#i",
		"#http://my\\.opera\\.com/.*/albums/show\\.dml\\?id=.*#i", "#http://my\\.opera\\.com/.*/albums/showpic\\.dml\\?album=.*&picture=.*#i",
		"#http://tumblr\\.com/.*#i", "#http://.*\\.tumblr\\.com/post/.*#i",
		"#http://www\\.polleverywhere\\.com/polls/.*#i", "#http://www\\.polleverywhere\\.com/multiple_choice_polls/.*#i", "#http://www\\.polleverywhere\\.com/free_text_polls/.*#i",
		"#http://www\\.quantcast\\.com/wd:.*#i", "#http://www\\.quantcast\\.com/.*#i",
		"#http://siteanalytics\\.compete\\.com/.*#i",
		"#http://statsheet\\.com/statplot/charts/.*/.*/.*/.*#i", "#http://statsheet\\.com/statplot/charts/e/.*#i", "#http://statsheet\\.com/.*/teams/.*/.*#i", "#http://statsheet\\.com/tools/chartlets\\?chart=.*#i",
		"#http://.*\\.status\\.net/notice/.*#i",
		"#http://identi\\.ca/notice/.*#i",
		"#http://brainbird\\.net/notice/.*#i",
		"#http://shitmydadsays\\.com/notice/.*#i",
		"#http://www\\.studivz\\.net/Profile/.*#i", "#http://www\\.studivz\\.net/l/.*#i", "#http://www\\.studivz\\.net/Groups/Overview/.*#i", "#http://www\\.studivz\\.net/Gadgets/Info/.*#i", "#http://www\\.studivz\\.net/Gadgets/Install/.*#i", "#http://www\\.studivz\\.net/.*#i", "#http://www\\.meinvz\\.net/Profile/.*#i", "#http://www\\.meinvz\\.net/l/.*#i", "#http://www\\.meinvz\\.net/Groups/Overview/.*#i", "#http://www\\.meinvz\\.net/Gadgets/Info/.*#i", "#http://www\\.meinvz\\.net/Gadgets/Install/.*#i", "#http://www\\.meinvz\\.net/.*#i", "#http://www\\.schuelervz\\.net/Profile/.*#i", "#http://www\\.schuelervz\\.net/l/.*#i", "#http://www\\.schuelervz\\.net/Groups/Overview/.*#i", "#http://www\\.schuelervz\\.net/Gadgets/Info/.*#i", "#http://www\\.schuelervz\\.net/Gadgets/Install/.*#i", "#http://www\\.schuelervz\\.net/.*#i",
		"#http://myloc\\.me/.*#i",
		"#http://pastebin\\.com/.*#i",
		"#http://pastie\\.org/.*#i", "#http://www\\.pastie\\.org/.*#i",
		"#http://redux\\.com/stream/item/.*/.*#i", "#http://redux\\.com/f/.*/.*#i", "#http://www\\.redux\\.com/stream/item/.*/.*#i", "#http://www\\.redux\\.com/f/.*/.*#i",
		"#http://cl\\.ly/.*#i", "#http://cl\\.ly/.*/content#i",
		"#http://speakerdeck\\.com/u/.*/p/.*#i",
		"#http://www\\.kiva\\.org/lend/.*#i",
		"#http://www\\.timetoast\\.com/timelines/.*#i",
		"#http://storify\\.com/.*/.*#i",
		"#http://.*meetup\\.com/.*#i", "#http://meetu\\.ps/.*#i",
		"#http://www\\.dailymile\\.com/people/.*/entries/.*#i",
		"#http://.*\\.kinomap\\.com/.*#i",
		"#http://www\\.metacdn\\.com/r/c/.*/.*#i", "#http://www\\.metacdn\\.com/r/m/.*/.*#i",
		"#http://prezi\\.com/.*/.*#i",
		"#http://.*\\.uservoice\\.com/.*/suggestions/.*#i",
		"#http://formspring\\.me/.*#i", "#http://www\\.formspring\\.me/.*#i", "#http://formspring\\.me/.*/q/.*#i", "#http://www\\.formspring\\.me/.*/q/.*#i",
		"#http://twitlonger\\.com/show/.*#i", "#http://www\\.twitlonger\\.com/show/.*#i", "#http://tl\\.gd/.*#i",
		"#http://www\\.qwiki\\.com/q/.*#i",
		"#http://crocodoc\\.com/.*#i", "#http://.*\\.crocodoc\\.com/.*#i", "#https://crocodoc\\.com/.*#i", "#https://.*\\.crocodoc\\.com/.*#i",
		"#http://www\\.wikipedia\\.org/wiki/.*#i", "#http://.*\\.wikipedia\\.org/wiki/.*#i",
		"#http://www\\.wikimedia\\.org/wiki/File.*#i",
		"#https://urtak\\.com/u/.*#i", "#https://urtak\\.com/clr/.*#i",
		"#http://graphicly\\.com/.*/.*/.*#i",
		"#https://ganxy\\.com/.*#i", "#https://www\\.ganxy\\.com/.*#i",
		"#http://gopollgo\\.com/.*#i", "#http://www\\.gopollgo\\.com/.*#i",
		"#http://360\\.io/.*#i",
		"#http://.*yfrog\\..*/.*#i",
		"#http://twitter\\.com/.*/status/.*/photo/.*#i", "#http://twitter\\.com/.*/statuses/.*/photo#i", "#http://pic\\.twitter\\.com/.*#i", "#http://www\\.twitter\\.com/.*/statuses/.*/photo/.*#i", "#http://mobile\\.twitter\\.com/.*/status/.*/photo/.*#i", "#http://mobile\\.twitter\\.com/.*/statuses/.*/photo/.*#i", "#https://twitter\\.com/.*/status/.*/photo/.*#i", "#https://twitter\\.com/.*/statuses/.*/photo/.*#i", "#https://www\\.twitter\\.com/.*/status/.*/photo/.*#i", "#https://www\\.twitter\\.com/.*/statuses/.*/photo/.*#i", "#https://mobile\\.twitter\\.com/.*/status/.*/photo/.*#i", "#https://mobile\\.twitter\\.com/.*/statuses/.*/photo/.*#i",
		"#http://www\\.flickr\\.com/photos/.*#i", "#http://flic\\.kr/.*#i",
		"#http://twitpic\\.com/.*#i", "#http://www\\.twitpic\\.com/.*#i", "#http://twitpic\\.com/photos/.*#i", "#http://www\\.twitpic\\.com/photos/.*#i",
		"#http://.*imgur\\.com/.*#i",
		"#http://.*\\.posterous\\.com/.*#i", "#http://post\\.ly/.*#i",
		"#http://twitgoo\\.com/.*#i",
		"#http://i.*\\.photobucket\\.com/albums/.*#i", "#http://s.*\\.photobucket\\.com/albums/.*#i", "#http://media\\.photobucket\\.com/image/.*#i",
		"#http://phodroid\\.com/.*/.*/.*#i",
		"#http://www\\.mobypicture\\.com/user/.*/view/.*#i", "#http://moby\\.to/.*#i",
		"#http://xkcd\\.com/.*#i", "#http://www\\.xkcd\\.com/.*#i", "#http://imgs\\.xkcd\\.com/.*#i",
		"#http://www\\.asofterworld\\.com/index\\.php\\?id=.*#i", "#http://www\\.asofterworld\\.com/.*\\.jpg#i", "#http://asofterworld\\.com/.*\\.jpg#i",
		"#http://www\\.qwantz\\.com/index\\.php\\?comic=.*#i",
		"#http://23hq\\.com/.*/photo/.*#i", "#http://www\\.23hq\\.com/.*/photo/.*#i",
		"#http://.*dribbble\\.com/shots/.*#i", "#http://drbl\\.in/.*#i",
		"#http://.*\\.smugmug\\.com/.*#i", "#http://.*\\.smugmug\\.com/.*\\#.*#i",
		"#http://emberapp\\.com/.*/images/.*#i", "#http://emberapp\\.com/.*/images/.*/sizes/.*#i", "#http://emberapp\\.com/.*/collections/.*/.*#i", "#http://emberapp\\.com/.*/categories/.*/.*/.*#i", "#http://embr\\.it/.*#i",
		"#http://picasaweb\\.google\\.com.*/.*/.*\\#.*#i", "#http://picasaweb\\.google\\.com.*/lh/photo/.*#i", "#http://picasaweb\\.google\\.com.*/.*/.*#i",
		"#http://dailybooth\\.com/.*/.*#i",
		"#http://brizzly\\.com/pic/.*#i", "#http://pics\\.brizzly\\.com/.*\\.jpg#i",
		"#http://img\\.ly/.*#i",
		"#http://www\\.tinypic\\.com/view\\.php.*#i", "#http://tinypic\\.com/view\\.php.*#i", "#http://www\\.tinypic\\.com/player\\.php.*#i", "#http://tinypic\\.com/player\\.php.*#i", "#http://www\\.tinypic\\.com/r/.*/.*#i", "#http://tinypic\\.com/r/.*/.*#i", "#http://.*\\.tinypic\\.com/.*\\.jpg#i", "#http://.*\\.tinypic\\.com/.*\\.png#i",
		"#http://meadd\\.com/.*/.*#i", "#http://meadd\\.com/.*#i",
		"#http://.*\\.deviantart\\.com/art/.*#i", "#http://.*\\.deviantart\\.com/gallery/.*#i", "#http://.*\\.deviantart\\.com/\\#/.*#i", "#http://fav\\.me/.*#i", "#http://.*\\.deviantart\\.com#i", "#http://.*\\.deviantart\\.com/gallery#i", "#http://.*\\.deviantart\\.com/.*/.*\\.jpg#i", "#http://.*\\.deviantart\\.com/.*/.*\\.gif#i", "#http://.*\\.deviantart\\.net/.*/.*\\.jpg#i", "#http://.*\\.deviantart\\.net/.*/.*\\.gif#i",
		"#http://www\\.fotopedia\\.com/.*/.*#i", "#http://fotopedia\\.com/.*/.*#i",
		"#http://photozou\\.jp/photo/show/.*/.*#i", "#http://photozou\\.jp/photo/photo_only/.*/.*#i",
		"#http://instagr\\.am/p/.*#i", "#http://instagram\\.com/p/.*#i",
		"#http://skitch\\.com/.*/.*/.*#i", "#http://img\\.skitch\\.com/.*#i", "#https://skitch\\.com/.*/.*/.*#i", "#https://img\\.skitch\\.com/.*#i",
		"#http://share\\.ovi\\.com/media/.*/.*#i",
		"#http://www\\.questionablecontent\\.net/#i", "#http://questionablecontent\\.net/#i", "#http://www\\.questionablecontent\\.net/view\\.php.*#i", "#http://questionablecontent\\.net/view\\.php.*#i", "#http://questionablecontent\\.net/comics/.*\\.png#i", "#http://www\\.questionablecontent\\.net/comics/.*\\.png#i",
		"#http://twitrpix\\.com/.*#i", "#http://.*\\.twitrpix\\.com/.*#i",
		"#http://www\\.someecards\\.com/.*/.*#i", "#http://someecards\\.com/.*/.*#i", "#http://some\\.ly/.*#i", "#http://www\\.some\\.ly/.*#i",
		"#http://pikchur\\.com/.*#i",
		"#http://achewood\\.com/.*#i", "#http://www\\.achewood\\.com/.*#i", "#http://achewood\\.com/index\\.php.*#i", "#http://www\\.achewood\\.com/index\\.php.*#i",
		"#http://www\\.whosay\\.com/content/.*#i", "#http://www\\.whosay\\.com/photos/.*#i", "#http://www\\.whosay\\.com/videos/.*#i", "#http://say\\.ly/.*#i",
		"#http://ow\\.ly/i/.*#i",
		"#http://color\\.com/s/.*#i",
		"#http://bnter\\.com/convo/.*#i",
		"#http://mlkshk\\.com/p/.*#i",
		"#http://lockerz\\.com/s/.*#i",
		"#http://lightbox\\.com/.*#i", "#http://www\\.lightbox\\.com/.*#i",
		"#http://pinterest\\.com/pin/.*#i",
		"#http://d\\.pr/i/.*#i",
		"#http://.*amazon\\..*/gp/product/.*#i", "#http://.*amazon\\..*/.*/dp/.*#i", "#http://.*amazon\\..*/dp/.*#i", "#http://.*amazon\\..*/o/ASIN/.*#i", "#http://.*amazon\\..*/gp/offer-listing/.*#i", "#http://.*amazon\\..*/.*/ASIN/.*#i", "#http://.*amazon\\..*/gp/product/images/.*#i", "#http://.*amazon\\..*/gp/aw/d/.*#i", "#http://www\\.amzn\\.com/.*#i", "#http://amzn\\.com/.*#i",
		"#http://www\\.shopstyle\\.com/browse.*#i", "#http://www\\.shopstyle\\.com/action/apiVisitRetailer.*#i", "#http://api\\.shopstyle\\.com/action/apiVisitRetailer.*#i", "#http://www\\.shopstyle\\.com/action/viewLook.*#i",
		"#http://itunes\\.apple\\.com/.*#i", "#https://itunes\\.apple\\.com/.*#i",
		"#http://soundcloud\\.com/.*#i", "#http://soundcloud\\.com/.*/.*#i", "#http://soundcloud\\.com/.*/sets/.*#i", "#http://soundcloud\\.com/groups/.*#i", "#http://snd\\.sc/.*#i",
		"#http://open\\.spotify\\.com/.*#i",
		"#http://www\\.last\\.fm/music/.*#i", "#http://www\\.last\\.fm/music/+videos/.*#i", "#http://www\\.last\\.fm/music/+images/.*#i", "#http://www\\.last\\.fm/music/.*/_/.*#i", "#http://www\\.last\\.fm/music/.*/.*#i",
		"#http://www\\.mixcloud\\.com/.*/.*/#i",
		"#http://www\\.radionomy\\.com/.*/radio/.*#i", "#http://radionomy\\.com/.*/radio/.*#i",
		"#http://www\\.hark\\.com/clips/.*#i",
		"#http://www\\.rdio\\.com/\\#/artist/.*/album/.*#i", "#http://www\\.rdio\\.com/artist/.*/album/.*#i",
		"#http://www\\.zero-inch\\.com/.*#i",
		"#http://.*\\.bandcamp\\.com/#i", "#http://.*\\.bandcamp\\.com/track/.*#i", "#http://.*\\.bandcamp\\.com/album/.*#i",
		"#http://freemusicarchive\\.org/music/.*#i", "#http://www\\.freemusicarchive\\.org/music/.*#i", "#http://freemusicarchive\\.org/curator/.*#i", "#http://www\\.freemusicarchive\\.org/curator/.*#i",
		"#http://www\\.npr\\.org/.*/.*/.*/.*/.*#i", "#http://www\\.npr\\.org/.*/.*/.*/.*/.*/.*#i", "#http://www\\.npr\\.org/.*/.*/.*/.*/.*/.*/.*#i", "#http://www\\.npr\\.org/templates/story/story\\.php.*#i",
		"#http://huffduffer\\.com/.*/.*#i",
		"#http://www\\.audioboo\\.fm/boos/.*#i", "#http://audioboo\\.fm/boos/.*#i", "#http://boo\\.fm/b.*#i",
		"#http://www\\.xiami\\.com/song/.*#i", "#http://xiami\\.com/song/.*#i",
		"#http://www\\.saynow\\.com/playMsg\\.html.*#i", "#http://www\\.saynow\\.com/playMsg\\.html.*#i",
		"#http://grooveshark\\.com/.*#i",
		"#http://radioreddit\\.com/songs.*#i", "#http://www\\.radioreddit\\.com/songs.*#i", "#http://radioreddit\\.com/\\?q=songs.*#i", "#http://www\\.radioreddit\\.com/\\?q=songs.*#i",
		"#http://www\\.gogoyoko\\.com/song/.*#i",
		"#http://espn\\.go\\.com/video/clip.*#i", "#http://espn\\.go\\.com/.*/story.*#i",
		"#http://abcnews\\.com/.*/video/.*#i", "#http://abcnews\\.com/video/playerIndex.*#i", "#http://abcnews\\.go\\.com/.*/video/.*#i", "#http://abcnews\\.go\\.com/video/playerIndex.*#i",
		"#http://washingtonpost\\.com/wp-dyn/.*/video/.*/.*/.*/.*#i", "#http://www\\.washingtonpost\\.com/wp-dyn/.*/video/.*/.*/.*/.*#i",
		"#http://www\\.boston\\.com/video.*#i", "#http://boston\\.com/video.*#i", "#http://www\\.boston\\.com/.*video.*#i", "#http://boston\\.com/.*video.*#i",
		"#http://www\\.facebook\\.com/photo\\.php.*#i", "#http://www\\.facebook\\.com/video/video\\.php.*#i", "#http://www\\.facebook\\.com/v/.*#i", "#https://www\\.facebook\\.com/photo\\.php.*#i", "#https://www\\.facebook\\.com/video/video\\.php.*#i", "#https://www\\.facebook\\.com/v/.*#i",
		"#http://cnbc\\.com/id/.*\\?.*video.*#i", "#http://www\\.cnbc\\.com/id/.*\\?.*video.*#i", "#http://cnbc\\.com/id/.*/play/1/video/.*#i", "#http://www\\.cnbc\\.com/id/.*/play/1/video/.*#i",
		"#http://cbsnews\\.com/video/watch/.*#i",
		"#http://plus\\.google\\.com/.*#i", "#http://www\\.google\\.com/profiles/.*#i", "#https://plus\\.google\\.com/.*#i", "#http://google\\.com/profiles/.*#i",
		"#http://www\\.cnn\\.com/video/.*#i",
		"#http://edition\\.cnn\\.com/video/.*#i",
		"#http://money\\.cnn\\.com/video/.*#i",
		"#http://today\\.msnbc\\.msn\\.com/id/.*/vp/.*#i", "#http://www\\.msnbc\\.msn\\.com/id/.*/vp/.*#i", "#http://www\\.msnbc\\.msn\\.com/id/.*/ns/.*#i", "#http://today\\.msnbc\\.msn\\.com/id/.*/ns/.*#i",
		"#http://www\\.globalpost\\.com/video/.*#i", "#http://www\\.globalpost\\.com/dispatch/.*#i",
		"#http://guardian\\.co\\.uk/.*/video/.*/.*/.*/.*#i", "#http://www\\.guardian\\.co\\.uk/.*/video/.*/.*/.*/.*#i",
		"#http://bravotv\\.com/.*/.*/videos/.*#i", "#http://www\\.bravotv\\.com/.*/.*/videos/.*#i",
		"#http://video\\.nationalgeographic\\.com/video/.*/.*/.*/.*#i",
		"#http://dsc\\.discovery\\.com/videos/.*#i", "#http://animal\\.discovery\\.com/videos/.*#i", "#http://health\\.discovery\\.com/videos/.*#i", "#http://investigation\\.discovery\\.com/videos/.*#i", "#http://military\\.discovery\\.com/videos/.*#i", "#http://planetgreen\\.discovery\\.com/videos/.*#i", "#http://science\\.discovery\\.com/videos/.*#i", "#http://tlc\\.discovery\\.com/videos/.*#i",
		"#http://video\\.forbes\\.com/fvn/.*#i");

	// Find URLs throughout the $feed, noting the tweet IDs they occur in
	$matched_urls = array();

	$i=0;
	foreach ($feed as &$status) 
	{	// Loop through the feed
		if ($status['entities'])	// If there are entities
		{
			$entities = $status['entities'];
			
			if($entities['links'])
			{
				foreach($entities['links'] as $urls) 
				{	
					foreach ($embedly_regex_array as $pattern) 
					{
						if (preg_match_all($pattern, $urls['url'], $matches, PREG_PATTERN_ORDER) > 0)
						{
							$matched_urls[$urls['url']][] = $i;
							break;	//	Once found, stop searching
						}
					}
				}
			}
		}
		$i++;	
	}
	
	// Make a single API call to Embedly.
	$justUrls = array_keys($matched_urls);
	$count = count($justUrls);
	if ($count == 0) return;
	if ($count > 20) 
	{
		// Embedly has a limit of 20 URLs processed at a time. Not ideal for @dabr, but fair enough to ignore images after that.
		$justUrls = array_chunk ($justUrls, 20);
		$justUrls = $justUrls[0];
	}
	$url = 'http://api.embed.ly/1/oembed?key='.EMBEDLY_KEY.'&urls=' . implode(',', $justUrls) . '&format=json';
	$embedly_json = dabr_fetch($url);
	$oembeds = json_decode($embedly_json);
	
	// Put the thumbnails into the $feed
	foreach ($justUrls as $index => $url) 
	{
		if ($thumb = $oembeds[$index]->thumbnail_url) 
		{
			$html = theme('external_link', urldecode($url), "<img src='" . IMAGE_PROXY_URL . "x50/" . $thumb . "' />");
			foreach ($matched_urls[$url] as $statusId) 
			{
				$feed[$statusId]['html'] =  $feed[$statusId]['html'] . '<br />' . $html;
			}
		}
	}
}
