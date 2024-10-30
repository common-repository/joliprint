<?php
require_once( ABSPATH . "wp-content/plugins/joliprint/joliprint_constants.php");
function joliprint_activate(){
	update_option( "joliprint_install_date",current_time( 'mysql' ));
}

function joliprint_deactivate(){
}
function joliprint_uninstall() {
	/* remove joliprint options */
	delete_option( "joliprint_install_date" );
	delete_option( "joliprint_button_type");
	delete_option( "joliprint_button_label");
	delete_option( "joliprint_button_label_position");
	delete_option( "joliprint_button_custom_url");
	delete_option( "joliprint_button_position" );
	delete_option( "joliprint_button_home_position" );
	delete_option( "joliprint_button_post_position" );
	delete_option( "joliprint_button_page_position" );
	
	delete_option( "joliprint_google_analytics_id");
	delete_option( "joliprint_google_analytics_medium_name");
	delete_option( "joliprint_google_analytics_campaign_name");
	
	delete_option( "joliprint_pdflayout_option");
	delete_option( "joliprint_template_logo");
	delete_option( "joliprint_template_headertext");
	
	delete_option( "joliprint_server_url");
	delete_option( "joliprint_credits");
	delete_option( "joliprint_button_stylesheet");
	delete_option( "joliprint_ga_tracking");
	
	/* remove logo and custom button and joliprint upload directory */
	
	$joliprintdir = ABSPATH . JOLIPRINT_OPTIONS_UPLOAD_DIR;
	if (is_dir($joliprintdir)) rrmdir($joliprintdir);

}
function rrmdir($dir) {
	if (is_dir($dir)) {
		if (function_exists( "scandir" )){
			$objects = scandir($dir);
		}else{
			$objects = php4_scandir($dir);
		}
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function php4_scandir($dir,$listDirectories=false, $skipDots=true) {
    $dirArray = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (($file != "." && $file != "..") || $skipDots == true) {
                if($listDirectories == false) { if(is_dir($file)) { continue; } }
                array_push($dirArray,basename($file));
            }
        }
        closedir($handle);
    }
    return $dirArray;
}
?>