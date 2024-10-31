<?php

/**
 * The core plugin class.
 *
 * @link       https://www.pubble.io
 * @since      1.1.0
 * @package    Pubble_Live_Chat
 * @subpackage Pubble_Live_Chat/includes
 * @author     Ian <ian@pubble.co>
 */
class Pubble_Live_Chat {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Pubble_Live_Chat_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.1.0';
		}
		$this->plugin_name = 'pubble-live-chat';

		$this->load_dependencies();
	

		add_action( 'wp_footer',             array( $this, 'output_install_code'       ) );
		add_action( 'admin_menu',            array( $this, 'app_config_page'       ) );
		add_action( 'network_admin_menu',    array( $this, 'app_config_page'       ) );
		add_action( 'admin_init',            array( $this, 'settings_init'             ) );
		add_action( 'admin_notices',         array( $this, 'prompt'                    ) );
		add_action( 'network_admin_notices', array( $this, 'prompt'                    ) );



	}


		function settings_init() {


		register_setting( 'pubble_livechat', 'settings-pblivechat', array( $this, 'validate' ) );

		if ( isset( $_REQUEST[ '_wpnonce' ] ) and wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'pubble_livechat-options' ) ) {

			$file = is_network_admin() ? 'settings.php' : 'options-general.php';

			if ( isset( $_POST[ 'settings-pblivechat-submit' ] ) and is_network_admin() ) {
				$opts = $this->validate( $_POST[ 'settings-pblivechat' ] );
				$this->update_settings( $opts );
				wp_redirect( add_query_arg( array(
					'page'    => 'pubble_livechat',
					'updated' => true
					), $file ) );
				die();
			}

		}

	}



	function app_config_page() {

			add_options_page(
				'Pubble Settings',
				'Pubble live Chat',
				'manage_options',
				'pubble_livechat',
				array( $this, 'render_setting_page' )
				);

		

	}





    function get_options() {

		return get_option( 'settings-pblivechat' );

	}




	function save_options( $opts ) {


		if ( is_network_admin() ) {
			update_site_option( 'settings-pblivechat', $opts );
		} else {
			update_option( 'settings-pblivechat', $opts );
		}

	}




	function render_setting_page() {

		$opts = $this->get_options();

		$action = 'options.php';

		?>

		<div class="wrap">

		<?php screen_icon( 'options-general' ); ?>
		<h2>Pubble livechat settings</h2>

		<div class="postbox-container">

			<form method="post" action="<?php echo $action; ?>">

				<?php settings_fields( 'pubble_livechat' ); ?>

				<table class="form-table">
					<tbody>

						<tr valign="top">
							<th scope="row">App ID</th>
							<td>
								<input name="settings-pblivechat[app-id]" type="text" value="<?php echo esc_attr( $opts[ 'app-id' ] ); ?>">
							</td>
						</tr>

					</tbody>

				</table>

				<p class="submit">
					<input class="button-primary" name="settings-pblivechat-submit" type="submit" value="Save Settings">
				</p>

			</form>

		</div>

		</div>
		<?php

	}



	function prompt() {


		if ( isset( $_GET[ 'page' ] ) and ( 'pubble_livechat' == $_GET[ 'page' ] ) ) {

			if ( is_network_admin() and isset( $_GET[ 'updated' ] ) ) { ?>
				<div class="updated" id="pubble-livechat-updated"><p><?php _e( 'Settings saved.' ); ?></p></div>
				<?php
			}

		}

		if ( ! current_user_can( 'manage_options' ) )
			return;

		$opts = $this->get_options();

		if ( !is_network_admin() and ( !isset( $opts[ 'app-id' ] ) or !$opts[ 'app-id' ] ) ) {
			echo '<div class="error" id="pubble-livechat-notice"><p><strong>You need to configure Pubble Messenger to add live chat on your website.</strong> ';
			
			if ( isset( $_GET[ 'page' ] ) and 'pubble_livechat' == $_GET[ 'page' ] ) {
				echo 'Please enter the app ID of your Pubble Messenger Live Chat.';
			} else {
				echo 'Please <a href="options-general.php?page=pubble_livechat">configure your Pubble Messenger Live Chat</a>.';
			}
			echo '</div>';
			
		}

	}



	function output_install_code() {

		global $current_user;
		$opts = $this->get_options();
		

		if ( !isset( $opts[ 'app-id' ] ) or !$opts[ 'app-id' ] )
			return;

		$out = '<div class="pubble-app" data-app-id="' . $opts[ 'app-id' ]. '"  data-app-identifier="' . $opts[ 'app-id' ] .'">';
		$out .= '</div><script type="text/javascript" src="https://cdn.pubble.io/javascript/loader.js" defer></script>';

	
		echo $out;

	}




	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pubble-live-chat-loader.php';
		$this->loader = new Pubble_Live_Chat_Loader();

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Pubble_Live_Chat_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
