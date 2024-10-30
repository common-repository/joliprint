<?php
require_once( ABSPATH . 'wp-content/plugins/joliprint/joliprint_constants.php' );

$location = $options_page; // Form Action URI
/* Check for admin Options submission and update options*/

$availables_options = array("button","pdflayout","tech","ga"); // only these options are authorized here ...
global $currentopt;
$currentopt = "button";
if (isset( $_GET['opt'] ) && in_array(strtolower(htmlentities($_GET['opt'])), $availables_options)) $currentopt = strtolower(htmlentities($_GET['opt']));


//add_filter('screen_layout_columns', 'joliprint_on_screen_layout_columns', 10, 2);
wp_enqueue_script('postbox');
add_meta_box( "joliprint_support_us", __("Support us","joliprint"), "joliprint_supportus_metabox", "joliprint", 'side', 'core');
add_meta_box( "joliprint_options_frm_button", __("Choose your button","joliprint"), "joliprint_options_frm_button", "joliprint_frm_button", 'normal', 'core');
add_meta_box( "joliprint_options_frm_pdflayout", __("Header options","joliprint"), "joliprint_options_frm_pdflayout", "joliprint_frm_pdflayout", 'normal', 'core');
add_meta_box( "joliprint_options_frm_tech", __("Joliprint Server Options","joliprint"), "joliprint_options_frm_tech", "joliprint_frm_tech", 'normal', 'core');
add_meta_box( "joliprint_options_frm_ga", __("Google Analytics Tracking Options","joliprint"), "joliprint_options_frm_ga", "joliprint_frm_ga", 'normal', 'core');


if ('process_credits' == $_POST['stage']) {
	if ( isset( $_POST['joliprint_credits'] ) ){
		update_option('joliprint_credits', $_POST['joliprint_credits']);		
	}
}else if ('process' == $_POST['stage']) {
	switch($currentopt){
		case "button":
			if ( isset( $_POST['joliprint_button_type'] ) ){
				update_option('joliprint_button_type', $_POST['joliprint_button_type']);		
			}
			if ( isset( $_POST['joliprint_button_label'] ) ){
				update_option('joliprint_button_label', $_POST['joliprint_button_label']);
			}
			if ( isset( $_POST['joliprint_button_label_position'] ) ){
				update_option('joliprint_button_label_position', $_POST['joliprint_button_label_position']);
			}
			
			if ( isset( $_POST['joliprint_button_custom_url'] ) ){
				update_option('joliprint_button_custom_url', $_POST['joliprint_button_custom_url']);
			}
			if ( isset( $_POST['joliprint_button_position'] ) ){
				update_option('joliprint_button_position', $_POST['joliprint_button_position']);		
			}
			if ( isset( $_POST['joliprint_button_home_position'] ) ){
				update_option('joliprint_button_home_position', $_POST['joliprint_button_home_position']);		
			}
			if ( isset( $_POST['joliprint_button_post_position'] ) ){
				update_option('joliprint_button_post_position', $_POST['joliprint_button_post_position']);		
			}
			if ( isset( $_POST['joliprint_button_page_position'] ) ){
				update_option('joliprint_button_page_position', $_POST['joliprint_button_page_position']);		
			}
			if ( isset( $_POST['joliprint_button_stylesheet'] ) ){
				update_option('joliprint_button_stylesheet', $_POST['joliprint_button_stylesheet']);
			}
			
			
			break;
			
		case "pdflayout":
			$resetcache = false;
			if ( isset( $_POST['joliprint_pdflayout_option'] ) ){
				$joliprint_pdflayout_option = $_POST['joliprint_pdflayout_option'];
				
				switch($joliprint_pdflayout_option){
					case 'text':
						if (!isset($_POST['joliprint_template_headertext']) || trim($_POST['joliprint_template_headertext']) == ''){
							$joliprint_pdflayout_option = "default";
						}
						break;
					case 'logo':
						if (!isset($_POST['joliprint_template_logo']) || trim($_POST['joliprint_template_logo']) == ''){
							$joliprint_pdflayout_option = "default";
						}
						break;
					default:
						$joliprint_pdflayout_option = "default";
				}
				if( get_option("joliprint_pdflayout_option") != $joliprint_pdflayout_option ){
					$resetcache = true;
				}
				update_option('joliprint_pdflayout_option', $joliprint_pdflayout_option);
			}
			if ( isset( $_POST['joliprint_template_logo'] ) ){
				if( $resetcache == false && $joliprint_pdflayout_option == 'logo' && get_option("joliprint_template_logo") != $_POST['joliprint_template_logo'] ){
					$resetcache = true;
				}
				update_option('joliprint_template_logo', trim($_POST['joliprint_template_logo']));
			}
			if ( isset( $_POST['joliprint_template_headertext'] ) ){
				if( $resetcache == false && $joliprint_pdflayout_option == 'text' && get_option("joliprint_template_headertext") != $_POST['joliprint_template_headertext'] ){
					$resetcache = true;
				}
				update_option('joliprint_template_headertext', trim($_POST['joliprint_template_headertext']));
			}
			if ($resetcache == true){
				$updated = joliprint_cache_update(null);
				if ( $updated == false ){
					echo joliprint_resetcache_warning();
				}else{
					echo joliprint_resetcache_done();
				}
			}
			break;
		case "ga":
			if ( isset( $_POST['joliprint_google_analytics_medium_name'] ) ){
				update_option('joliprint_google_analytics_medium_name', $_POST['joliprint_google_analytics_medium_name']);
			}
			if ( isset( $_POST['joliprint_google_analytics_campaign_name'] ) ){
				update_option('joliprint_google_analytics_campaign_name', $_POST['joliprint_google_analytics_campaign_name']);
			}
			if ( isset( $_POST['joliprint_ga_tracking'] ) ){
				update_option('joliprint_ga_tracking', $_POST['joliprint_ga_tracking']);
			}
			break;
		default:
			break;
	}
}

