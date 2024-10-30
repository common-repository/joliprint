<?php
/*
Plugin Name: PDF & Print Button Joliprint
Plugin URI: http://joliprint.com/
Description: With the Joliprint button, add a nice feature for your readers : Make your post printed in a nice, crisp and professional layout. Forget ugly prints.

Author: Joliprint
Version: 1.3.2
Author URI: http://joliprint.com/
*/

require_once( ABSPATH . "wp-content/plugins/joliprint/joliprint_constants.php");
global $joliprint_script_included;
$joliprint_script_included = false;
/* joliprint initialisation on activation, see joliprint_install.php */
require_once( ABSPATH . "wp-content/plugins/joliprint/joliprint_install.php" );
register_activation_hook(__FILE__,'joliprint_activate');
register_deactivation_hook(__FILE__, 'joliprint_deactivate' ); 
if ( function_exists('register_uninstall_hook') ){
	register_uninstall_hook(__FILE__, 'joliprint_uninstall');
}


/*Filter */
add_filter('plugin_row_meta', 'joliprint_register_plugin_links',10,2);
add_action('init', 'joliprint_init');
/* Admin hooks */
add_action('admin_menu', 'joliprint_options_page');

load_plugin_textdomain( 'joliprint', 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/lang', basename(dirname(__FILE__)) . '/lang' );


function joliprint_get_wp_version() {
    return (float)substr(get_bloginfo('version'),0,3); 
}

function joliprint_init(){
	global $joliprint_script_included;
	add_thickbox();
	if (joliprint_get_wp_version() >= 2.7 || apply_filters('at_assume_latest', '__return_false')  ) {
		if ( is_admin() ) {
			add_action( 'admin_init', 'joliprint_register_settings' );
			joliprint_admin_warnings();
			return;
		}
	}
	add_action('wp_print_scripts', 'joliprint_print_scripts', 50000);
	add_action( 'wp_head', 'wp_head_joliprint' , 50000);
	
}
function joliprint_register_settings(){
	register_setting( "joliprint", "joliprint_install_date" );
	register_setting( "joliprint", "joliprint_button_type");
	register_setting( "joliprint", "joliprint_button_label");
	register_setting( "joliprint", "joliprint_button_label_position");
	register_setting( "joliprint", "joliprint_button_custom_url");
	register_setting( "joliprint", "joliprint_button_position" );
	register_setting( "joliprint", "joliprint_button_home_position" );
	register_setting( "joliprint", "joliprint_button_post_position" );
	register_setting( "joliprint", "joliprint_button_page_position" );
	
	//register_setting( "joliprint", "joliprint_google_analytics_id"); // removed in 1.2.0
	register_setting( "joliprint", "joliprint_google_analytics_medium_name");
	register_setting( "joliprint", "joliprint_google_analytics_campaign_name");
	
	register_setting( "joliprint", "joliprint_pdflayout_option");
	register_setting( "joliprint", "joliprint_template_logo");
	register_setting( "joliprint", "joliprint_template_headertext");
	
	//register_setting( "joliprint", "joliprint_server_url"); // removed in 1.2.3
	register_setting( "joliprint", "joliprint_credits");
	register_setting( "joliprint", "joliprint_button_stylesheet"); // added in 1.2.0
	register_setting( "joliprint", "joliprint_ga_tracking");// added in 1.2.0
}
function joliprint_admin_warnings(){
	if( get_option( "joliprint_button_position" ) == null || get_option( "joliprint_button_position" ) == "" ){
		$pos = 0;
		if (isset( $_GET['page'] ) && $_GET['page'] != "" ){
			$pos = strpos( $_GET['page'] , "joliprint_admin_options");
		}
		if ( $pos == null || $pos == "" || $pos < 1 ) {
			function joliprint_install_warning() {
			echo "<div id='joliprint-warning' class='updated fade'><p><strong>".__('Joliprint is almost ready.','joliprint')."</strong> ".sprintf(__('Take some time to <a href="%1$s">complete and save your configuration</a>.','joliprint'), "options-general.php?page=joliprint/joliprint_admin_options.php")."</p></div>";
			}
			add_action('admin_notices', 'joliprint_install_warning');
			return;
		}
	}
}

function joliprint_register_plugin_links($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links[] = '<a href="options-general.php?page=joliprint/joliprint_admin_options.php">' . __('Settings','joliprint') . '</a>';
			$links[] = '<a target="_blank" href="mailto:hello@joliprint.com?subject=' . rawurlencode("[Wordpress Plugin " . JOLIPRINT_WPPLUGIN_VERSION . "] [" . site_url() . "]") . '">' . __('Support','joliprint') . '</a>';
		}
		return $links;
}
/*
	Add the Joliprint option panel to wordpress configuration panel 
*/
function joliprint_options_page() {
	// The options page is described in the joliprint/options.php page
	$pagehook = add_options_page('Joliprint Options', 'Joliprint', 10, 'joliprint/joliprint_admin_options.php');

}
function wp_head_joliprint(){
	if (is_single() || is_page()) {
		// load meta even if button is not show on the page. Useful for people using joliprint bookmarklets or magazine
		$post = &get_post( $id = 0 );
		echo joliprint_get_metas( $post );
	}
	if (wp_script_is( "wp_joliprint" ) == 1){
		echo "<script type='text/javascript' language='javascript'>";
		echo joliprint_get_button_configuration();
		echo "</script>";
		add_filter('the_content', 'joliprint_show_button');
	}
}

