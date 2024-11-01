<?php
/*
Plugin Name: Visitor Force Login Page
Plugin URI: https://wordpress.org/plugins/wp-visitor-force-login-page/
Description: Easily hide your WordPress site from public viewing by requiring visitors to redirect specific or login page in first.
Version: 1.0.2
Requires at least: 5.0
WC tested up to: 6.2.2
Author: WP Lovers
Author URI: https://wordpress.org/five-for-the-future/pledge/wordpress-lovers-team/
Text Domain: wp-visitor-force-login-page
Developer: sumitsingh
Developer URI: https://profiles.wordpress.org/sumitsingh
Domain Path: /languages
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * Restrict REST API for authorized users only
 *
 * @since 5.1.0
 * @param WP_Error|null|bool $result WP_Error if authentication error, null if authentication
 *                              method wasn't used, true if authentication succeeded.
 */
function visitor_force_page_rest_access( $result ) {
	if ( null === $result && ! is_user_logged_in() ) {
		return new WP_Error( 'rest_unauthorized', __( "Only authenticated users can access the REST API.", 'wp-visitor-force-login-page' ), array( 'status' => rest_authorization_required_code() ) );
	}
	return $result;
}
add_filter( 'rest_authentication_errors', 'visitor_force_login_page_rest_access', 99 );

/*
 * Localization
 */
function visitor_force_page_load_textdomain() {
	load_plugin_textdomain( 'wp-visitor-force-login-page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'visitor_force_page_load_textdomain' );

if (!function_exists('visitor_force_get_page_list')) {
    function visitor_force_get_page_list()
    {
       $visitor_force_login_page = get_option('visitor_force_login_page');
       $response = '';
           $wpf_temp_arg = array(
                'post_type' => 'page'
            );
            $posts = get_posts($wpf_temp_arg);
            $response .= "<select name='visitor_force_login_page'><option value=''>Select page</option>";
            $wpf_count_post = count($posts);
            if ($wpf_count_post) {
                foreach ($posts as $post) {
                	if($visitor_force_login_page == $post->ID){
                		$response .= "<option selected value='".$post->ID."'>".esc_html( __($post->post_title,'wp-visitor-force-login-page'))."</option>";
                	}else{
                		$response .= "<option value='".$post->ID."'>".esc_html( __($post->post_title,'wp-visitor-force-login-page'))."</option>";
                	}
                	
                }
                 $response .= "</select>";
            }
          
        return $response;
    }
}

function visitor_force_page_options_page() {
  add_options_page('Visitor force page', 'Visitor force page', 'manage_options', 'visitor_force_page', 'visitor_force_form_page','','');
}
add_action('admin_menu', 'visitor_force_page_options_page');


add_action( 'admin_post_save_visitor_force_page','visitor_force_page_options' );
function visitor_force_page_options(){
	$login_page = sanitize_text_field($_POST['visitor_force_login_page']);
	if ( ! check_ajax_referer( 'wp-visitor-force-login-page', 'wpf_nonce' ) ) {
        echo 'Invalid security token sent.';
        wp_die();
    }else{
    	update_option( 'visitor_force_login_page', $login_page ,'no');
    }
	wp_redirect( add_query_arg( 'page', 'visitor_force_page&update_page=1', admin_url( 'options-general.php' ) ) );
}

function visitor_force_form_page()
{
?>
  <div>
  <h2>Visitor force page</h2>
 <form method="post" action="admin-post.php">
 <?php if(isset($_GET['update_page']) && $_GET['update_page'] == '1') { echo esc_html( __('saved successfully','wp-visitor-force-login-page')); } ?>
 <input type="hidden" name="action" value="save_visitor_force_page"/>
  <table>
  <tr valign="top">
  <th scope="row"><label for="visitor_force_login_page"><?php echo esc_html( __('Select page','wp-visitor-force-login-page')); ?></label></th>
  <td><?php echo visitor_force_get_page_list(); ?></td>
  </tr>
  </table>
  <?php wp_nonce_field( 'wp-visitor-force-login-page' ); ?>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
}

function visitor_force_page() {
	$get_page_id = get_option('visitor_force_login_page');
	if($get_page_id ==""){
		$redirect_url = '/wp-admin/';
	}else{
		$selected_page_name = get_post_field( 'post_name', $get_page_id );
		$redirect_url = "/".$selected_page_name."/";
	}
	// Exceptions for AJAX, Cron, or WP-CLI requests
	if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	// Redirect unauthorized visitors
	if ( ! is_user_logged_in() ) {
		// Get visited URL
		$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
		$url .= '://' . $_SERVER['HTTP_HOST'];
		// port is prepopulated here sometimes
		if ( strpos( $_SERVER['HTTP_HOST'], ':' ) === FALSE ) {
			$url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
		}
		$url .= $_SERVER['REQUEST_URI'];

		/**
		 * Bypass filters.
		 *
		 * @since 3.0.0 The `$whitelist` filter was added.
		 * @since 4.0.0 The `$bypass` filter was added.
		 * @since 5.2.0 The `$url` parameter was added.
		 */

		if ( preg_replace( '/\?.*/', '', $url ) !== preg_replace( '/\?.*/', '', home_url( $redirect_url ) ) && ! $bypass && ! in_array( $url, $whitelist ) ) {
			// Set the headers to prevent caching
			nocache_headers();
			// Redirect
			wp_safe_redirect( home_url( $redirect_url ), 302 ); exit;
		}
	}
	elseif ( function_exists('is_multisite') && is_multisite() ) {
		// Only allow Multisite users access to their assigned sites
		if ( ! is_user_member_of_blog() && ! current_user_can('setup_network') ) {
			wp_die( __( "You're not authorized to access this site.", 'wp-visitor-force-login-page' ), get_option('blogname') . ' &rsaquo; ' . __( "Error", 'wp-visitor-force-login-page' ) );
		}
	}
}
add_action( 'template_redirect', 'visitor_force_page' );