global $joliprint_server_url;
$joliprint_server_url = "http://" . JOLIPRINT_SERVER;
if (is_ssl()) $joliprint_server_url = "https://" . JOLIPRINT_SERVER;

global $btnpath;
$btnpath = $joliprint_server_url . "/res/joliprint/img/buttons/default";

global $baseLocation ;
$baseLocation = "admin.php?page=joliprint/joliprint_admin_options.php";

global $joliprint_credits;
$joliprint_credits = get_option('joliprint_credits');
if ( $joliprint_credits == null || $joliprint_credits == '' ) $joliprint_credits = "true";

?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var joliprint_upload_options = "&amp;TB_iframe=true&amp;width=360&amp;height=140";
		jQuery('#upload_logo_button').click(function() {
			document.getElementById("joliprint_header_option_logo").checked = "checked";
			tb_show("", "<?php echo site_url( "/wp-content/plugins/joliprint/joliprint_options_upload.php?type=logo", ( is_ssl() ? "https" : "http" ));?>" + joliprint_upload_options);
		});
		jQuery('#upload_button_button').click(function() {
			document.getElementById("radio_ws-joliprint-button-custom").checked = "checked";
			tb_show("", "<?php echo site_url( "/wp-content/plugins/joliprint/joliprint_options_upload.php?type=button", ( is_ssl() ? "https" : "http" ));?>" + joliprint_upload_options);
		});

	});
	function joliprint_cache_reset(){
		try{
			
			var _url = "<?php echo site_url( "/wp-content/plugins/joliprint/joliprint_ajax.php?do=cache_reset&callback=?", ( is_ssl() ? "https" : "http" ) );?>";
			jQuery( "#joliprint_cache_status" ).html( "<?php echo htmlentities(__("Please wait","joliprint"));?>&nbsp;<img src='<?php echo site_url( "/wp-content/plugins/joliprint/img/ajax-loader.gif", ( is_ssl() ? "https" : "http" ));?>' border='0'/>" );
			jQuery.ajax({
				url: _url,
				dataType:"jsonp",
				type:"GET",
				success:function( json, textStatus, XMLHttpRequest ){
					if (json == null){
						jQuery( "#joliprint_cache_status" ).html( "<span class='joliprint_cache_status_error'><?php echo __esc_js(__( "An error occured during the communication with the joliprint server. Please try again later.","joliprint" ) );?></span>" );
					}
					
					try{
						if (json.status_code == '200'){
							jQuery( "#joliprint_cache_status" ).html( "<span class='joliprint_cache_status_ok'><?php echo __esc_js(__("PDF Cache erased.","joliprint"));?></span>" );	
						}else{
							jQuery( "#joliprint_cache_status" ).html( "<span class='joliprint_cache_status_error'>" + json.status_txt + "</span>" );	
						}
					}catch(e){
						alert(e.message);
					}
				},
				error:function(XMLHttpRequest, textStatus, errorThrown){
					try{
						jQuery( "#joliprint_cache_status" ).html( "<span class='joliprint_cache_status_error'>An error has occurred making the request: " + errorThrown);
					}catch(e){
						alert(e.message);
					}
				}
			});
		}catch(e){	
			alert(e.message);
		}
	}
</script>
<style>
.joliprint_cache_status_error{
	color:red;
}
</style>

		<div class="wrap">
			<?php if (function_exists( "screen_icon" )) screen_icon('options-general'); ?>
			<h2><?php _e('Joliprint Options', 'joliprint') ?></h2>
			<ul class="subsubsub">
				<li><a class='<?php if( $currentopt == 'button' ){echo "current";}?>' href='<?php echo admin_url($baseLocation . "&opt=button");?>'><?php _e("Button", "joliprint");?></a> |</li>
				<li><a class='<?php if( $currentopt == 'pdflayout' ){echo "current";}?>' href='<?php echo admin_url($baseLocation . "&opt=pdflayout");?>'><?php _e("PDF layout", "joliprint");?></a> |</li>
				<li><a class='<?php if( $currentopt == 'tech' ){echo "current";}?>' href='<?php echo admin_url($baseLocation . "&opt=tech");?>'><?php _e("Technical options", "joliprint");?></a> |</li>
				<li><a class='<?php if( $currentopt == 'ga' ){echo "current";}?>' href='<?php echo admin_url($baseLocation . "&opt=ga")?>'><?php _e("Analytics", "joliprint");?></a></li>
			</ul>