function joliprint_print_scripts(){
	$loadjs = false;
	if (get_option("joliprint_button_position") == "custom") $loadjs = true; // if the button is placed manually load the js by default as we don't know where
	if (get_option("joliprint_button_position") != "custom"){
		if ( is_single() && get_option( "joliprint_button_post_position" ) != 'none'){
			$loadjs = true;
		}else if( is_page() && get_option( "joliprint_button_page_position" ) != 'none' ){
			$loadjs = true;
		}else if( is_home() && get_option( "joliprint_button_home_position" ) != 'none' ){
			$loadjs = true;
		}else{
		}
	}
	if ($loadjs == false ) return;
	
	$joliprint_server_url = "http://" . JOLIPRINT_SERVER;
	if (is_ssl()) $joliprint_server_url = "https://" . JOLIPRINT_SERVER;
	wp_register_script( 'joliprintpopin', $joliprint_server_url . "/joliprint/js/popin/joliprint-popin.js.jspz" , null, null);
	wp_enqueue_script('joliprintpopin');	
	
	wp_register_script( 'joliprint_main', $joliprint_server_url . "/joliprint/js/joliprint.js" , array('joliprintpopin'), null);
	wp_enqueue_script('joliprint_main');
	
	//$script_location = apply_filters( 'at_files_uri',  plugins_url( '', basename(dirname(__FILE__)) ) ) . '/joliprint/js/wp_joliprint.js' ;
	$script_location = apply_filters( 'at_files_uri',  plugins_url( '', basename(dirname(__FILE__)) ) ) . '/joliprint/js/wp_joliprint-min.js' ;
	wp_register_script( 'wp_joliprint', $script_location , array('joliprint_main','jquery'), JOLIPRINT_WPPLUGIN_VERSION);
	wp_enqueue_script('wp_joliprint');
	
}

function joliprint_get_metas($post){
	$metas = "\n<meta name='joliprint.siteurl' content='" . site_url() . "' />";
	$authordata = get_userdata($post->post_author);
	$author = apply_filters('the_author', is_object($authordata) ? $authordata->display_name : null);
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$post_title = joliprint_escattr( $post->post_title );
	$post_author = joliprint_escattr( $author );
	$post_date = joliprint_escattr( $date );

	$metas .=  "\n<meta name='joliprint.title' content='" . $post_title . "' />";
	$metas .=  "\n<meta name='joliprint.author' content='" . $post_author . "' />";
	$metas .=  "\n<meta name='joliprint.date' content='" . $post_date . "' />";

	$joliprint_pdflayout_option = get_option( "joliprint_pdflayout_option" );
	switch( $joliprint_pdflayout_option ){
		case "logo":
			if ( get_option("joliprint_template_logo") ){
				$url_logo = get_option("joliprint_template_logo");
				if (strpos($url_logo, "/") == 0) $url_logo = site_url($url_logo, ( is_ssl() ? "https" : "http" ));
				$metas .=  "\n<meta name='joliprint.logo' content='" . $url_logo . "' />";
			}
			break;
		case "text":
			$joliprint_template_headertext = get_option("joliprint_template_headertext");
			if ( $joliprint_template_headertext != null && $joliprint_template_headertext != ""){
				$joliprint_template_headertext = htmlentities(stripslashes($joliprint_template_headertext),ENT_QUOTES, get_option('blog_charset'));
				$metas .=  "\n<meta name='joliprint.sitename' content='" . $joliprint_template_headertext. "' />";
			}
			break;
		default:
			$metas .=  "\n<meta name='joliprint.sitename' content='" . get_option( "blogname" ). "' />";
	}
	if ( get_option("joliprint_google_analytics_medium_name") && get_option( "joliprint_google_analytics_campaign_name" ) ){
		$metas .=  "\n<meta name='joliprint.google_analytics_medium_name' content='" . get_option("joliprint_google_analytics_medium_name") . "' />";
		$metas .=  "\n<meta name='joliprint.google_analytics_campaign_name' content='" . get_option("joliprint_google_analytics_campaign_name") . "' />";
		$postslug = joliprint_escattr( $post->post_name );
		$metas .= "\n<meta name='joliprint.google_analytics_postslug' content='" . $postslug . "' />";
	}

	return $metas;
}

