<?php

function index_mp3s($dir, $recursive = true) {
	global $mp3s;
	
	if(!is_array($mp3s)) {
		$mp3s = array();
	}
	
	if(!is_dir($dir)) {
		return;
	}
	
	if($handle = opendir($dir)) {
		while(false !== ($item = readdir($handle))) {
			if($item != "." && $item != "..") {
				if(is_dir($dir."/".$item) && $recursive) {
					index_mp3s($dir."/".$item, $recursive);
				} else {
					if(end(explode(".", $item)) == "mp3") {
						$uid = str_replace("/", "-", $dir);
						$uid .=  "-" . str_replace(".mp3", "", $item);
						$uid = str_replace(" ", "-", $uid);
						$uid = strtolower($uid);
						
						$title = str_replace(".mp3", "", $item);
						$title = str_replace("_", " ", $title);
						$title = ucwords($title);
						
						array_push($mp3s, array("dir"=>str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir), "filename"=>$item, "title"=>$title, "date"=>filemtime($dir."/".$item) ));
					}
				}
			}
		}
		closedir($handle);
	}
}

function rcmp($a, $b) {
    if($a['date'] == $b['date']) {
        return 0;
    }
    return ($a['date'] > $b['date']) ? -1 : 1;
}


define('WP_USE_THEMES', false);

/** Loads the WordPress Environment and Template */
$dir = "";

$max_search_levels = 10;
$search_level = 0;
while(!is_file($dir."wp-blog-header.php")) {
	$dir = "../" . $dir;
	
	$search_level++;
	if($search_level > $max_search_levels) {
		die();
	}
}

require($dir.'wp-blog-header.php');

$n3rdskwat_mp3path = get_option("n3rdskwat_mp3path");

if($n3rdskwat_mp3path == "/") {
	$n3rdskwat_mp3path = "";
}

if(substr($n3rdskwat_mp3path, 0, 1) == "/") {
	$n3rdskwat_mp3path = substr($n3rdskwat_mp3path, 1, strlen($n3rdskwat_mp3path));
}

if(substr($n3rdskwat_mp3path, -1, 1) == "/") {
	$n3rdskwat_mp3path = substr($n3rdskwat_mp3path, 0, strlen($n3rdskwat_mp3path)-1);
}

$n3rdskwat_mp3path = ($n3rdskwat_mp3path == "")?$_SERVER['DOCUMENT_ROOT']:$_SERVER['DOCUMENT_ROOT']."/".$n3rdskwat_mp3path;
$recursive = get_option('n3rdskwat_search_recusive');

$mp3s = array();
index_mp3s($n3rdskwat_mp3path, ($recursive == 1));

uasort($mp3s, 'rcmp');

$tmp = array();
foreach($mp3s as $value) {
	array_push($tmp, $value);
}
$mp3s = $tmp;


if($_GET['type'] == 'json') {
	die(json_encode($mp3s));
}

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<listing>
	<tracks>
<?php

// Open a known directory, and proceed to read its contents
foreach($mp3s as $mp3) {
	$dir = htmlspecialchars($mp3['dir']);
	
	echo "\t\t<track>\n";
	echo "\t\t\t<location>". $dir . "/" . htmlspecialchars($mp3['filename'])."</location>\n";
	echo "\t\t\t<title>".htmlspecialchars($mp3['title'])."</title>\n";
	echo "\t\t\t<date>".$mp3['date']."</date>\n";
	echo "\t\t</track>\n";
}
?>
	</tracks>
</listing>