<br style="clear:both"/>
	<form id="frm_joliprint_credits" name="frm_joliprint_credits" method="post" action="<?php echo $baseLocation;?>&opt=<?php echo $currentopt; ?>&updated=true">
		<input type="hidden" name="stage" value="process_credits" />
		<input type='hidden' id='joliprint_credits' name='joliprint_credits' value='<?php echo $joliprint_credits?>' />
	</form>
			<form id="frm_joliprint_options" name="frm_joliprint_options_<?php echo $currentopt; ?>" method="post" action="<?php echo $baseLocation;?>&opt=<?php echo $currentopt; ?>&updated=true">
			<input type="hidden" name="stage" value="process" />
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="side-info-column" class="inner-sidebar" >
					<?php do_meta_boxes("joliprint", 'side', $data);?>
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php 
						wp_nonce_field('update-options');
						switch($currentopt){
							case "button":
								//joliprint_options_frm_button();
								do_meta_boxes("joliprint_frm_button", "normal", $data);
								break;
							
							case "pdflayout":
								do_meta_boxes("joliprint_frm_pdflayout", "normal", $data);
								//joliprint_options_frm_pdflayout();
								break;
							
							case "tech":
								do_meta_boxes("joliprint_frm_tech", "normal", $data);
								//joliprint_options_frm_tech();
								break;
							case "ga":
								do_meta_boxes("joliprint_frm_ga", "normal", $data);
								//joliprint_options_frm_ga();
								break;
							default:
								break;
						}
					?>
				<p class="submit">
					<input type="submit" name="Submit" value="<?php _e('Save Changes', 'joliprint') ?>" />
				</p>
				</div>
			</div>	
		</div>
		</form>
		
	
<?php

