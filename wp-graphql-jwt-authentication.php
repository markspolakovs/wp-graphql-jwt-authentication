<?php
/**
 * Plugin Name:     WPGraphQL JWT Authentication
 * Plugin URI:      https://www.wpgraphql.com
 * Description:     JWT Authentication for WPGraphQL
 * Author:          WPGraphQL, Jason Bahl
 * Author URI:      https://www.wpgraphql.com
 * Text Domain:     wp-graphql-jwt-authentication-jwt-authentication
 * Domain Path:     /languages
 * Version:         0.2.1
 *
 * @package         WPGraphQL_JWT_Authentication
 */

namespace WPGraphQL\JWT_Authentication;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( '\WPGraphQL\JWT_Authentication' ) ) :

	final class JWT_Authentication {

		/**
		 * Stores the instance of the JWT_Authentication class
		 *
		 * @var JWT_Authentication The one true JWT_Authentication
		 * @since  0.0.1
		 * @access private
		 */
		private static $instance;

		/**
		 * The instance of the JWT_Authentication object
		 *
		 * @return object|JWT_Authentication - The one true JWT_Authentication
		 * @since  0.0.1
		 * @access public
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof JWT_Authentication ) ) {
				self::$instance = new JWT_Authentication;
				self::$instance->setup_constants();
				self::$instance->includes();
			}

			self::$instance->init();

			/**
			 * Fire off init action
			 *
			 * @param JWT_Authentication $instance The instance of the Init_JWT_Authentication class
			 */
			do_action( 'graphql_jwt_authentication_init', self::$instance );

			/**
			 * Return the Init_JWT_Authentication Instance
			 */
			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since  0.0.1
		 * @access public
		 * @return void
		 */
		public function __clone() {

			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'The Init_JWT_Authentication class should not be cloned.', 'wp-graphql-jwt-authentication' ), '0.0.1' );

		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since  0.0.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {

			// De-serializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL class is not allowed', 'wp-graphql-jwt-authentication' ), '0.0.1' );

		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'WPGRAPHQL_JWT_AUTHENTICATION_VERSION' ) ) {
				define( 'WPGRAPHQL_JWT_AUTHENTICATION_VERSION', '0.2.1' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_DIR' ) ) {
				define( 'WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_URL' ) ) {
				define( 'WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_FILE' ) ) {
				define( 'WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Include required files.
		 * Uses composer's autoload
		 *
		 * @access private
		 * @since  0.0.1
		 * @return void
		 */
		private function includes() {

			// Autoload Required Classes
			require_once( WPGRAPHQL_JWT_AUTHENTICATION_PLUGIN_DIR . 'vendor/autoload.php' );

		}

		/**
		 * Initialize the plugin
		 */
		private static function init() {

			/**
			 * Filter the rootMutation fields
			 */
			add_filter( 'graphql_rootMutation_fields', [
				'\WPGraphQL\JWT_Authentication\Login',
				'root_mutation_fields'
			], 10, 1 );

			/**
			 * Filter how WordPress determines the current user
			 */
			add_filter( 'determine_current_user', [
				'\WPGraphQL\JWT_Authentication\Auth',
				'filter_determine_current_user'
			], 10 );
		}

	}

endif;

function init() {
	return JWT_Authentication::instance();
}

add_action( 'graphql_init', '\WPGraphQL\JWT_Authentication\init' );