function joliprint_show_the_button($url=null){
	if (get_option("joliprint_button_position") != "custom"){
		return;
	}
	if($url == null && !is_home() && !is_page() && !is_single()){
		$url = joliprint_getCurrentPageUrl();
	}
	return joliprint_getButton( $url );
}
function joliprint_show_button($content){
	
	if ( get_option("joliprint_button_position") == 'custom' ){
		if ( strpos( $content, '<!--nojoliprint-->' ) ){
			$content = $content . "<script type='text/javascript'>if(typeof(joliprint_button_config) && joliprint_button_config != null){joliprint_button_config._hide=true;}</script>";
		}
		return $content;
	}
	if( !is_home() && !is_page() && !is_single()) return $content;
	
	if (is_home() && get_option( "joliprint_button_home_position" ) == "none" ) {
		return $content;
	}else if(is_single() && get_option( "joliprint_button_post_position" ) == "none" ) {
		return $content;
	}else if( is_page() && get_option( "joliprint_button_page_position" ) == "none" ){
		return $content;
	}else if ( strpos( $content, '<!--nojoliprint-->' ) ) {
		return $content;
	}else{
		// continue
	}
	
	$where = null;
	if ( is_home() ) {
		$where = get_option( "joliprint_button_home_position" );
	}else if(is_single() ) {
		$where = get_option( "joliprint_button_post_position" );
	}else if( is_page() ){
		$where = get_option( "joliprint_button_page_position" );
	}else{
		// continue
	}
	if ($where == null ) $where = "after"; // default
	switch ($where) {
		case "manual":
			return $content;
			break;
		case "before" : 
			return joliprint_getButton() . $content;
			break;
		case "both" : 
			$button = joliprint_getButton();
			return $button . $content . $button;
			break;
		default :
			return $content . joliprint_getButton();
	}
}
function joliprint_get_button_configuration(){
	$button_type = get_option('joliprint_button_type');
	$show_list = get_option('joliprint_show_list');

	if ($url == null) $url = get_permalink();
	
	$catchdebug = "";
	if (defined( "JOLIPRINT_DEBUG" ) && JOLIPRINT_DEBUG == true){
		$catchdebug = "alert('joliprint error : ' + e.message);";
	}
	$joliprint_button_label = get_option("joliprint_button_label");
	if ( $joliprint_button_label != null && $joliprint_button_label != ""){
		$joliprint_button_label = htmlentities(stripslashes($joliprint_button_label),ENT_QUOTES, get_option('blog_charset'));
	}
	$joliprint_button_label_position = get_option("joliprint_button_label_position");
	if ( $joliprint_button_label_position == null || $joliprint_button_label_position == ""){
		$joliprint_button_label_position = "after";
	}
	
	$joliprint_params = array();
	switch ( $button_type ){
		case "joliprint-button":
			$joliprint_params["button"] = $button_type;
			break;
		case "joliprint-share-button":
			$joliprint_params["button"] = $button_type;
			break;
		case "joliprint-button-big":
			$joliprint_params["button"] = $button_type;
			break;
		case "joliprint-share-style":
			$joliprint_params["button"] = $button_type;
			break;
		case "joliprint-icon":
			$label = $joliprint_button_label;
			if ($label == "") $label = "joliprint";
			$joliprint_params["label"] = $label;
			$joliprint_params["labelposition"] = $joliprint_button_label_position;
			$joliprint_params["button"] = $button_type;
			break;
		case "joliprint-button-both":
			$label = $joliprint_button_label;
			if ($label == "") $label = "joliprint";
			
			$joliprint_params["label"] = $label;
			$joliprint_params["labelposition"] = $joliprint_button_label_position;
			$joliprint_params["button"] = $button_type;
			break;
		
		case "joliprint-button-textonly":
			$label = $joliprint_button_label;
			if ($label == null) $label = "joliprint";
			$joliprint_params["label"] = $label;
			$joliprint_params["labelposition"] = $joliprint_button_label_position;
			break;

		case "joliprint-button-custom":
			$label = $joliprint_button_label;
			if ($label == null) $label = "";
			$buttonurl = get_option("joliprint_button_custom_url");
			if (strpos($buttonurl, "/") == 0) $buttonurl = site_url( $buttonurl, ( is_ssl() ? "https" : "http" ) ); // relative url

			$joliprint_params["label"] = $label;
			$joliprint_params["labelposition"] = $joliprint_button_label_position;
			if ($buttonurl == null || $buttonurl == ''){
				//
			}else{
				$joliprint_params["buttonUrl"] = $buttonurl;
			}
			break;
		default:
			$joliprint_params["button"] = "joliprint-button";
	}
	
	
	$joliprint_ga_tracking = get_option("joliprint_ga_tracking");
	if ($joliprint_ga_tracking == null || $joliprint_ga_tracking == '') $joliprint_ga_tracking = '1';
	
	$wp_jp_config = "\nvar joliprint_button_config = {\n";
		$wp_jp_config .= "\t'urloptions':'ver=" . rawurlencode( JOLIPRINT_WPPLUGIN_VERSION ) . "'\n";
		if ( $joliprint_ga_tracking == "0" ){
			$wp_jp_config .= "\t,'_ga_tracking':false\n";
		}else{
			$wp_jp_config .= "\t,'_ga_tracking':true\n"; // DEFAULT
		}
		if (get_option( "joliprint_button_stylesheet" ) == "custom") $wp_jp_config .= "\t,'skipJoliprintCss':true\n";
		if (get_option( "joliprint_credits" ) == "false") $wp_jp_config .= "\t,'popin_logo_url':'none'\n";
		if ($joliprint_params["button"] != null) $wp_jp_config .= "\t,'button':'" . joliprint_escattr($joliprint_params["button"]) . "'\n";
		if ($joliprint_params["buttonUrl"] != null) $wp_jp_config .= "\t,'buttonUrl':'" . joliprint_escattr($joliprint_params["buttonUrl"]) . "'\n";
		if ($joliprint_params["label"] != null) $wp_jp_config .= "\t,'label':'" . joliprint_escattr($joliprint_params["label"]) . "'\n";
		if ($joliprint_params["labelposition"] != null) $wp_jp_config .= "\t,'labelposition':'" . joliprint_escattr($joliprint_params["labelposition"]) . "'\n";
		$wp_jp_config .= "};";

	return  $wp_jp_config;
}
function joliprint_getButton($url=null,$title=null){
	if ($url == null) $url = get_permalink();
	if ($title == null) $title = get_the_title();
	
	$title = joliprint_escattr( $title );
	$url = joliprint_escattr( $url );
	
	return "<span class=\"joliprint_button\" data-title=\"" . $title . "\" data-url=\"" . $url ."\"></span>";
}