function joliprint_options_frm_button(){
	global $btnpath, $currentopt, $baseLocation, $joliprint_server_url;
	$script .= "<script type='text/javascript'>";
	$script .= "document.write(unescape(\"%3Cscript src='" . $joliprint_server_url . "/joliprint/js/joliprint.js' type='text/javascript'%3E%3C/script%3E\"));";
	$script .= "</script>";
	echo $script;
	
	$joliprint_button_type = get_option( "joliprint_button_type" );
	if ($joliprint_button_type == null || $joliprint_button_type == ''){
		$joliprint_button_type = "joliprint-button";
	}

	$joliprint_button_label = get_option("joliprint_button_label");
	if ( $joliprint_button_label != null && $joliprint_button_label != ""){
		$joliprint_button_label = htmlentities(stripslashes($joliprint_button_label),ENT_QUOTES, get_option('blog_charset'));
	}
	
	$joliprint_button_label_position = get_option("joliprint_button_label_position");
	if ( $joliprint_button_label_position == null || $joliprint_button_label_position == ""){
		$joliprint_button_label_position = "after";
	}
	
	$joliprint_button_custom_url = get_option("joliprint_button_custom_url");
	if ( $joliprint_button_custom_url != null && $joliprint_button_custom_url != ""){
		$joliprint_button_custom_url = htmlentities(stripslashes($joliprint_button_custom_url),ENT_QUOTES, get_option('blog_charset'));
	}
	
?>
	
	<table cellspacing="2" cellpadding="5" class="form-table" >
		<tr>
			<td style="vertical-align:middle">
			  <input name="joliprint_button_type" id="radio_ws-joliprint-button" value="joliprint-button" type="radio" <?php if( $joliprint_button_type == 'joliprint-button' ){echo "checked='checked'";} ; ?> style="border:none" />
				<?php echo "<script type='text/javascript'>\n<!--\ntry{\n\$joliprint().set('url',null).set('button', 'joliprint-button').set('title','" . htmlentities(__("Print with Joliprint","joliprint"),ENT_QUOTES, get_option('blog_charset')) . "').ignoreStats().write();\n}catch(e){\n".$catchdebug."\n}\n--></script>";?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle">
			  <input name="joliprint_button_type" id="radio_ws-joliprint-share-button" value="joliprint-share-button" type="radio" <?php if( $joliprint_button_type == 'joliprint-share-button' ){echo "checked='checked'";} ; ?> style="border:none" />
				<?php echo "<script type='text/javascript'>\n<!--\ntry{\n\$joliprint().set('url',null).set('button', 'joliprint-share-button').set('title','" . htmlentities(__("Print with Joliprint","joliprint"),ENT_QUOTES, get_option('blog_charset')) . "').ignoreStats().write();\n}catch(e){\n".$catchdebug."\n}\n--></script>";?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle">
			  <input name="joliprint_button_type" id="radio_ws-joliprint-share-style" value="joliprint-share-style" type="radio" <?php if( $joliprint_button_type == 'joliprint-share-style' ){echo "checked='checked'";} ; ?> style="border:none" />
				<?php echo "<script type='text/javascript'>\n<!--\ntry{\n\$joliprint().set('url',null).set('button', 'joliprint-share-style').set('title','" . htmlentities(__("Print with Joliprint","joliprint"),ENT_QUOTES, get_option('blog_charset')) . "').ignoreStats().write();\n}catch(e){\n".$catchdebug."\n}\n--></script>";?>
			</td>
		</tr>
		</table>
		<table cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="baseline">
			<td style="vertical-align:middle" width="40%">
				<input name="joliprint_button_type" id="radio_ws-joliprint-icon" value="joliprint-icon" type="radio" <?php if( $joliprint_button_type == 'joliprint-icon' ){echo "checked='checked'";} ; ?> style="border:none" />
				<?php
					$label = $joliprint_button_label;
					if ($label == "") $label = "joliprint";
					echo "<script type='text/javascript'>\n<!--\ntry{\n\$joliprint().set('url',null).set('button', 'joliprint-icon').set('label','" . $label . "').set('labelposition','" . $joliprint_button_label_position . "').set('title','" . htmlentities(__("Print with Joliprint","joliprint"),ENT_QUOTES, get_option('blog_charset')) . "').ignoreStats().write();\n}catch(e){\n".$catchdebug."\n}\n--></script>";
				?>
			</td>
			<td rowspan="4" style="width:60%;border-left:1px solid #c3c3c3;vertical-align:middle;text-align:left">
				<label><?php _e("Button label", "joliprint");?></label><br/>
				<input id="joliprint_button_label" name="joliprint_button_label"  value="<?php echo $joliprint_button_label;?>" type="text" />
				<br/>
				<small><?php _e("Hint : if you don't want any label for your button, leave a blank space in the label text field","joliprint");?></small>
				<br/><br/>
				<label><?php _e("Label position", "joliprint");?></label><br/>
				<input type="radio" id="joliprint_button_label_position_after" name="joliprint_button_label_position" value="after" <?php if( $joliprint_button_label_position == 'after' ){echo "checked='checked'";} ; ?> style="border:none" />
				<label for="joliprint_button_label_position_after"><?php _e("After the image", "joliprint");?></label><br/>
				<input type="radio" id="joliprint_button_label_position_before" name="joliprint_button_label_position" value="before" <?php if( $joliprint_button_label_position == 'before' ){echo "checked='checked'";} ; ?> style="border:none" />
				<label for="joliprint_button_label_position_before"><?php _e("Before the image", "joliprint");?></label><br/>
			</td>
		</tr>
		<tr valign="baseline">
			<td style="vertical-align:middle">
				<input name="joliprint_button_type"  id="radio_ws-joliprint-button-both" value="joliprint-button-both" type="radio" <?php if( $joliprint_button_type == 'joliprint-button-both' ){echo "checked='checked'";} ; ?> style="border:none" />
				<?php
					$label = $joliprint_button_label;
					if ($label == "") $label = "joliprint";
					echo "<script type='text/javascript'>\n<!--\ntry{\n\$joliprint().set('url',null).set('button', 'joliprint-button-both').set('label','" . $label . "').set('labelposition','" . $joliprint_button_label_position . "').set('title','" . htmlentities(__("Print with Joliprint","joliprint"),ENT_QUOTES, get_option('blog_charset')) . "').ignoreStats().write();\n}catch(e){\n".$catchdebug."\n}\n--></script>";
				?>
				
			</td>
		</tr>
		<tr valign="baseline">
			<td style="vertical-align:middle">
				<input name="joliprint_button_type"  id="radio_ws-joliprint-button-textonly" value="joliprint-button-textonly" type="radio" <?php if( $joliprint_button_type == 'joliprint-button-textonly' ){echo "checked='checked'";} ; ?> style="border:none" />
				<label><?php _e("Text link","joliprint");?> :</label>	  
				<?php
					$label = $joliprint_button_label;
					if ($label == "") $label = "joliprint";
					echo "<script type='text/javascript'>\n<!--\ntry{\n\$joliprint().set('url',null).set('label','" . $label . "').set('labelposition','" . $joliprint_button_label_position . "').set('title','" . htmlentities(__("Print with Joliprint","joliprint"),ENT_QUOTES, get_option('blog_charset')) . "').ignoreStats().write();\n}catch(e){\n".$catchdebug."\n}\n--></script>";
				?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle">
				<input name="joliprint_button_type"  id="radio_ws-joliprint-button-custom" value="joliprint-button-custom" type="radio" <?php if( $joliprint_button_type == 'joliprint-button-custom' ){echo "checked='checked'";} ; ?> style="border:none" />
				<label><?php _e("Use your own button","joliprint");?></label>	  
				<br/><input id="joliprint_button_custom_url" name="joliprint_button_custom_url"  value="<?php echo $joliprint_button_custom_url;?>" type="text" />
				<input type="button" id="upload_button_button" value="<?php _e("Upload your button","joliprint");?>" />
				
			</td>
		</tr>
		</table>
	
	<?php
		$joliprint_button_position = get_option( "joliprint_button_position" );
		if ( $joliprint_button_position == null || $joliprint_button_position == '' ){
			$joliprint_button_position = "standard";
		}
		$joliprint_button_home_position = get_option( "joliprint_button_home_position" );
		if ( $joliprint_button_home_position == null || $joliprint_button_home_position == '' ){
			$joliprint_button_home_position = "after";
		}
		$joliprint_button_post_position = get_option( "joliprint_button_post_position" );
		if ( $joliprint_button_post_position == null || $joliprint_button_post_position == '' ){
			$joliprint_button_post_position = "after";
		}
		$joliprint_button_page_position = get_option( "joliprint_button_page_position" );
		if ( $joliprint_button_page_position == null || $joliprint_button_page_position == '' ){
			$joliprint_button_page_position = "after";
		}
		$joliprint_button_stylesheet = get_option( "joliprint_button_stylesheet" );
		if ( $joliprint_button_stylesheet == null || $joliprint_button_stylesheet == '' ){
			$joliprint_button_stylesheet = "default";
		}
		
	?>
	<script type='text/javascript'>
		function joliprint_toggle_select(){
			
			if( document.getElementById("joliprint_button_position_custom").checked == true ){
				document.getElementById("joliprint_button_home_position").disabled = "disabled";
				document.getElementById("joliprint_button_post_position").disabled = "disabled";
				document.getElementById("joliprint_button_page_position").disabled = "disabled";
			}else{
				document.getElementById("joliprint_button_home_position").disabled = "";
				document.getElementById("joliprint_button_post_position").disabled = "";
				document.getElementById("joliprint_button_page_position").disabled = "";
			}
		}
		jQuery(document).ready(function(){
			joliprint_toggle_select();
		});
	</script>
	<div>
		<h4><?php _e('Button styling','joliprint'); ?></h4>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="baseline">
			<td style="vertical-align:top">
				<input type="radio" name="joliprint_button_stylesheet" id="joliprint_button_stylesheet_default" value="default" <?php if ($joliprint_button_stylesheet == null || $joliprint_button_stylesheet != 'custom'){echo "checked='checked'";}?>/>
				<label for="joliprint_button_stylesheet_default"><?php _e('Use default Joliprint CSS stylesheet ','joliprint'); ?></label><br/>
			</th>
		</tr>
		<tr valign="baseline">
			<td style="vertical-align:top">
				<input type="radio" name="joliprint_button_stylesheet" id="joliprint_button_stylesheet_custom" value="custom" <?php if ($joliprint_button_stylesheet == 'custom'){echo "checked='checked'";}?>/>
				<label for="joliprint_button_stylesheet_custom"><?php _e('Skip default Joliprint CSS stylesheet ','joliprint'); ?></label>
			</th>
		</tr>
		</table>
	</div>
	<div>
		<h4><?php _e('Button position','joliprint'); ?></h4>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="baseline">
			<th style="vertical-align:top" colspan="2">
				<input onclick='joliprint_toggle_select();' type="radio" name="joliprint_button_position" id="joliprint_button_position_standard" value="standard" <?php if ($joliprint_button_position == 'standard'){echo "checked='checked'";}?>/>
				&nbsp;<label for="joliprint_button_position_standard"><?php _e( "Standard position", "joliprint" );?></label>
			</th>
		</tr>

		<tr valign="baseline">
			<th style="vertical-align:top;padding-left:40px">
				<label for="joliprint_button_home_position"><?php _e( "&raquo; Homepage", "joliprint" );?></label>
			</th>
			<td>
				<select id="joliprint_button_home_position" name="joliprint_button_home_position" >
					<option value="none" <?php if( $joliprint_button_home_position == 'none'){ echo "selected='selected'";} ?> ><?php _e("Button not displayed","joliprint")?></option>
					<option value="after" <?php if( $joliprint_button_home_position == 'after'){ echo "selected='selected'";} ?> ><?php _e("After the text","joliprint")?></option>
					<option value="before" <?php if( $joliprint_button_home_position == 'before'){ echo "selected='selected'";} ?> ><?php _e("Before the text","joliprint")?></option>
					<option value="both" <?php if( $joliprint_button_home_position == 'both'){ echo "selected='selected'";} ?> ><?php _e("Before and after the text","joliprint")?></option>
				</select>
			</td>
		</tr>	
		<tr valign="baseline">
			<th style="vertical-align:top;padding-left:40px">
				<label for="joliprint_button_post_position"><?php _e('&raquo; Posts','joliprint'); ?></label>
			</th>
			<td>
				<select id="joliprint_button_post_position" name="joliprint_button_post_position" >
					<option value="none" <?php if( $joliprint_button_post_position == 'none'){ echo "selected='selected'";} ?> ><?php _e("Button not displayed","joliprint")?></option>
					<option value="after" <?php if( $joliprint_button_post_position == 'after'){ echo "selected='selected'";} ?> ><?php _e("After the text","joliprint")?></option>
					<option value="before" <?php if( $joliprint_button_post_position == 'before'){ echo "selected='selected'";} ?> ><?php _e("Before the text","joliprint")?></option>
					<option value="both" <?php if( $joliprint_button_post_position == 'both'){ echo "selected='selected'";} ?> ><?php _e("Before and after the text","joliprint")?></option>
				</select>
			</td>
		</tr>	
		<tr valign="baseline">
			<th style="vertical-align:top;padding-left:40px">
				<label for="joliprint_button_page_position"><?php _e( "&raquo; Pages", "joliprint" );?></label>
			</th>
			<td>
				<select id="joliprint_button_page_position" name="joliprint_button_page_position" >
					<option value="none" <?php if( $joliprint_button_page_position == 'none'){ echo "selected='selected'";} ?> ><?php _e("Button not displayed","joliprint")?></option>
					<option value="after" <?php if( $joliprint_button_page_position == 'after'){ echo "selected='selected'";} ?> ><?php _e("After the text","joliprint")?></option>
					<option value="before" <?php if( $joliprint_button_page_position == 'before'){ echo "selected='selected'";} ?> ><?php _e("Before the text","joliprint")?></option>
					<option value="both" <?php if( $joliprint_button_page_position == 'both'){ echo "selected='selected'";} ?> ><?php _e("Before and after the text","joliprint")?></option>
					
				</select>
			</td>
		</tr>	
		<tr valign="baseline">
			<th style="vertical-align:top" colspan="2">
				<input onclick='joliprint_toggle_select()' type="radio" name="joliprint_button_position" id="joliprint_button_position_custom" value="custom" <?php if ($joliprint_button_position == 'custom'){echo "checked='checked'";}?>/>
				&nbsp;<label for="joliprint_button_position_custom"><?php _e( "Custom position", "joliprint" );?></label>
			</th>
		</tr>
		<tr valign="baseline">
			<td style="vertical-align:top;padding-left:40px" colspan="2">
				<?php _e( "insert the following PHP code in your templates where you want to display your button<br/><code>&lt;?php if ( function_exists( 'joliprint_show_the_button' ) ) echo joliprint_show_the_button(); ?&gt;</code>","joliprint" ); ?>
				<?php _e( "<br/>or you can insert the following HTML code somewhere in your templates/widgets if you are able to provide the url and title within the widget<br/><code>&lt;span class='joliprint_button' data-url='[url]' data-title='[title]'&gt;&lt;/span&gt;</code>","joliprint" ); ?>
				<?php _e( "<br/>if you are not sure about the title and url you can simply use <code>&lt;span class='joliprint_button'&gt;&lt;/span&gt;</code>. Joliprint will try to find url and title by itself but Google Analytics tracking should not be correct.", "joliprint" ); ?>
				<br/><br/><small><?php _e( "Warning : Do not forget to remove this code from your pages if you deactivate or remove the Joliprint plugin.","joliprint" ); ?></small>
			</td>
		</tr>
		</table>
	</div>
<?php
}

