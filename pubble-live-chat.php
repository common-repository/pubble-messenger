<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://www.pubble.io
 * @since             1.1.0
 * @package           Pubble_Live_Chat
 *
 * @wordpress-plugin
 * Plugin Name:       Pubble Messenger Live Chat
 * Plugin URI:        https://www.pubble.io/messenger
 * Description:       With Pubble Messenger Live Chat, we focused on building a live chat embeddable widget that mirrors the messaging experience offered by the main smartphone messaging apps. Features like presence, read receipts, push notifications are leveraged to offer a familiar messaging experience to your customers.
 * Version:           1.1.0
 * Author:            Pubble
 * Author URI:        https://www.pubble.io
 * Text Domain:       pubble-live-chat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );

require plugin_dir_path( __FILE__ ) . 'includes/class-pubble-live-chat.php';

function run_pubble_live_chat() {


	$plugin = new Pubble_Live_Chat();
	$plugin->run();

}
run_pubble_live_chat(); 
