<?php
require( "../../../wp-load.php" );

// Only authorized on admin pages
check_admin_referer();

@header("Cache-Control: no-cache, must-revalidate");
@header("Content-Type: application/json; charset=UTF-8");
@header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
@header("Status: 200 OK");
if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
	@header("Cache-Control: no-cache");
	@header("Pragma: no-cache");
}
global $callback;
$callback = (isset($_GET['callback']) && $_GET['callback'] != null) ? $_GET['callback'] : null;

$do = (isset($_GET['do']) && $_GET['do'] != null) ? $_GET['do'] : null;

if ($do == null) $do = "";

switch( $do ){
	case "cache_reset" :
		$response = joliprint_ajax_cache_reset();
		@header("Content-Length: ".strlen($response));
		die($response);
		break;
	default:
		$response = joliprint_ajax_error( __("This action is not valid.","joliprint") );
		@header("Content-Length: ".strlen($response));
		die($response);
}

function joliprint_ajax_cache_reset(){
	global  $callback;
	if ( floatval($wp_version) >= 2.7){
		return joliprint_no_cache_reset();
	}
	
	$request = new WP_Http;
	$joliprint_server_url = "http://" . JOLIPRINT_SERVER;
	if (is_ssl()) $joliprint_server_url = "https://" . JOLIPRINT_SERVER;
	$url = $joliprint_server_url . "/api/rest/cachereset/json?url=" . rawurlencode(site_url());
	if ($callback != null ) $url .= "&callback=" . $callback;
	
	$result = $request->request( $url, array( 'timeout' => 5, 'redirection' => 5, 'method' => 'GET', 'headers' => array( 'referer'=> site_url() ) ) );
	if ( is_wp_error( $result ) ) return joliprint_ajax_error($result->get_error_message());
	$response = $result['body'];
	
	return $response;
}	
function joliprint_ajax_error($error){
	global $callback;
	return ( $callback == null ? "?" : $callback ) . "({\"status_code\":\"500\",\"status_txt\":\"ERROR : " . __esc_js($error) . "\"})";
	
}
function joliprint_no_cache_reset(){
	return joliprint_ajax_error( __("Cache update is not possible due to your Wordpress version. Please upgrade to at least Wordpress 2.7.", "joliprint") );
}
?>