function joliprint_options_frm_pdflayout($data){
	global $btnpath, $currentopt, $baseLocation;
	$joliprint_template_logo = get_option( "joliprint_template_logo" );
	if ($joliprint_template_logo == null ) $joliprint_template_logo = "";

	$joliprint_template_headertext = get_option("joliprint_template_headertext");
	if ( $joliprint_template_headertext != null && $joliprint_template_headertext != ""){
		$joliprint_template_headertext = htmlentities(stripslashes($joliprint_template_headertext),ENT_QUOTES, get_option('blog_charset'));
	}
	
	$joliprint_pdflayout_option = get_option( "joliprint_pdflayout_option" );
	if ( $joliprint_pdflayout_option == null || $joliprint_pdflayout_option == ''){
		$joliprint_pdflayout_option = "default";
	}
	?>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="baseline">
			<th scope="row"> 
				<input type="radio" id="joliprint_header_option_default" name="joliprint_pdflayout_option" value="default" <?php if( $joliprint_pdflayout_option == 'default' ) echo "checked='checked'";?> style="border:none" />
				<label for="joliprint_header_option_default"><?php _e("Default header", "joliprint"); ?></label>
			</th>
			<td>
				<?php
					$blogname = get_option("blogname");
					echo sprintf(__('Based on your blogname : <strong>%1$s</strong>', 'joliprint'), $blogname);
				?>
			</td>
		</tr>
		<tr valign="baseline">
			<th scope="row"> 
				<input type="radio" id="joliprint_header_option_text" name="joliprint_pdflayout_option" value="text" <?php if( $joliprint_pdflayout_option == 'text' ) echo "checked='checked'";?> style="border:none" />
				<label for="joliprint_header_option_text"><?php _e("Custom text header", "joliprint"); ?></label>
			</th> 
			<td> 
				<input type="text" name="joliprint_template_headertext" value="<?php echo $joliprint_template_headertext;?>" size="60" onfocus="document.getElementById('joliprint_header_option_text').checked = 'checked';"/> 
			</td> 
		</tr>

		<tr valign="baseline">
			<th scope="row"> 
				<input type="radio" id="joliprint_header_option_logo" name="joliprint_pdflayout_option" value="logo" <?php if( $joliprint_pdflayout_option == 'logo' ) echo "checked='checked'";?> style="border:none" />
				<label for="joliprint_header_option_logo"><?php _e("Graphic header", "joliprint"); ?></label>
			</th> 
			<td> 
				<input type="text" id="joliprint_template_logo" name="joliprint_template_logo" value="<?php echo $joliprint_template_logo ?>" size="60" onfocus="document.getElementById('joliprint_header_option_logo').checked = 'checked';" /> 
				<input type="button" id="upload_logo_button" value="<?php _e("Upload your image", "joliprint");?>" />
				<br/><small><?php _e( "The complete URL to your logo (.eps or .ai files prefered). Recommanded size : 800x120 px - 72 dpi", "joliprint" ); ?></small> 
				<br/><small><?php _e( "<strong>Be sure that this URL will be visible on the internet.</strong>", "joliprint" ); ?></small> 
		</td> 
		</tr>
		</table>
	<?php
}
function joliprint_options_frm_tech(){
	global $btnpath, $currentopt, $baseLocation;
?>
	<div class="joliprint_options_item" id="joliprint_options_<?php echo $currentopt; ?>">

		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
			<tr>
				<th scope="row"> 
					<?php _e("Cache reset", "joliprint");?>
				</th> 
				<td>
				<a href="#" onclick='joliprint_cache_reset()'><?php _e("Click here to reset the cache now", "joliprint");?></a>
				<span id="joliprint_cache_status" style='font-size:80%;margin-left:10px'></span>
				<br/><small><?php _e("Joliprint is caching PDF files for a few hours.", "joliprint");?></small>
				</td>
			</tr>
		</table>
		
	</div>

<?php
}

