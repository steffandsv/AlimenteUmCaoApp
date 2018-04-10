<?php


/** CONFIG:START **/
$config["host"] 		= "host" ; 		//host
$config["user"] 		= "user" ; 		//Username SQL
$config["pass"] 		= "pass" ; 		//Password SQL
$config["dbase"] 		= "banco_de_dados" ; 		//Database
$config["utf8"] 		= true ; 		//turkish charset set false
$config["abs_url_images"] 		= "urlabsoluta/media/image/" ; 		//Absolute Images URL
$config["abs_url_videos"] 		= "urlabsoluta/media/media/" ; 		//Absolute Videos URL
$config["abs_url_audios"] 		= "urlabsoluta/media/media/" ; 		//Absolute Audio URL
$config["abs_url_files"] 		= "urlabsoluta/media/file/" ; 		//Absolute Files URL
$config["image_allowed"][] 		= array("mimetype"=>"image/jpeg","ext"=>"jpg") ; 		//whitelist image
$config["image_allowed"][] 		= array("mimetype"=>"image/jpg","ext"=>"jpg") ; 		
$config["image_allowed"][] 		= array("mimetype"=>"image/png","ext"=>"png") ; 		
$config["file_allowed"][] 		= array("mimetype"=>"text/plain","ext"=>"txt") ; 		
/** CONFIG:END **/

if(isset($_SERVER["HTTP_X_AUTHORIZATION"])){
	list($_SERVER["PHP_AUTH_USER"],$_SERVER["PHP_AUTH_PW"]) = explode(":" , base64_decode(substr($_SERVER["HTTP_X_AUTHORIZATION"],6)));
}
$rest_api=array("data"=>array("status"=>404,"title"=>"Not found"),"title"=>"Error","message"=>"Routes not found");

/** connect to mysql **/
$mysql = new mysqli($config["host"], $config["user"], $config["pass"], $config["dbase"]);
if (mysqli_connect_errno()){
	die(mysqli_connect_error());
}


if(!isset($_GET["json"])){
	$_GET["json"]= "route";
}
if((!isset($_GET["form"])) && ($_GET["json"] == "submit")) {
	$_GET["json"]= "route";
}

if($config["utf8"]==true){
	$mysql->set_charset("utf8");
}

$get_dir = explode("/", $_SERVER["PHP_SELF"]);
unset($get_dir[count($get_dir)-1]);
$main_url = "http://" . $_SERVER["HTTP_HOST"] . implode("/",$get_dir)."/";