function __esc_js( $text ) {
	if (function_exists( "esc_js" )){
		return esc_js($text);
	}else if( function_exists( "js_escape" ) ) {
		return js_escape( $text );
	}else{
		if (function_exists( "wp_check_invalid_utf8" )) $safe_text = wp_check_invalid_utf8( $text );
		if (function_exists( "_wp_specialchars" )) $safe_text = _wp_specialchars( $safe_text, ENT_COMPAT );
		$safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );
		$safe_text = str_replace( "\r", '', $safe_text );
		$safe_text = str_replace( "\n", '\\n', addslashes( $safe_text ) );
		return $safe_text;	
	}
}
function joliprint_getCurrentPageUrl(){
	return wp_guess_url();
	/*
	$pageURL = 'http://';
	if (is_ssl()){
		$pageURL = 'https://';
		if (isset($_SERVER["SERVER_PORT"]) && "443" != $_SERVER["SERVER_PORT"] ) {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
	}else{
		if (isset($_SERVER["SERVER_PORT"]) && "80" != $_SERVER["SERVER_PORT"] ) {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
	}
	return $pageURL . $_SERVER["REQUEST_URI"];
	*/
}
function joliprint_escattr( $text ){
	if ($text == null) return null;
	if (function_exists( "esc_attr" )){
		 $text = esc_attr( $text );
	}else{
		$text = attribute_escape( $text );
	}
	return $text;
}
?>