function joliprint_options_frm_ga(){
	global $btnpath, $currentopt, $baseLocation;
	/*
	Removed in 1.2.0
	$joliprint_google_analytics_id = get_option( "joliprint_google_analytics_id" );
	if ($joliprint_google_analytics_id == null ) $joliprint_google_analytics_id = "";
	*/
	$joliprint_google_analytics_medium_name = get_option( "joliprint_google_analytics_medium_name" );
	if ($joliprint_google_analytics_medium_name == null ) $joliprint_google_analytics_medium_name = "";

	$joliprint_google_analytics_campaign_name = get_option( "joliprint_google_analytics_campaign_name" );
	if ($joliprint_google_analytics_campaign_name == null ) $joliprint_google_analytics_campaign_name = "";
	
	$joliprint_ga_tracking = get_option("joliprint_ga_tracking");
	if ($joliprint_ga_tracking == null || $joliprint_ga_tracking == '') $joliprint_ga_tracking = '1';
	
	?>
			
			<h4><?php _e( "Track Joliprint requests into your Google Analytics account", "joliprint" ); ?></h4> 
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
			<tr valign="baseline">
				<th scope="row"> 
					<input type="radio" name="joliprint_ga_tracking" id="joliprint_ga_tracking_yes" value="1" <?php if($joliprint_ga_tracking == '1') {echo "checked='checked'";} ?>/>&nbsp;<label for="joliprint_ga_tracking_yes"><?php _e("Joliprint tracking enabled", "joliprint"); ?></label></br>
					<input type="radio" name="joliprint_ga_tracking" id="joliprint_ga_tracking_no" value="0" <?php if($joliprint_ga_tracking == '0') {echo "checked='checked'";} ?>/>&nbsp;<label for="joliprint_ga_tracking_no"><?php _e("Joliprint tracking disabled", "joliprint"); ?></label>
				</td> 
				</tr>
			</table>
			<small> <?php _e( "<strong>IMPORTANT :</strong>Joliprint will use your current Google Analytics configuration. In order to have Joliprint tracking in Google Analytics you will need to have a Google Analytics plugin enabled on your Blog and a valid Google Analytics account.<br/>You will find your Joliprint printed article in your Events tracking in your GA dashboard under the Joliprint category (Content > Event Tracking) .", "joliprint" ); ?></small> 
			<hr size='1'/>
			
			<h4><?php _e('Google Analytics Campaigns URL Tracking','joliprint'); ?></h4>
			<small><?php _e( "If you are using Google Analytics and want to identify links pointing back to your website you can set the campaign name and campaign medium below.", "joliprint" ); ?></small> 
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
			<tr valign="baseline">
				<th scope="row"> 
					<?php _e("Medium name", "joliprint"); ?>
					
				</th> 
				<td> 
					<input type="text" name="joliprint_google_analytics_medium_name" value="<?php echo $joliprint_google_analytics_medium_name;?>" size="60" /> 
					<small><?php _e("(ie. joliprint_printed_pdf)", "joliprint");?></small>
				</td> 
			</tr>
			<tr valign="baseline">
				<th scope="row"> 
					<?php _e("Campaign name", "joliprint"); ?>
				</th> 
				<td> 
					<input type="text" name="joliprint_google_analytics_campaign_name" value="<?php echo $joliprint_google_analytics_campaign_name;?>" size="60" /> 
					<small><?php _e("(ie. PDF)", "joliprint");?></small>
				</td>
			</tr>
			</table>
	
	<?php
}


