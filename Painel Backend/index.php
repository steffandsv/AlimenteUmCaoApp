<?php



/** CONFIG:START **/
/** database **/
$config["host"]			= "host" ; 		//host
$config["user"]			= "user" ; 		//Username SQL
$config["pass"]			= "pass" ; 		//Password SQL
$config["dbase"]			= "banco_de_dados" ; 		//Database
/** admin **/
$config["email"]			= "admin" ; 		//email for login
$config["password"]			= "admin" ; 		//password
$config["utf8"]			= true; 		
$config["theme"]			= "paper" ;		// theme name you can get here https://www.bootstrapcdn.com/bootswatch/
$config["navbar"]		= "nav-bar" ; 		// nav-bar or nav-stacked
/** file **/
$config["image_allowed"]		= array("jpeg", "jpg", "png", "gif");
$config["media_allowed"]		= array("mp3", "mp4", "avi", "wav");
$config["file_allowed"]		= array("zip");
$config["kcfinder"]		= false;		// download kcfinder from from http://kcfinder.sunhater.com then unzip with filename `kcfinder`, no need configuration (the key folder name `kcfinder` is exist)
/** CONFIG:END **/


/** LANGGUAGE:START **/
define("LANG_MAP","Maps");
define("LANG_NOTICIAS","Noticiass");
define("LANG_NID","Nids");
define("LANG_TITLE","Titles");
define("LANG_LOCATION","Locations");
define("LANG_DESCRIPTION","Descriptions");
define("LANG_ID","Ids");
define("LANG_TITULO","Titulos");
define("LANG_FOTO","Fotos");
define("LANG_NOTICIA","Noticias");
define("LANG_FILE_BROWSER","File Browser");
define("LANG_IMAGES","Images");
define("LANG_FILES","Files");
define("LANG_MEDIAS","Media");
define("LANG_HOME","Home");
define("LANG_ADD","Add");
define("LANG_DELETE","Delete");
define("LANG_LIST","List");
define("LANG_EDIT","Edit");
define("LANG_MORE","More");
/** LANGGUAGE:END **/


session_start();
$_SESSION["KCFINDER"]["disabled"] = true; //terminate filebrowser
if(!isset($_SESSION["IS_ADMIN"])){
	$_SESSION["IS_ADMIN"] = false;
}
if(file_exists("kcfinder/browse.php")){

$config["kcfinder"]		= true;
}
if($config["navbar"] == "nav-bar"){
	$sidebaleft = 12;
	$sidebaright = 12;
}else{
	$config["navbar"] = "nav-stacked";
	$sidebaleft = 3;
	$sidebaright = 9;
}
//filebrowser enable if admin
if($_SESSION["IS_ADMIN"] == true){
	$_SESSION["KCFINDER"] = array();
	$_SESSION["KCFINDER"]["disabled"] = false;
	$_SESSION["KCFINDER"]["uploadURL"] = "../media";
	$_SESSION["KCFINDER"]["filenameChangeChars"] = array(" "=>"-",":"=>".");
	$_SESSION["KCFINDER"]["dirnameChangeChars"] = array(" "=>"-",":"=>".");
	$_SESSION["KCFINDER"]["denyUpdateCheck"] = true;
	$_SESSION["KCFINDER"]["denyExtensionRename"] = true;
	$_SESSION["KCFINDER"]["types"]["media"] = implode(" ",$config["media_allowed"]);
	$_SESSION["KCFINDER"]["types"]["image"] = implode(" ",$config["image_allowed"]);
	$_SESSION["KCFINDER"]["types"]["file"] = implode(" ",$config["file_allowed"]);
}else{
//terminate filebrowser
	$_SESSION["KCFINDER"]["disabled"] = true;
}
$js_for_relation ="";
	$get_dir = explode("/", $_SERVER["PHP_SELF"]);
	unset($get_dir[count($get_dir)-1]);
	$main_url = "http://" . $_SERVER["HTTP_HOST"] ;
	$full_url = $main_url . implode("/",$get_dir);