switch($_GET["json"]){	
	// TODO: -+- Listing : map
	case "map":
		$rest_api=array();
		$where = $_where = null;
		// TODO: -+----+- statement where
		if(isset($_GET["nid"])){
			if($_GET["nid"]!="-1"){
				$_where[] = "`nid` LIKE '%".$mysql->escape_string($_GET["nid"])."%'";
			}
		}
		if(isset($_GET["title"])){
			if($_GET["title"]!="-1"){
				$_where[] = "`title` LIKE '%".$mysql->escape_string($_GET["title"])."%'";
			}
		}
		if(isset($_GET["location"])){
			if($_GET["location"]!="-1"){
				$_where[] = "`location` LIKE '%".$mysql->escape_string($_GET["location"])."%'";
			}
		}
		if(isset($_GET["Description"])){
			if($_GET["Description"]!="-1"){
				$_where[] = "`Description` LIKE '%".$mysql->escape_string($_GET["Description"])."%'";
			}
		}
		if(is_array($_where)){
			$where = " WHERE " . implode(" AND ",$_where);
		}
		// TODO: -+----+- orderby
		$order_by = "`nid`";
		$sort_by = "DESC";
		if(!isset($_GET["order"])){
			$_GET["order"] = "`nid`";
		}
		// TODO: -+----+- sort asc/desc
		if(!isset($_GET["sort"])){
			$_GET["sort"] = "desc";
		}
		if($_GET["sort"]=="asc"){
			$sort_by = "ASC";
		}else{
			$sort_by = "DESC";
		}
		if($_GET["order"]=="nid"){
			$order_by = "`nid`";
		}
		if($_GET["order"]=="title"){
			$order_by = "`title`";
		}
		if($_GET["order"]=="location"){
			$order_by = "`location`";
		}
		if($_GET["order"]=="Description"){
			$order_by = "`Description`";
		}
		if($_GET["order"]=="random"){
			$order_by = "RAND()";
		}
		// TODO: -+----+- SQL Query
		$sql = "SELECT * FROM `map` ".$where."ORDER BY ".$order_by." ".$sort_by." LIMIT 0, 100" ;
		if($result = $mysql->query($sql)){
			$z=0;
			while ($data = $result->fetch_array()){
				if(isset($data['nid'])){$rest_api[$z]['nid'] = $data['nid'];}; # id
				if(isset($data['title'])){$rest_api[$z]['title'] = $data['title'];}; # heading-1
				if(isset($data['location'])){$rest_api[$z]['location'] = $data['location'];}; # gmap
				if(isset($data['Description'])){$rest_api[$z]['Description'] = $data['Description'];}; # to_trusted
				$z++;
			}
			$result->close();
			if(isset($_GET["nid"])){
				if(isset($rest_api[0])){
					$rest_api = $rest_api[0];
				}
			}
		}

		break;
	
	// TODO: -+- Listing : noticias
	case "noticias":
		$rest_api=array();
		$where = $_where = null;
		// TODO: -+----+- statement where
		if(isset($_GET["id"])){
			if($_GET["id"]!="-1"){
				$_where[] = "`id` LIKE '%".$mysql->escape_string($_GET["id"])."%'";
			}
		}
		if(isset($_GET["titulo"])){
			if($_GET["titulo"]!="-1"){
				$_where[] = "`titulo` LIKE '%".$mysql->escape_string($_GET["titulo"])."%'";
			}
		}
		if(isset($_GET["foto"])){
			if($_GET["foto"]!="-1"){
				$_where[] = "`foto` LIKE '%".$mysql->escape_string($_GET["foto"])."%'";
			}
		}
		if(isset($_GET["noticia"])){
			if($_GET["noticia"]!="-1"){
				$_where[] = "`noticia` LIKE '%".$mysql->escape_string($_GET["noticia"])."%'";
			}
		}
		if(is_array($_where)){
			$where = " WHERE " . implode(" AND ",$_where);
		}
		// TODO: -+----+- orderby
		$order_by = "`id`";
		$sort_by = "DESC";
		if(!isset($_GET["order"])){
			$_GET["order"] = "`id`";
		}
		// TODO: -+----+- sort asc/desc
		if(!isset($_GET["sort"])){
			$_GET["sort"] = "desc";
		}
		if($_GET["sort"]=="asc"){
			$sort_by = "ASC";
		}else{
			$sort_by = "DESC";
		}
		if($_GET["order"]=="id"){
			$order_by = "`id`";
		}
		if($_GET["order"]=="titulo"){
			$order_by = "`titulo`";
		}
		if($_GET["order"]=="foto"){
			$order_by = "`foto`";
		}
		if($_GET["order"]=="noticia"){
			$order_by = "`noticia`";
		}
		if($_GET["order"]=="random"){
			$order_by = "RAND()";
		}
		// TODO: -+----+- SQL Query
		$sql = "SELECT * FROM `noticias` ".$where."ORDER BY ".$order_by." ".$sort_by." LIMIT 0, 100" ;
		if($result = $mysql->query($sql)){
			$z=0;
			while ($data = $result->fetch_array()){
				if(isset($data['id'])){$rest_api[$z]['id'] = $data['id'];}; # id
				if(isset($data['titulo'])){$rest_api[$z]['titulo'] = $data['titulo'];}; # heading-1
				
				$abs_url_images = $config['abs_url_images'].'/';
				$abs_url_videos = $config['abs_url_videos'].'/';
				$abs_url_audios = $config['abs_url_audios'].'/';
				if(!isset($data['foto'])){$data['foto']='undefined';}; # images
				if((substr($data['foto'], 0, 7)=='http://')||(substr($data['foto'], 0, 8)=='https://')){
					$abs_url_images = $abs_url_videos  = $abs_url_audios = '';
				}
				
				if(substr($data['foto'], 0, 5)=='data:'){
					$abs_url_images = $abs_url_videos  = $abs_url_audios = '';
				}
				$rest_api[$z]['foto'] = $abs_url_images . $data['foto']; # images
				if(isset($data['noticia'])){$rest_api[$z]['noticia'] = $data['noticia'];}; # to_trusted
				$z++;
			}
			$result->close();
			if(isset($_GET["id"])){
				if(isset($rest_api[0])){
					$rest_api = $rest_api[0];
				}
			}
		}

		break;
	// TODO: -+- route
	case "route":		$rest_api=array();
		$rest_api["site"]["name"] = "GMAP Test" ;
		$rest_api["site"]["description"] = "Testing Template Listing gMAP (default features and no coding)" ;
		$rest_api["site"]["imabuilder"] = "rev17.07.01" ;

		$rest_api["routes"][0]["namespace"] = "map";
		$rest_api["routes"][0]["tb_version"] = "Upd.1801300921";
		$rest_api["routes"][0]["methods"][] = "GET";
		$rest_api["routes"][0]["args"]["nid"] = array("required"=>"false","description"=>"Selecting `map` based `nid`");
		$rest_api["routes"][0]["args"]["title"] = array("required"=>"false","description"=>"Selecting `map` based `title`");
		$rest_api["routes"][0]["args"]["location"] = array("required"=>"false","description"=>"Selecting `map` based `location`");
		$rest_api["routes"][0]["args"]["Description"] = array("required"=>"false","description"=>"Selecting `map` based `Description`");
		$rest_api["routes"][0]["args"]["order"] = array("required"=>"false","description"=>"order by `random`, `nid`, `title`, `location`, `Description`");
		$rest_api["routes"][0]["args"]["sort"] = array("required"=>"false","description"=>"sort by `asc` or `desc`");
		$rest_api["routes"][0]["_links"]["self"] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]."?json=map";
		$rest_api["routes"][1]["namespace"] = "noticias";
		$rest_api["routes"][1]["tb_version"] = "Upd.1801300926";
		$rest_api["routes"][1]["methods"][] = "GET";
		$rest_api["routes"][1]["args"]["id"] = array("required"=>"false","description"=>"Selecting `noticias` based `id`");
		$rest_api["routes"][1]["args"]["titulo"] = array("required"=>"false","description"=>"Selecting `noticias` based `titulo`");
		$rest_api["routes"][1]["args"]["foto"] = array("required"=>"false","description"=>"Selecting `noticias` based `foto`");
		$rest_api["routes"][1]["args"]["noticia"] = array("required"=>"false","description"=>"Selecting `noticias` based `noticia`");
		$rest_api["routes"][1]["args"]["order"] = array("required"=>"false","description"=>"order by `random`, `id`, `titulo`, `foto`, `noticia`");
		$rest_api["routes"][1]["args"]["sort"] = array("required"=>"false","description"=>"sort by `asc` or `desc`");
		$rest_api["routes"][1]["_links"]["self"] = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]."?json=noticias";
		break;
	// TODO: -+- submit

	case "submit":
		$rest_api=array();

		$rest_api["methods"][0] = "POST";
		$rest_api["methods"][1] = "GET";

	break;

}


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization,X-Authorization');
if (!isset($_GET["callback"])){
	header('Content-type: application/json');
	if(defined("JSON_UNESCAPED_UNICODE")){
		echo json_encode($rest_api,JSON_UNESCAPED_UNICODE);
	}else{
		echo json_encode($rest_api);
	}

}else{
	if(defined("JSON_UNESCAPED_UNICODE")){
		echo strip_tags($_GET["callback"]) ."(". json_encode($rest_api,JSON_UNESCAPED_UNICODE). ");" ;
	}else{
		echo strip_tags($_GET["callback"]) ."(". json_encode($rest_api) . ");" ;
	}

}