function joliprint_resetcache_warning(){
	return "<div id='joliprint-resetcache-warning' class='updated fade'><p><strong>".__('If you have made changes in this page it would be a clever idea to reset your PDF cache files on Joliprint.<br/>','joliprint')."</strong> ".sprintf(__('You can reset your cache files on this <a href="%1$s">page</a>.','joliprint'), "admin.php?page=joliprint/joliprint_admin_options.php&opt=tech")."</p></div>";
}
function joliprint_resetcache_done(){
	return "<div id='joliprint-resetcache-warning' class='updated fade'><p><strong>".__('The cache of your PDF files has been deleted on the Joliprint server.<br/>','joliprint')."</strong></p></div>";
}
function joliprint_cache_update($post_id){
	global $wp_version, $joliprint_server_url;
	
	if ( floatval($wp_version) >= 2.7){
		if ($post_id == null){
			$cacheurl = site_url();
		}else{
			$cacheurl = get_permalink();
		}
		$url = $joliprint_server_url . "/api/rest/cachereset/xml?url=" . urlencode($cacheurl);
		$request = new WP_Http;
		$result = $request->request( $url, array( 'timeout' => 5, 'redirection' => 5, 'method' => 'GET', 'headers' => array( 'referer'=> $cacheurl ) ) );
		if ( is_wp_error( $result ) ) return false;
		if ( $result['response']['message'] != 'OK') return false;

		$response = $result['body'];
		$xml = str_replace(array ("\r\n", "\r"), "\n", $response);
		preg_match_all('|<status_code>(.*?)</status_code>|is', $xml, $status_code);
		if ($status_code[1][0] != '200') return false;
		return true;
	}else{
		return false;// nothing ...
	}
}

