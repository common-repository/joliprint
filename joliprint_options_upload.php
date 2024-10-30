<?php
//require_once('../../../wp-admin/admin.php');
require( "../../../wp-load.php" );

if (!current_user_can('upload_files')) wp_die(__('You do not have permission to upload files.'));
if (!current_user_can('edit_plugins')) wp_die(__('You do not have permission to edit plugins.'));

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php echo $title; ?> &lsaquo; <?php bloginfo('name') ?>  &#8212; WordPress</title>
<?php

// if (function_exists("wp_admin_css")){
	// wp_admin_css( 'css/global' );
	// wp_admin_css();
	// wp_admin_css( 'css/colors' );
	// wp_admin_css( 'css/ie' );
// }
// do_action('admin_print_styles');
?>
<style>
html,body{
	width:90%;
	height:90%;
}
</style>

<script type="text/javascript">
function hideme(type, filename, duration){
	if (duration == null) duration = 3000;
	var t = 0;
	var _int = window.setInterval( function(){
		t+=1000;
		if (t >= duration){
			window.clearInterval(_int);
			parent.tb_remove();
		}
	},1000);
	if (type == null) return;
	if (filename == null) return;
	
	var url = "<?php echo JOLIPRINT_OPTIONS_UPLOAD_DIR ;?>" + filename;
	if (type == 'logo'){
		try{
			parent.document.getElementById('joliprint_template_logo').value = url;
			parent.document.forms['frm_joliprint_options'].submit();
		}catch(e){
		}
	}else if( type == 'button' ){
		try{
			parent.document.getElementById('joliprint_button_custom_url').value = url;
			parent.document.forms['frm_joliprint_options'].submit();
		}catch(e){
		}
	}else{
	}
	
	
}
</script>
</head>
<body>
<div id="wpbody-content" style="text-align:center;vertical-align:middle">


<?php
	$available_types = array("logo","button");
	$type = "";
	if (isset( $_GET['type'] ) && in_array(strtolower(htmlentities($_GET['type'])), $available_types)) $type = strtolower(htmlentities($_GET['type']));
	// $type = $_GET['type'];
	$ext_autorises = "";
	$filename = "";
	$extension = "";
	switch ($type) {
		case "logo":
			$ext_autorises = JOLIPRINT_OPTIONS_LOGO_EXT;
			$filename = "logo";
			break;
		case "button":
			$ext_autorises = JOLIPRINT_OPTIONS_BUTTON_EXT;
			$filename = "button";
			break;
		default:
			die("This type is not authorized, sorry");
			break;
	}
	$ext_autorises = explode(",",$ext_autorises);
	
	if (is_uploaded_file($_FILES['fileupload']['tmp_name'])){
		$ext_fichier = explode(".",$_FILES['fileupload']['name']);
		$ext_autorise_ok = false ;

		foreach ($ext_autorises as $ext_autorise){
			if ($ext_autorise == end($ext_fichier))
			{
				$extension = $ext_autorise;
				$ext_autorise_ok = true ;
				break ;
			}
		}
		if ($ext_autorise_ok){
			$source = $_FILES[fileupload][tmp_name];
			
			$destdir = ABSPATH.'/'. JOLIPRINT_OPTIONS_UPLOAD_DIR;
			if ( !is_dir($destdir) ){
				$makedir = mkdir($destdir);
				if (!is_dir($destdir)){
					echo __("The upload directory doesn't exists on your server and the plugin failed to create it.<br><strong>Please create a joliprint directory in your /wp-content/uploads/ directory.</strong>", "joliprint") ."<script type='text/javascript'>hideme(null,null, 10000);</script>";
					return;
				}
			}

			$dest = $destdir . "/" . $filename . "." . $extension;
			if ($source){
				copy($source,$dest);
			}
			echo __("<p style='color:green'>Your file has been sent to the server, thank you.</p>", "joliprint") . "<script type='text/javascript'>hideme('" . $type . "', '" . $filename . "." . $extension . "');</script>";
			unlink($source);
		}else{
			echo __("<p style='color:red'>This action is not authorized, we are sorry.</p>", "joliprint") ."<script type='text/javascript'>hideme(null,null);</script>";
		}
	}else{
?>
	 <form name="form_upload" method="post" enctype="multipart/form-data">
		<h2 style='border-bottom:1px solid'><?php _e("Please choose a file to upload","joliprint");?></h2>
		
		<input type="file" name="fileupload" id="upfile_0" size="10" tabindex="1" />
		<input align="center" type="submit" name="envoyer" value="envoyer" />
	</form>
<?php
	}
?>
</div>

<body>
</html>
