<?php

define('WP_USE_THEMES', false);

/** Loads the WordPress Environment and Template */
$document_root = "";

$max_search_levels = 10;
$search_level = 0;
while(!is_file($document_root."wp-blog-header.php")) {
	$document_root = "../" . $document_root;
	
	$search_level++;
	if($search_level > $max_search_levels) {
		die();
	}
}

require($document_root.'wp-blog-header.php');

$blog = get_bloginfo('url');
$blog = str_replace("http://", "", $blog);

$blog_parts = explode("/", $blog);
if(count($blog_parts) > 1) {
	$blog = "/" . end($blog_parts);
} else {
	$blog = "";
}

$document_root = realpath($document_root);


if (!function_exists('json_encode')) {
	function json_encode($a=false) {
		// Some basic debugging to ensure we have something returned
		if(is_null($a)) return 'null';
		if($a === false) return 'false';
		if($a === true) return 'true';
		if(is_scalar($a)) {
			if(is_float($a)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if(is_string($a)) {
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
		}
		
		$isList = true;
		
		for($i = 0; reset($a); $i) {
			if(key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		
		$result = array();
		
		if($isList) {
			foreach($a as $v) $result[] = json_encode($v);
			return '[' . join(',', $result) . ']';
		} else {
			foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
}

function index_mp3s($dir, $recursive = true) {
	global $mp3s;
	global $document_root, $blog;
	
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
						
						array_push($mp3s, array("dir"=>str_replace($document_root, $blog, $dir), "filename"=>$item, "title"=>$title, "date"=>filemtime($dir."/".$item) ));
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

$n3rdskwat_mp3path = ($n3rdskwat_mp3path == "")?$document_root:$document_root."/".$n3rdskwat_mp3path;
$recursive = get_option('n3rdskwat_search_recusive');

$mp3s = array();
index_mp3s($n3rdskwat_mp3path, ($recursive == 1));

uasort($mp3s, 'rcmp');

$tmp = array();
foreach($mp3s as $value) {
	array_push($tmp, $value);
}
$mp3s = $tmp;


header("HTTP/1.0 200 OK");

if($_GET['type'] == 'json') {
	echo json_encode($mp3s);
	exit(1);
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