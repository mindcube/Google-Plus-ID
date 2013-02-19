<?php
/*
	Plugin Name: Google+ ID manager
	Plugin URI: http://www.scottlandes.com
	Description: A plugin that adds the simple ability to set and manage Google+ ID for site.
	Author: Scott Landes
	Version: 0.1
	Author URI: http://www.scottlandes.com
	License: GPL2
 */


add_action( 'admin_menu', 'gidm_plugin_menu' );

function gidm_plugin_menu() {
	add_submenu_page( 'options-general.php', 'Manage Google+ ID', 'Google+ ID', 'manage_options', 'google-plus-manage', 'gidm_management_page' );
}

function gidm_management_page() { 
	global $wpdb;

	/*	1. Check to see if domain mapping is enabled, if so
		2. Get current blog id
		3. Query domain mapping table w/ blog ID and retrieve primary domain
		4. Convert to display format
		else
		2. Get domain using built in wp functions
	*/

    $table_name = $wpdb->base_prefix.'domain_mapping';

	if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
		$blog_id = get_current_blog_id();
		$blog_urls = $wpdb->get_results(
			"
			SELECT domain
			FROM $table_name
			WHERE blog_id = $blog_id AND active = 1
			"
		);
		$domain = $blog_urls[0]->domain;
	} else {
		$domain = get_option('siteurl'); //or home
		$domain = str_replace('http://', '', $domain);
	}

	if ( isset($_POST['form_submit']) ) {
		update_option ( 'google_plus_id', $_POST['google_plus_id'] );

		?><div class="updated fade"><p>Settings Saved</p></div><?php
	}

	?>

	<div class="wrap">

		<h2>Set the Google+ ID for Authorship</h2>
		<p>For example, if this is the Google+ profile URL: https://plus.google.com/105716196679190237759/posts</p>

		<p>Please enter just the profile ID:
		105716196679190237759</p>

		<p><a href="http://www.google.com/webmasters/tools/richsnippets?url=<?php echo $domain;?>&html=" target="_blank">Click here to check</a></p>

		 <form id="form_data" name="form" method="post">  
		 	<label style="margin-right:5px">Google+ ID: </label>
		 	<input type="text" name="google_plus_id" value="<?php echo get_option( 'google_plus_id', '' );?>" />
		 	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'to') ?>" style="display:block;margin-top:10px">
		 	<input type="hidden" name="form_submit" value="true" />
		 	
		 </form>

	</div>

<?php }

if ( get_option( 'google_plus_id' )) {

	add_action( 'wp_head', 'gidm_head' );

	function gidm_head() {
		echo '<a rel="author" href="https://plus.google.com/'.get_option( 'google_plus_id' ).'/posts"/>';
	}

}

?>