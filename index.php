<?php

/**
 * Author: Luca Palano
 * Site: http://www.lpzone.it/projects/htmlig
 * Contacts: http://www.lpzone.it/contacts/
 */

# requires the curl class
require_once 'curl.php';

# display the errors: turn off in production environment
if (isset($_REQUEST['debug'])) ini_set('display_errors', 1);
else ini_set('display_errors', 0);

# This function allows you to disable standard libxml errors and enable user error handling.
libxml_use_internal_errors(true);

# a function used to show a generic error message
function invalidURLNotification() {
	echo 'Invalid URL!';
	exit();
}

# check the presence of the url REQUEST variable
if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
	$url = $_REQUEST['url'];
	$urlParsingResults = parse_url($url);
	if ($urlParsingResults === FALSE) invalidURLNotification();
	
	# TEST: try to check the dns
	# 
	# if (checkdnsrr($urlParsingResults['host']) === FALSE) invalidURLNotification();
} else {
	invalidURLNotification();
}

# create an instance of singleton curl class
$curl = curl::instance();
$curl->setOption(CURLOPT_URL, $url);
$result = $curl->startConnection();

# check if the loaded content is empty
if (empty($result)) {
	$response = array (
		'status' => 'error',
		'message' => 'The page looks like empty. Please, check the URL.'
	);
	echo json_encode($response);
	exit();
}
	
# This array will contain all the images URLs
$imagesURLs = array();

# create a DOMDocument for HTML (XML) parsing
$dom = new DOMDocument();
$dom->loadHTML($result);

/*
echo "<pre>";
print_r(libxml_get_errors());
echo "</pre>";
*/

# clears the libxml error buffer. This is useful to avoid the validation warnings in debug mode where display_errors is true.
libxml_clear_errors();

/*
# check if the page is valid, according to DTD
$validation = 1;
if (!$dom->validate()) {
	$validation = 0;
}
*/

# parse the HTML code
$images = $dom->getElementsByTagName('img');
foreach ($images as $image) {
	foreach ($image->attributes as $attr) {
		# check the presence of src attribute
		if ($attr->nodeName == 'src') {
	        $src = $attr->nodeValue;
	        # if the src path contains more than 2 forward slashes, they will be replaced by one slash
	        $src = preg_replace("/\w(\/){2,}/", "/", $src);
	        if (strstr($src, '://') == FALSE) {
	        	if ($url[strlen($url)-1] == '/') 
	        		$imageURL = substr($url, 0, -1).$src;
	            else
	            	$imageURL = $url.$src;
	        } else {
	        	$imageURL = $src;
	        }
            array_push($imagesURLs, $imageURL);
		}
    }
}

# When you aren't in debug mode you'll collect the standard JSON response otherwise a raw list of images URLs
if (!isset($_REQUEST['debug'])) {
	# set the HTTP header for the JSON content
	header('Content-Type: application/json');
	
	$response = array(
		'status' => 'ok',
		'imagesURLs' => $imagesURLs
	);
	
	echo json_encode($response);
} else {
	echo "In debug mode will be printed only the images array:<pre>";
	print_r($imagesURLs);
	echo "</pre>";
}

?>