function joliprint_supportus_metabox($data){
	global $baseLocation,$joliprint_credits;

?>
	<script type='text/javascript'>
		function toggleCredit(){
			var val = jQuery( "#frm_joliprint_credits input#joliprint_credits" ).val();
			if (val == "true"){
				val = "false";
			}else{
				val = "true";
			}
			jQuery( "#frm_joliprint_credits input#joliprint_credits" ).val(val);
			jQuery( "#frm_joliprint_credits").submit();
		}
	</script>
	<style>	
		ul.joliprint_supportbox img{
			margin-right:3px;
			border:none;
		}
		ul.joliprint_supportbox a{
			text-decoration:none;
		}
	</style>
	<ul class='joliprint_supportbox'>
		<li><img src="<?php echo plugins_url('/joliprint/img/rate.png');?>"><a href="http://wordpress.org/extend/plugins/joliprint/" target="_blank"><?php _e("Give this plugin a perfect Rating !","joliprint");?></a></li>
		<li><a title="Twitter" href="http://twitter.com/home?status=Just installed @Joliprint plugin on my blog. Try it ! http://bit.ly/Joliprint-WP-plugin" target="_blank"><img alt="Twitter" src="<?php echo plugins_url('/joliprint/img/twitter.png');?>" /><?php _e("Tell your followers","joliprint");?></a></li>
		<li><img src="<?php echo plugins_url('/joliprint/img/fb.png');?>"><a href="http://www.facebook.com/joliprint" target="_blank"><?php _e("Like our Facebook page","joliprint");?></a></li>
		<li><img src="<?php echo plugins_url('/joliprint/img/twit.png');?>" alt="" /><?php _e("Follow <a href='http://twitter.com/joliprint' target='_blank'>Joliprint's updates on Twitter</a> &amp; Visit <a href='http://www.joliprint.com/blog/' target='_blank'>our blog</a>","joliprint");?></li>
		<li><img src="<?php echo plugins_url('/joliprint/img/help.png');?>"><a target="_blank" href="mailto:hello@joliprint.com?subject=<?php echo rawurlencode("[Wordpress Plugin " . JOLIPRINT_WPPLUGIN_VERSION . "] [" . site_url() . "]"); ?>"><?php _e("Support and Help","joliprint");?></a></li>
	</ul>
	<hr size='1' />
	<ul class='joliprint_supportbox'>
		<li>
				<a href="#" onclick="javascript:toggleCredit();">
				<?php
					if ( $joliprint_credits == 'true' ){
						_e("Hide Joliprint credit in waiting page.", "joliprint");
					}else{
						_e("Show Joliprint credit in waiting page.","joliprint");
					}
				?>
				</a>
		</li>
	</ul>
<?php
}

?>