if(isset($_POST["email"]) && isset($_POST["password"])){
	if(($_POST["email"]==$config["email"]) && ($_POST["password"]==$config["password"])){
		$_SESSION["IS_ADMIN"] = true;
		header("Location: ?table=home");
	}
}
if(!isset($_GET["table"])){
	$_GET["table"] = "home";
}
if(isset($_GET["logout"])){
	$_SESSION["IS_ADMIN"] = false;
	$_SESSION["KCFINDER"]["disabled"] = true; //terminate filebrowser
	header("Location: ?clear");
}
$tags_html=null;
if($_SESSION["IS_ADMIN"]==true){
	/** connect to mysql **/
	$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["dbase"]);
	if (mysqli_connect_errno()){
		die(mysqli_connect_error());
	}
	
	if($config["utf8"]==true){
		$mysql->set_charset("utf8");
	}
	
	/** prepare notice **/
	$notice = null;
	
	/** no action **/
	if(!isset($_GET["action"])){
		$_GET["action"] = "list";
	}
	
	/** create ionicon **/
	$ionicons = "alert,alert-circled,android-add,android-add-circle,android-alarm-clock,android-alert,android-apps,android-archive,android-arrow-back,android-arrow-down,android-arrow-dropdown,android-arrow-dropdown-circle,android-arrow-dropleft,android-arrow-dropleft-circle,android-arrow-dropright,android-arrow-dropright-circle,android-arrow-dropup,android-arrow-dropup-circle,android-arrow-forward,android-arrow-up,android-attach,android-bar,android-bicycle,android-boat,android-bookmark,android-bulb,android-bus,android-calendar,android-call,android-camera,android-cancel,android-car,android-cart,android-chat,android-checkbox,android-checkbox-blank,android-checkbox-outline,android-checkbox-outline-blank,android-checkmark-circle,android-clipboard,android-close,android-cloud,android-cloud-circle,android-cloud-done,android-cloud-outline,android-color-palette,android-compass,android-contact,android-contacts,android-contract,android-create,android-delete,android-desktop,android-document,android-done,android-done-all,android-download,android-drafts,android-exit,android-expand,android-favorite,android-favorite-outline,android-film,android-folder,android-folder-open,android-funnel,android-globe,android-hand,android-hangout,android-happy,android-home,android-image,android-laptop,android-list,android-locate,android-lock,android-mail,android-map,android-menu,android-microphone,android-microphone-off,android-more-horizontal,android-more-vertical,android-navigate,android-notifications,android-notifications-none,android-notifications-off,android-open,android-options,android-people,android-person,android-person-add,android-phone-landscape,android-phone-portrait,android-pin,android-plane,android-playstore,android-print,android-radio-button-off,android-radio-button-on,android-refresh,android-remove,android-remove-circle,android-restaurant,android-sad,android-search,android-send,android-settings,android-share,android-share-alt,android-star,android-star-half,android-star-outline,android-stopwatch,android-subway,android-sunny,android-sync,android-textsms,android-time,android-train,android-unlock,android-upload,android-volume-down,android-volume-mute,android-volume-off,android-volume-up,android-walk,android-warning,android-watch,android-wifi,aperture,archive,arrow-down-a,arrow-down-b,arrow-down-c,arrow-expand,arrow-graph-down-left,arrow-graph-down-right,arrow-graph-up-left,arrow-graph-up-right,arrow-left-a,arrow-left-b,arrow-left-c,arrow-move,arrow-resize,arrow-return-left,arrow-return-right,arrow-right-a,arrow-right-b,arrow-right-c,arrow-shrink,arrow-swap,arrow-up-a,arrow-up-b,arrow-up-c,asterisk,at,backspace,backspace-outline,bag,battery-charging,battery-empty,battery-full,battery-half,battery-low,beaker,beer,bluetooth,bonfire,bookmark,bowtie,briefcase,bug,calculator,calendar,camera,card,cash,chatbox,chatbox-working,chatboxes,chatbubble,chatbubble-working,chatbubbles,checkmark,checkmark-circled,checkmark-round,chevron-down,chevron-left,chevron-right,chevron-up,clipboard,clock,close,close-circled,close-round,closed-captioning,cloud,code,code-download,code-working,coffee,compass,compose,connection-bars,contrast,crop,cube,disc,document,document-text,drag,earth,easel,edit,egg,eject,email,email-unread,erlenmeyer-flask,erlenmeyer-flask-bubbles,eye,eye-disabled,female,filing,film-marker,fireball,flag,flame,flash,flash-off,folder,fork,fork-repo,forward,funnel,gear-a,gear-b,grid,hammer,happy,happy-outline,headphone,heart,heart-broken,help,help-buoy,help-circled,home,icecream,image,images,information,information-circled,ionic,ios-alarm,ios-alarm-outline,ios-albums,ios-albums-outline,ios-americanfootball,ios-americanfootball-outline,ios-analytics,ios-analytics-outline,ios-arrow-back,ios-arrow-down,ios-arrow-forward,ios-arrow-left,ios-arrow-right,ios-arrow-thin-down,ios-arrow-thin-left,ios-arrow-thin-right,ios-arrow-thin-up,ios-arrow-up,ios-at,ios-at-outline,ios-barcode,ios-barcode-outline,ios-baseball,ios-baseball-outline,ios-basketball,ios-basketball-outline,ios-bell,ios-bell-outline,ios-body,ios-body-outline,ios-bolt,ios-bolt-outline,ios-book,ios-book-outline,ios-bookmarks,ios-bookmarks-outline,ios-box,ios-box-outline,ios-briefcase,ios-briefcase-outline,ios-browsers,ios-browsers-outline,ios-calculator,ios-calculator-outline,ios-calendar,ios-calendar-outline,ios-camera,ios-camera-outline,ios-cart,ios-cart-outline,ios-chatboxes,ios-chatboxes-outline,ios-chatbubble,ios-chatbubble-outline,ios-checkmark,ios-checkmark-empty,ios-checkmark-outline,ios-circle-filled,ios-circle-outline,ios-clock,ios-clock-outline,ios-close,ios-close-empty,ios-close-outline,ios-cloud,ios-cloud-download,ios-cloud-download-outline,ios-cloud-outline,ios-cloud-upload,ios-cloud-upload-outline,ios-cloudy,ios-cloudy-night,ios-cloudy-night-outline,ios-cloudy-outline,ios-cog,ios-cog-outline,ios-color-filter,ios-color-filter-outline,ios-color-wand,ios-color-wand-outline,ios-compose,ios-compose-outline,ios-contact,ios-contact-outline,ios-copy,ios-copy-outline,ios-crop,ios-crop-strong,ios-download,ios-download-outline,ios-drag,ios-email,ios-email-outline,ios-eye,ios-eye-outline,ios-fastforward,ios-fastforward-outline,ios-filing,ios-filing-outline,ios-film,ios-film-outline,ios-flag,ios-flag-outline,ios-flame,ios-flame-outline,ios-flask,ios-flask-outline,ios-flower,ios-flower-outline,ios-folder,ios-folder-outline,ios-football,ios-football-outline,ios-game-controller-a,ios-game-controller-a-outline,ios-game-controller-b,ios-game-controller-b-outline,ios-gear,ios-gear-outline,ios-glasses,ios-glasses-outline,ios-grid-view,ios-grid-view-outline,ios-heart,ios-heart-outline,ios-help,ios-help-empty,ios-help-outline,ios-home,ios-home-outline,ios-infinite,ios-infinite-outline,ios-information,ios-information-empty,ios-information-outline,ios-ionic-outline,ios-keypad,ios-keypad-outline,ios-lightbulb,ios-lightbulb-outline,ios-list,ios-list-outline,ios-location,ios-location-outline,ios-locked,ios-locked-outline,ios-loop,ios-loop-strong,ios-medical,ios-medical-outline,ios-medkit,ios-medkit-outline,ios-mic,ios-mic-off,ios-mic-outline,ios-minus,ios-minus-empty,ios-minus-outline,ios-monitor,ios-monitor-outline,ios-moon,ios-moon-outline,ios-more,ios-more-outline,ios-musical-note,ios-musical-notes,ios-navigate,ios-navigate-outline,ios-nutrition,ios-nutrition-outline,ios-paper,ios-paper-outline,ios-paperplane,ios-paperplane-outline,ios-partlysunny,ios-partlysunny-outline,ios-pause,ios-pause-outline,ios-paw,ios-paw-outline,ios-people,ios-people-outline,ios-person,ios-person-outline,ios-personadd,ios-personadd-outline,ios-photos,ios-photos-outline,ios-pie,ios-pie-outline,ios-pint,ios-pint-outline,ios-play,ios-play-outline,ios-plus,ios-plus-empty,ios-plus-outline,ios-pricetag,ios-pricetag-outline,ios-pricetags,ios-pricetags-outline,ios-printer,ios-printer-outline,ios-pulse,ios-pulse-strong,ios-rainy,ios-rainy-outline,ios-recording,ios-recording-outline,ios-redo,ios-redo-outline,ios-refresh,ios-refresh-empty,ios-refresh-outline,ios-reload,ios-reverse-camera,ios-reverse-camera-outline,ios-rewind,ios-rewind-outline,ios-rose,ios-rose-outline,ios-search,ios-search-strong,ios-settings,ios-settings-strong,ios-shuffle,ios-shuffle-strong,ios-skipbackward,ios-skipbackward-outline,ios-skipforward,ios-skipforward-outline,ios-snowy,ios-speedometer,ios-speedometer-outline,ios-star,ios-star-half,ios-star-outline,ios-stopwatch,ios-stopwatch-outline,ios-sunny,ios-sunny-outline,ios-telephone,ios-telephone-outline,ios-tennisball,ios-tennisball-outline,ios-thunderstorm,ios-thunderstorm-outline,ios-time,ios-time-outline,ios-timer,ios-timer-outline,ios-toggle,ios-toggle-outline,ios-trash,ios-trash-outline,ios-undo,ios-undo-outline,ios-unlocked,ios-unlocked-outline,ios-upload,ios-upload-outline,ios-videocam,ios-videocam-outline,ios-volume-high,ios-volume-low,ios-wineglass,ios-wineglass-outline,ios-world,ios-world-outline,ipad,iphone,ipod,jet,key,knife,laptop,leaf,levels,lightbulb,link,load-a,load-b,load-c,load-d,location,lock-combination,locked,log-in,log-out,loop,magnet,male,man,map,medkit,merge,mic-a,mic-b,mic-c,minus,minus-circled,minus-round,model-s,monitor,more,mouse,music-note,navicon,navicon-round,navigate,network,no-smoking,nuclear,outlet,paintbrush,paintbucket,paper-airplane,paperclip,pause,person,person-add,person-stalker,pie-graph,pin,pinpoint,pizza,plane,planet,play,playstation,plus,plus-circled,plus-round,podium,pound,power,pricetag,pricetags,printer,pull-request,qr-scanner,quote,radio-waves,record,refresh,reply,reply-all,ribbon-a,ribbon-b,sad,sad-outline,scissors,search,settings,share,shuffle,skip-backward,skip-forward,social-android,social-android-outline,social-angular,social-angular-outline,social-apple,social-apple-outline,social-bitcoin,social-bitcoin-outline,social-buffer,social-buffer-outline,social-chrome,social-chrome-outline,social-codepen,social-codepen-outline,social-css3,social-css3-outline,social-designernews,social-designernews-outline,social-dribbble,social-dribbble-outline,social-dropbox,social-dropbox-outline,social-euro,social-euro-outline,social-facebook,social-facebook-outline,social-foursquare,social-foursquare-outline,social-freebsd-devil,social-github,social-github-outline,social-google,social-google-outline,social-googleplus,social-googleplus-outline,social-hackernews,social-hackernews-outline,social-html5,social-html5-outline,social-instagram,social-instagram-outline,social-javascript,social-javascript-outline,social-linkedin,social-linkedin-outline,social-markdown,social-nodejs,social-octocat,social-pinterest,social-pinterest-outline,social-python,social-reddit,social-reddit-outline,social-rss,social-rss-outline,social-sass,social-skype,social-skype-outline,social-snapchat,social-snapchat-outline,social-tumblr,social-tumblr-outline,social-tux,social-twitch,social-twitch-outline,social-twitter,social-twitter-outline,social-usd,social-usd-outline,social-vimeo,social-vimeo-outline,social-whatsapp,social-whatsapp-outline,social-windows,social-windows-outline,social-wordpress,social-wordpress-outline,social-yahoo,social-yahoo-outline,social-yen,social-yen-outline,social-youtube,social-youtube-outline,soup-can,soup-can-outline,speakerphone,speedometer,spoon,star,stats-bars,steam,stop,thermometer,thumbsdown,thumbsup,toggle,toggle-filled,transgender,trash-a,trash-b,trophy,tshirt,tshirt-outline,umbrella,university,unlocked,upload,usb,videocamera,volume-high,volume-low,volume-medium,volume-mute,wand,waterdrop,wifi,wineglass,woman,wrench,xbox";
	$ionicon_dialog = null;
	$ionicon_dialog .= "<div id=\"ionicons\" class=\"modal fade\" aria-labelledby=\"Ionicons\" aria-hidden=\"true\">"."\r\n";
	$ionicon_dialog .= "<div class=\"modal-dialog\">"."\r\n";
	$ionicon_dialog .= "<div class=\"modal-content\">"."\r\n";
	$ionicon_dialog .= "<div class=\"modal-header\">"."\r\n";
	$ionicon_dialog .= "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>";
	$ionicon_dialog .= "<h4 class=\"modal-title\">Ionicons</h4>"."\r\n";
	$ionicon_dialog .= "</div>"."\r\n";
	$ionicon_dialog .= "<div class=\"modal-body\">"."\r\n";
	$ionicon_dialog .= "<div style=\"width:100%;height:360px;overflow-x:scroll;\">"."\r\n";
	$ionicon_dialog .= "<div class=\"width:99%;\" >"."\r\n";
	foreach(explode(",",$ionicons) as $icon){
		$ionicon_dialog .= "<div class=\"col-lg-1 col-md-1 col-sm-1 col-xs-1\"><a href=\"#ion-".$icon."\" onclick=\"ionicons('ion-".$icon."');\" style=\"font-size:28px\" ><i class=\"ion ion-".$icon."\"></i></a></div>"."\r\n";
	}
	$ionicon_dialog .="</div>"."\r\n";
	$ionicon_dialog .="</div>"."\r\n";
	$ionicon_dialog .="</div>"."\r\n";
	$ionicon_dialog .="</div>"."\r\n";
	$ionicon_dialog .="</div>"."\r\n";
	$ionicon_dialog .="</div>"."\r\n";
	
	$tags_html .= "<div class=\"container-fluid\">" ;
	$tags_html .= "<div class=\"header\">" ;
	$tags_html .= "<ul class=\"nav nav-pills pull-right\">" ;
	$tags_html .= "<li><a href=\"?logout\">Logout</a></li>" ;
	$tags_html .= "</ul>" ;
	$tags_html .= "<h3 class=\"text-muted\">GMAP Test</h3>" ;
	$tags_html .= "<p>Testing Template Listing gMAP (default features and no coding)</p>" ;
	$tags_html .= "</div>" ;
	$tags_html .= "<div class=\"row\">" ;
	$tags_html .= "<div class=\"col-md-".$sidebaleft."\">" ;
	$tags_html .= "<div class=\"panel panel-default\">" ;
	$tags_html .= "<div class=\"panel-body\">" ;
	$tags_html .= "<ul class=\"nav nav-pills ".$config["navbar"]."\">" ;
	if($_GET["table"]=="home"){
		$tags_html .= "<li class=\"active\"><a href='?table=home'>".@LANG_HOME."</a></li>" ;
	}else{
		$tags_html .= "<li><a href='?table=home'>".@LANG_HOME."</a></li>" ;
	}
	if($_GET["table"]=="maps"){
		$tags_html .= "<li class=\"active\"><a href='?table=maps'>". @LANG_MAP."</a></li>" ;
	}else{
		$tags_html .= "<li><a href='?table=maps'>". @LANG_MAP."</a></li>" ;
	}
	if($_GET["table"]=="noticiass"){
		$tags_html .= "<li class=\"active\"><a href='?table=noticiass'>". @LANG_NOTICIAS."</a></li>" ;
	}else{
		$tags_html .= "<li><a href='?table=noticiass'>". @LANG_NOTICIAS."</a></li>" ;
	}
	if($config["kcfinder"]==true){
		if($_GET["table"]=="filebrowser"){
			$tags_html .= "<li class=\"active\"><a href='?table=filebrowser'>".@LANG_FILE_BROWSER."</a></li>" ;
		}else{
			$tags_html .= "<li><a href='?table=filebrowser'>".@LANG_FILE_BROWSER."</a></li>" ;
		}
	}
	$tags_html .= "</ul>" ;
	$tags_html .= "</div>" ;
	$tags_html .= "</div>" ;
	$tags_html .= "</div>" ;
	$tags_html .= "<div class=\"col-md-".$sidebaright."\">" ;
	$tags_html .= "<div class=\"panel panel-default\">" ;
	$tags_html .= "<div class=\"panel-body\">" ;
	switch($_GET["table"]){
		case "home":
					$tags_html .= "<h4 class=\"page-title\">GMAP Test</h4>" ;
					$tags_html .= "<div class=\"row\">" ;
					$tags_html .= "<div class=\"col-md-4\">" ;
					$tags_html .= "<div class=\"panel panel-primary\" >" ;
					$tags_html .= "<div class=\"panel-heading\">" ;
					$tags_html .= "<h4  class=\"panel-title\"><span class=\"glyphicon glyphicon-list-alt\"></span> ". @LANG_MAP ."</h4>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "<div class=\"panel-body\" style=\"min-height: 150px;\">" ;
					$tags_html .= "<ul class=\"list\">" ;
					/** fetch data from mysql **/
					$sql_query = "SELECT * FROM `map` ORDER BY `nid`  DESC LIMIT 0 , 5" ;
					if($result = $mysql->query($sql_query)){
						while ($data = $result->fetch_array()){
							$tags_html .= "<li>" . htmlentities(stripslashes($data["title"])) . "</li>" ;
						}
					}
					$tags_html .= "</ul>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "<div class=\"panel-footer text-right\"><a href=\"?table=maps\" class=\"btn btn-sm btn-primary\">".LANG_MORE."</a></div>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "<div class=\"col-md-4\">" ;
					$tags_html .= "<div class=\"panel panel-success\" >" ;
					$tags_html .= "<div class=\"panel-heading\">" ;
					$tags_html .= "<h4  class=\"panel-title\"><span class=\"glyphicon glyphicon-list-alt\"></span> ". @LANG_NOTICIAS ."</h4>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "<div class=\"panel-body\" style=\"min-height: 150px;\">" ;
					$tags_html .= "<ul class=\"list\">" ;
					/** fetch data from mysql **/
					$sql_query = "SELECT * FROM `noticias` ORDER BY `id`  DESC LIMIT 0 , 5" ;
					if($result = $mysql->query($sql_query)){
						while ($data = $result->fetch_array()){
							$tags_html .= "<li>" . htmlentities(stripslashes($data["titulo"])) . "</li>" ;
						}
					}
					$tags_html .= "</ul>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "<div class=\"panel-footer text-right\"><a href=\"?table=noticiass\" class=\"btn btn-sm btn-success\">".LANG_MORE."</a></div>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "</div>" ;
					$tags_html .= "</div>" ;
		break;
		// TODO: Maps
		case "maps":
					$tags_html .= "<h4>". @LANG_MAP ."</h4>" ;
				switch($_GET["action"]){
					// TODO: ---- listing
					case "list":
					$tags_html .= "<ul class=\"nav nav-tabs\">" ;
					$tags_html .= "<li class=\"active\"><a href=\"?table=maps\">".@LANG_LIST."</a></li>" ;
					$tags_html .= "<li><a href=\"?table=maps&action=add\">".@LANG_ADD."</a></li>" ;
					$tags_html .= "</ul>" ;
					$tags_html .= "<br/>" ;
					$tags_html .= "<div class=\"table-responsive\">" ;
					$table_html = "<table id=\"datatable\" class=\"table table-striped table-hover\">" ;
					$table_html .= "<thead>" ;
					$table_html .= "<tr>" ;
					$table_html .= "<th>".@LANG_TITLE."</th>" ;
					$table_html .= "<th>".@LANG_LOCATION."</th>" ;
					$table_html .= "<th>".@LANG_DESCRIPTION."</th>" ;
					$table_html .= "<th style=\"width:100px;\">Action</th>" ;
					$table_html .= "</tr>" ;
					$table_html .= "</thead>" ;
					$table_html .= "<tbody>" ;
					/** fetch data from mysql **/
					$sql_query = "SELECT * FROM `map`" ;
					if($result = $mysql->query($sql_query)){
						while ($data = $result->fetch_array()){
							$table_html .= "<tr>" ;
							$table_html .= "<td>" . htmlentities(stripslashes($data["title"])) . "</td>" ;
							$table_html .= "<td>" . htmlentities(stripslashes($data["location"])) . "</td>" ;
							$content_html = substr( strip_tags($data["Description"]),0,50);
							$table_html .= "<td>" . stripslashes($content_html). "...</td>" ;
							$table_html .= "<td>" ;
							$table_html .= "<div class=\"btn-group\" >" ;
							$table_html .= "<a class=\"btn btn-sm btn-warning\" href=\"?table=maps&action=edit&id=". $data["nid"]. "\"><span class=\"glyphicon glyphicon-pencil\"></span></a> " ;
							$table_html .= "<a onclick=\"return confirm('Are you sure you want to delete ID#". $data["nid"]. "')\" class=\"btn btn-sm btn-danger\" href=\"?table=maps&action=delete&id=". $data["nid"]. "\"><span class=\"glyphicon glyphicon-trash\"></span></a> " ;
							$table_html .= "</div>" ;
							$table_html .= "</td>" ;
							$table_html .= "</tr>" ;
							$table_html .= "\r\n" ;
						}
						$result->close();
					}
				$table_html .= "</tbody>" ;
				$table_html .= "</table>" ;
				$table_html .= "</div>" ;
				$tags_html .= $table_html;
				break;
			case "add":
				// TODO: ---- add
				$tags_html .= "<ul class=\"nav nav-tabs\">" ;
				$tags_html .= "<li><a href=\"?table=maps&action=list\">".@LANG_LIST."</a></li>" ;
				$tags_html .= "<li class=\"active\"><a href=\"?table=maps&action=add\">".@LANG_ADD."</a></li>" ;
				$tags_html .= "</ul>" ;
				$tags_html .= "<br/>" ;
	
				/** push button **/
				if(isset($_POST["add"])){
	
					/** avoid error **/
					$data_title = "";
					$data_location = "";
					$data_Description = "";
	
					/** get input **/
					if(isset($_POST["title"])){
						$data_title = addslashes($_POST["title"]);
					}
					if(isset($_POST["location"])){
						$data_location = addslashes($_POST["location"]);
					}
					if(isset($_POST["Description"])){
						$data_Description = addslashes($_POST["Description"]);
					}
	
					/** prepare save to mysql **/
					$sql_query = "INSERT INTO `map` (`title`,`location`,`Description`) VALUES ('$data_title','$data_location','$data_Description')" ;
					$stmt = $mysql->prepare($sql_query);
					$stmt->execute();
					$stmt->close();
					header("Location: ?table=maps&action=list");
				}
	
	
				/** Create Form **/
				$tags_html .= '
				<form action="" method="post" enctype="multipart/form-data">
					

<!--
// TODO: ----|-- form : NID
-->


<!--
// TODO: ----|-- form : TITLE
-->
<div class="form-group">
	<label for="title">' . @LANG_TITLE. '</label>
	<input id="title" class="form-control" data-type="heading-1" type="text" name="title" placeholder="" />
	<p class="help-block"></p>
</div>


<!--
// TODO: ----|-- form : LOCATION
-->
<div class="form-group">
	<label for="location">' . @LANG_LOCATION. '</label>
	<input id="location" class="form-control" data-type="gmap" type="text" name="location" placeholder="" />
	<p class="help-block"></p>
</div>


<!--
// TODO: ----|-- form : DESCRIPTION
-->
<div class="form-group">
	<label for="Description">' . @LANG_DESCRIPTION. '</label>
	<textarea id="Description" class="form-control"  data-type="to_trusted" name="Description" ></textarea>
	<p class="help-block"></p>
</div>
					<div class="form-group">
						<label for="add"></label>
						<input class="btn btn-primary" type="submit" name="add" />
					</div>
				</form>
				';
			break;
		case "edit":
			// TODO: ---- edit
			$tags_html .= "<ul class=\"nav nav-tabs\">" ;
			$tags_html .= "<li><a href=\"?table=maps&action=list\">".@LANG_LIST."</a></li>" ;
			$tags_html .= "<li><a href=\"?table=maps&action=add\">".@LANG_ADD."</a></li>" ;
			$tags_html .= "<li class=\"active\"><a href=\"#\">".@LANG_EDIT."</a></li>" ;
			$tags_html .= "</ul>" ;
			$tags_html .= "<br/>" ;
			/** avoid error **/
			if(isset($_GET["id"])){
				/** fix security **/
				$entry_id = (int)$_GET["id"];
	
				/** avoid blank field **/
				$data_title = "";
				$data_location = "";
				$data_Description = "";
	
				/** push button **/
				if(isset($_POST["edit"])){
					/** get input **/
					if(isset($_POST["title"])){
						$data_title = addslashes($_POST["title"]);
					}
					if(isset($_POST["location"])){
						$data_location = addslashes($_POST["location"]);
					}
					if(isset($_POST["Description"])){
						$data_Description = addslashes($_POST["Description"]);
					}
	
					/** update data to sql **/
					$sql_query = "UPDATE `map` SET `title` = '$data_title',`location` = '$data_location',`Description` = '$data_Description' WHERE `nid`=$entry_id" ;
					$stmt = $mysql->prepare($sql_query);
					$stmt->execute();
					$stmt->close();
					header("Location: ?table=maps&action=list");
				}
	
			/** fetch current data **/
			$sql_query = "SELECT * FROM `map`  WHERE `nid`=$entry_id LIMIT 0,1" ;
			if($result = $mysql->query($sql_query)){
				while ($data = $result->fetch_array()){
					$rows[] = $data;
				}
				$result->close();
			}
	
			/** get single data **/
			if(isset($rows[0]["title"])){
				$data_title = stripslashes($rows[0]["title"]) ;
			}
			if(isset($rows[0]["location"])){
				$data_location = stripslashes($rows[0]["location"]) ;
			}
			if(isset($rows[0]["Description"])){
				$data_Description = stripslashes($rows[0]["Description"]) ;
			}
	
			/** buat form edit **/
			$tags_html .= '
<form action="" method="post" enctype="multipart/form-data">


<!--
// TODO: ----|-- form : NID
-->


<!--
// TODO: ----|-- form : TITLE
-->
<div class="form-group">
	<label for="title">' . @LANG_TITLE. '</label>
	<input id="title" class="form-control" data-type="heading-1" type="text" name="title" placeholder="" value="'.htmlentities($data_title).'" />
	<p class="help-block"></p>
</div>


<!--
// TODO: ----|-- form : LOCATION
-->
<div class="form-group">
	<label for="location">' . @LANG_LOCATION. '</label>
	<input id="location" class="form-control" data-type="gmap" type="text" name="location" placeholder="" value="'.htmlentities($data_location).'" />
	<p class="help-block"></p>
</div>


<!--
// TODO: ----|-- form : DESCRIPTION
-->
<div class="form-group">
	<label for="Description">' . @LANG_DESCRIPTION. '</label>
	<textarea id="Description" class="form-control"  data-type="to_trusted" name="Description" >'.htmlentities($data_Description).'</textarea>
	<p class="help-block"></p>
</div>
<div class="form-group">
	<label for="edit"></label>
	<input class="btn btn-primary" type="submit" name="edit" />
</div>
</form>
				';
			};
			break;
			case "delete":
			// TODO: ---- delete
				/** avoid error **/
				if(isset($_GET["id"])){
					/** fix security **/
					$entry_id = (int)$_GET["id"];
					/** delete item in sql **/
					$sql_query = "DELETE FROM `map` WHERE `nid`=$entry_id" ;
					$stmt = $mysql->prepare($sql_query);
					$stmt->execute();
					$stmt->close();
					header("Location: ?table=maps&action=list");
				};
				break;
			}
			break;
		// TODO: Noticiass
		case "noticiass":
					$tags_html .= "<h4>". @LANG_NOTICIAS ."</h4>" ;
				switch($_GET["action"]){
					// TODO: ---- listing
					case "list":
					$tags_html .= "<ul class=\"nav nav-tabs\">" ;
					$tags_html .= "<li class=\"active\"><a href=\"?table=noticiass\">".@LANG_LIST."</a></li>" ;
					$tags_html .= "<li><a href=\"?table=noticiass&action=add\">".@LANG_ADD."</a></li>" ;
					$tags_html .= "</ul>" ;
					$tags_html .= "<br/>" ;
					$tags_html .= "<div class=\"table-responsive\">" ;
					$table_html = "<table id=\"datatable\" class=\"table table-striped table-hover\">" ;
					$table_html .= "<thead>" ;
					$table_html .= "<tr>" ;
					$table_html .= "<th>".@LANG_TITULO."</th>" ;
					$table_html .= "<th>".@LANG_FOTO."</th>" ;
					$table_html .= "<th>".@LANG_NOTICIA."</th>" ;
					$table_html .= "<th style=\"width:100px;\">Action</th>" ;
					$table_html .= "</tr>" ;
					$table_html .= "</thead>" ;
					$table_html .= "<tbody>" ;
					/** fetch data from mysql **/
					$sql_query = "SELECT * FROM `noticias`" ;
					if($result = $mysql->query($sql_query)){
						while ($data = $result->fetch_array()){
							$table_html .= "<tr>" ;
							$table_html .= "<td>" . htmlentities(stripslashes($data["titulo"])) . "</td>" ;
							$table_html .= "<td><img src=\"" . $data["foto"] . "\" alt=\"#\" width=\"64\" height=\"64\" /></td>" ;
							$content_html = substr( strip_tags($data["noticia"]),0,50);
							$table_html .= "<td>" . stripslashes($content_html). "...</td>" ;
							$table_html .= "<td>" ;
							$table_html .= "<div class=\"btn-group\" >" ;
							$table_html .= "<a class=\"btn btn-sm btn-warning\" href=\"?table=noticiass&action=edit&id=". $data["id"]. "\"><span class=\"glyphicon glyphicon-pencil\"></span></a> " ;
							$table_html .= "<a onclick=\"return confirm('Are you sure you want to delete ID#". $data["id"]. "')\" class=\"btn btn-sm btn-danger\" href=\"?table=noticiass&action=delete&id=". $data["id"]. "\"><span class=\"glyphicon glyphicon-trash\"></span></a> " ;
							$table_html .= "</div>" ;
							$table_html .= "</td>" ;
							$table_html .= "</tr>" ;
							$table_html .= "\r\n" ;
						}
						$result->close();
					}
				$table_html .= "</tbody>" ;
				$table_html .= "</table>" ;
				$table_html .= "</div>" ;
				$tags_html .= $table_html;
				break;
			case "add":
				// TODO: ---- add
				$tags_html .= "<ul class=\"nav nav-tabs\">" ;
				$tags_html .= "<li><a href=\"?table=noticiass&action=list\">".@LANG_LIST."</a></li>" ;
				$tags_html .= "<li class=\"active\"><a href=\"?table=noticiass&action=add\">".@LANG_ADD."</a></li>" ;
				$tags_html .= "</ul>" ;
				$tags_html .= "<br/>" ;
	
				/** push button **/
				if(isset($_POST["add"])){
	
					/** avoid error **/
					$data_titulo = "";
					$data_foto = "";
					$data_noticia = "";
	
					/** get input **/
					if(isset($_POST["titulo"])){
						$data_titulo = addslashes($_POST["titulo"]);
					}
					if(isset($_POST["foto"])){
						$data_foto = addslashes($_POST["foto"]);
					}
				if(isset($_FILES["foto-upload"]["name"])){
					if($_FILES["foto-upload"]["name"]!=""){
						$ext = pathinfo($_FILES["foto-upload"]["name"],PATHINFO_EXTENSION);
						$uploadfile =  "media/image/". sha1(time()).".".$ext;
						$uploadtemp =  $_FILES["foto-upload"]["tmp_name"];
						$mimetype = getimagesize($uploadtemp);
						if(!is_dir(dirname(__FILE__)."/media/image/")) {
							mkdir( dirname(__FILE__)."/media/image/",0777, true);
						}
							if (in_array(strtolower($ext),$config["image_allowed"])){
								if(preg_match("/image/",$mimetype["mime"])){
									move_uploaded_file($uploadtemp,dirname(__FILE__)."/" .$uploadfile);
									$data_foto =  $full_url ."/". $uploadfile;
								}
							}
						}
					}
					if(isset($_POST["noticia"])){
						$data_noticia = addslashes($_POST["noticia"]);
					}
	
					/** prepare save to mysql **/
					$sql_query = "INSERT INTO `noticias` (`titulo`,`foto`,`noticia`) VALUES ('$data_titulo','$data_foto','$data_noticia')" ;
					$stmt = $mysql->prepare($sql_query);
					$stmt->execute();
					$stmt->close();
					header("Location: ?table=noticiass&action=list");
				}
	
	
				/** Create Form **/
				$tags_html .= '
				<form action="" method="post" enctype="multipart/form-data">
					

<!--
// TODO: ----|-- form : ID
-->


<!--
// TODO: ----|-- form : TITULO
-->
<div class="form-group">
	<label for="titulo">' . @LANG_TITULO. '</label>
	<input id="titulo" class="form-control" data-type="heading-1" type="text" name="titulo" placeholder="" />
	<p class="help-block"></p>
</div>


<!--
// TODO: ----|-- form : FOTO
-->
<div class="form-group">
	<label for="foto">' . @LANG_FOTO. '</label>
	<textarea id="foto" class="form-control"  data-type="images" name="foto" ></textarea>
	<p class="help-block">Using base64 image data <input data-target="#foto" type="file" data-type="image-base64" /> or file upload <input name="foto-upload" type="file" data-type="image-upload" /></p>
</div>


<!--
// TODO: ----|-- form : NOTICIA
-->
<div class="form-group">
	<label for="noticia">' . @LANG_NOTICIA. '</label>
	<textarea id="noticia" class="form-control"  data-type="to_trusted" name="noticia" ></textarea>
	<p class="help-block"></p>
</div>
					<div class="form-group">
						<label for="add"></label>
						<input class="btn btn-primary" type="submit" name="add" />
					</div>
				</form>
				';
			break;
		case "edit":
			// TODO: ---- edit
			$tags_html .= "<ul class=\"nav nav-tabs\">" ;
			$tags_html .= "<li><a href=\"?table=noticiass&action=list\">".@LANG_LIST."</a></li>" ;
			$tags_html .= "<li><a href=\"?table=noticiass&action=add\">".@LANG_ADD."</a></li>" ;
			$tags_html .= "<li class=\"active\"><a href=\"#\">".@LANG_EDIT."</a></li>" ;
			$tags_html .= "</ul>" ;
			$tags_html .= "<br/>" ;
			/** avoid error **/
			if(isset($_GET["id"])){
				/** fix security **/
				$entry_id = (int)$_GET["id"];
	
				/** avoid blank field **/
				$data_titulo = "";
				$data_foto = "";
				$data_noticia = "";
	
				/** push button **/
				if(isset($_POST["edit"])){
					/** get input **/
					if(isset($_POST["titulo"])){
						$data_titulo = addslashes($_POST["titulo"]);
					}
					if(isset($_POST["foto"])){
						$data_foto = addslashes($_POST["foto"]);
					}
				if(isset($_FILES["foto-upload"]["name"])){
					if($_FILES["foto-upload"]["name"]!=""){
						$ext = pathinfo($_FILES["foto-upload"]["name"],PATHINFO_EXTENSION);
						$uploadfile =  "media/image/". sha1(time()).".".$ext;
						$uploadtemp =  $_FILES["foto-upload"]["tmp_name"];
						$mimetype = getimagesize($uploadtemp);
						if(!is_dir(dirname(__FILE__)."/media/image/")) {
							mkdir( dirname(__FILE__)."/media/image/",0777, true);
						}
							if (in_array(strtolower($ext),$config["image_allowed"])){
								if(preg_match("/image/",$mimetype["mime"])){
									move_uploaded_file($uploadtemp,dirname(__FILE__)."/" .$uploadfile);
									$data_foto =  $full_url."/". $uploadfile;
								}
							}
						}
					}
					if(isset($_POST["noticia"])){
						$data_noticia = addslashes($_POST["noticia"]);
					}
	
					/** update data to sql **/
					$sql_query = "UPDATE `noticias` SET `titulo` = '$data_titulo',`foto` = '$data_foto',`noticia` = '$data_noticia' WHERE `id`=$entry_id" ;
					$stmt = $mysql->prepare($sql_query);
					$stmt->execute();
					$stmt->close();
					header("Location: ?table=noticiass&action=list");
				}
	
			/** fetch current data **/
			$sql_query = "SELECT * FROM `noticias`  WHERE `id`=$entry_id LIMIT 0,1" ;
			if($result = $mysql->query($sql_query)){
				while ($data = $result->fetch_array()){
					$rows[] = $data;
				}
				$result->close();
			}
	
			/** get single data **/
			if(isset($rows[0]["titulo"])){
				$data_titulo = stripslashes($rows[0]["titulo"]) ;
			}
			if(isset($rows[0]["foto"])){
				$data_foto = stripslashes($rows[0]["foto"]) ;
			}
			if(isset($rows[0]["noticia"])){
				$data_noticia = stripslashes($rows[0]["noticia"]) ;
			}
	
			/** buat form edit **/
			$tags_html .= '
<form action="" method="post" enctype="multipart/form-data">


<!--
// TODO: ----|-- form : ID
-->


<!--
// TODO: ----|-- form : TITULO
-->
<div class="form-group">
	<label for="titulo">' . @LANG_TITULO. '</label>
	<input id="titulo" class="form-control" data-type="heading-1" type="text" name="titulo" placeholder="" value="'.htmlentities($data_titulo).'" />
	<p class="help-block"></p>
</div>


<!--
// TODO: ----|-- form : FOTO
-->
<div class="form-group">
	<label for="foto">' . @LANG_FOTO. '</label>
	<textarea id="foto" class="form-control"  data-type="images" name="foto" >'.htmlentities($data_foto).'</textarea>
	<p class="help-block">Using base64 image data <input data-target="#foto" type="file" data-type="image-base64" /> or file upload <input name="foto-upload" type="file" data-type="image-upload" /></p>
</div>


<!--
// TODO: ----|-- form : NOTICIA
-->
<div class="form-group">
	<label for="noticia">' . @LANG_NOTICIA. '</label>
	<textarea id="noticia" class="form-control"  data-type="to_trusted" name="noticia" >'.htmlentities($data_noticia).'</textarea>
	<p class="help-block"></p>
</div>
<div class="form-group">
	<label for="edit"></label>
	<input class="btn btn-primary" type="submit" name="edit" />
</div>
</form>
				';
			};
			break;
			case "delete":
			// TODO: ---- delete
				/** avoid error **/
				if(isset($_GET["id"])){
					/** fix security **/
					$entry_id = (int)$_GET["id"];
					/** delete item in sql **/
					$sql_query = "DELETE FROM `noticias` WHERE `id`=$entry_id" ;
					$stmt = $mysql->prepare($sql_query);
					$stmt->execute();
					$stmt->close();
					header("Location: ?table=noticiass&action=list");
				};
				break;
			}
			break;
		// TODO: FileBrowser
		case "filebrowser":
			if(!isset($_GET["type"])){
					$_GET["type"]="image";
			}
			$tags_html .= "<h4 class=\"page-title\">".@LANG_FILE_BROWSER."</h4>" ;
					$tags_html .= "<ul class=\"nav nav-tabs\">" ;
			if($_GET["type"]=="image"){
					$tags_html .= "<li class=\"active\"><a href=\"?table=filebrowser&type=image\">".@LANG_IMAGES."</a></li>" ;
				}else{
					$tags_html .= "<li><a href=\"?table=filebrowser&type=image\">".@LANG_IMAGES."</a></li>" ;
				}
			if($_GET["type"]=="file"){
					$tags_html .= "<li class=\"active\"><a href=\"?table=filebrowser&type=file\">".@LANG_FILES."</a></li>" ;
				}else{
					$tags_html .= "<li><a href=\"?table=filebrowser&type=file\">".@LANG_FILES."</a></li>" ;
				}
			if($_GET["type"]=="media"){
					$tags_html .= "<li class=\"active\"><a href=\"?table=filebrowser&type=media\">".@LANG_MEDIAS."</a></li>" ;
				}else{
					$tags_html .= "<li><a href=\"?table=filebrowser&type=media\">".@LANG_MEDIAS."</a></li>" ;
				}
					$tags_html .= "</ul>" ;
					$tags_html .= "<br/>" ;
			$tags_html .= "<div>" ;
			$tags_html .= "<iframe src=\"kcfinder/browse.php?opener=tinymce4&type=".$_GET["type"]."\" style=\"border:0;padding:0;margin:0;overflow:hidden;min-height:480px;width:100%;\" ></iframe>" ;
			$tags_html .= "</div>" ;
			break;
	}
		$tags_html .= "</div>" ;
		$tags_html .= "</div>" ;
		$tags_html .= "</div>" ;
		$tags_html .= "</div>" ;
		$tags_html .= "</div>" ;
		$tags_html .= "<footer>" ;
		$tags_html .= "<div class=\"container-fluid\">" ;
		$tags_html .= "<div class=\"row\">" ;
		$tags_html .= "<div class=\"col-md-12\"><p class=\"text-left navbar-text\">Criado e fornecido com amor por Steffan :D </p></div>" ;
		$tags_html .= "</div>" ;
		$tags_html .= $ionicon_dialog ;
		$tags_html .= "</div>" ;
		$tags_html .= "</footer>" ;
	}else{
		$tags_html .= '
<div class="container">    
    <div style="max-width: 330px;margin: 0 auto;">    
        <form method="post" enctype="multipart/form-data">
            <h2 class="form-signin-heading">Please sign in</h2>
            <div class="form-group">
            	<label for="username">Username</label>
            	<input class="form-control" type="text" name="email" placeholder="Email address" required autofocus />
            </div>
            <div class="form-group">
            	<label for="password">Password</label>
            	<input class="form-control" type="password" name="password" placeholder="Password" required autofocus/>
            </div>
            <input type="submit" class="btn btn-primary" name="log-in"/>
        </form>
    </div>
</div>
' ;
	}
	
	$kcfinder_tinymce = $kcfinder_input = null;
	if($config["kcfinder"]==true){
		$kcfinder_tinymce = '
    file_browser_callback : function(field, url, type, win) {
		tinyMCE.activeEditor.windowManager.open({
			file: "kcfinder/browse.php?opener=tinymce4&cms=ima_builder&field=" + field + "&type=" + type,
			title: "KCFinder",
			width: 640,
			height: 500,
			inline: true,
			close_previous: false
		}, {
			window: win,
			input: field
		});
     return false;
	},' ;
		$kcfinder_input = '
            var KCFinderTarget = "";
            window.KCFinder = {
            	callBack: function(path) {
            		$("#" + KCFinderTarget).val(main_url + path);
            	},
            	Open: function(prop_id, file_type) {
            		KCFinderTarget = prop_id;
            		var newwindow = window.open("./kcfinder/browse.php?type=" + file_type, "Image Editor", "height=480,width=1024");
            		if (window.focus) {
            			newwindow.focus()
            		}
            	}
            }; 
            $("*[data-type=\'images\']").click(function(){
                KCFinder.Open($(this).prop("id"), "image");
            });
            $("*[data-type=\'audio\']").click(function(){
                KCFinder.Open($(this).prop("id"), "media");
            });
            $("*[data-type=\'video\']").click(function(){
                KCFinder.Open($(this).prop("id"), "media");
            });
            ';
	};
echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>GMAP Test</title>    
    <link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.6/'.strtolower($config["theme"]).'/bootstrap.min.css" rel="stylesheet"/>
    <link href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet"/>
    <link href="//cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link href="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-corner-indicator.css" rel="stylesheet"/>
    <!--[if lt IE 9]>
        <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
    <script type="text/javascript"> var main_url = "'.$main_url.'";</script>
  </head>
  <body>
    '.$tags_html.'
    <script data-pace-options=\'{"ajax":true}\' src="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
    <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
    <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        window.icon_picker_target = "test"; 
        function readURL(input,target) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(target).val(e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function ionicons(myclass){
            $("#"+window.icon_picker_target).val(myclass);
            $("#ionicons").modal("hide");
        }
        $(document).ready(function() {
            if($("#datatable").length){
        	   $("#datatable").dataTable();
            }
            tinymce.init({
                selector: "textarea[data-type=\'to_trusted\']",
                plugins: "code textcolor image link table  contextmenu",
                toolbar1: "undo redo | forecolor backcolor  | bold italic underline | alignleft aligncenter alignright alignjustify | image table | numlist bullist | media",              
                toolbar2: "styleselect fontsizeselect",
                force_br_newlines : false,
                force_p_newlines : false,
                forced_root_block : "",
                extended_valid_elements : "*[*]",
                valid_elements : "*[*]",
                link_class_list:[{"text":"None ","value":""},{"text":"Button Light","value":"button button-light ink"},{"text":"Button Stable","value":"button button-stable ink"},{"text":"Button Positive","value":"button button-positive ink"},{"text":"Button Calm","value":"button button-calm ink"},{"text":"Button Balanced","value":"button button-balanced ink"},{"text":"Button Energized","value":"button button-energized ink"},{"text":"Button Assertive","value":"button button-assertive ink"},{"text":"Button Royal","value":"button button-royal ink"},{"text":"Button Dark","value":"button button-dark ink"}],
                target_list : [{text: "None",value: ""},{text: "New window",alue: "_blank"},{text: "Top window",value: "_top"},{text: "Self window",value: "_self"}],
                '.$kcfinder_tinymce.'               
            });
           	$("input[data-type=\'icon\']").click(function(){
           	       window.icon_picker_target = $(this).prop("id");
                   $("#ionicons").modal("show");
            });
            $("input[data-type=\'image-base64\']").change(function(){
                var target = $(this).attr("data-target");
                readURL(this,target);
            });
            '.$kcfinder_input.'                              
        });
        
    /** relation **/    
    '.$js_for_relation.'
        
    </script>
    
    </body>
</html>';	
	
?>