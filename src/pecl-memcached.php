<?php
/**
 * Plugin Name:    PECL Memcached Object Cache
 * Plugin URI:     https://github.com/tollmanz/wordpress-memcached-backend
 * Description:    Object cache drop-in using the Memcached PECL extension to interface with Memcached.
 * Version:        1.0.0.
 * Author:         Zack Tollman
 * Author URI:     http://tollmanz.com
 * License:        MIT
 *
 *                 Copyright (c) 2013 Zack Tollman

 *                 Permission is hereby granted, free of charge, to any person obtaining a copy
 *                 of this software and associated documentation files (the "Software"), to deal
 *                 in the Software without restriction, including without limitation the rights
 *                 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *                 copies of the Software, and to permit persons to whom the Software is
 *                 furnished to do so, subject to the following conditions:
 *
 *                 The above copyright notice and this permission notice shall be included in all
 *                 copies or substantial portions of the Software.
 *
 *                 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *                 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *                 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *                 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *                 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *                 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *                 SOFTWARE.
 */

if ( ! class_exists( 'ZDT_PECL_Memcached_Object_Cache' ) ) :
/**
 * Bootstrap functionality for the plugin.
 *
 * @since 1.0.0.
 */
class ZDT_PECL_Memcached_Object_Cache {
	/**
	 * Current plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @var   string    The semantically versioned plugin version number.
	 */
	var $version = '1.0.0';

	/**
	 * File path to the plugin dir (e.g., /var/www/mysite/wp-content/plugins/pecl-memcached-object-cache).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the root of this plugin.
	 */
	var $root_dir = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/pecl-memcached-object-cache/pecl-memcached.php).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/pecl-memcached-object-cache).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of ZDT_PECL_Memcached_Object_Cache.
	 *
	 * @since 1.0.0.
	 *
	 * @var   ZDT_PECL_Memcached_Object_Cache
	 */
	private static $instance;

	/**
	 * Instantiate or return the one ZDT_PECL_Memcached_Object_Cache instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return ZDT_PECL_Memcached_Object_Cache
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create a new section.
	 *
	 * @since  1.0.0.
	 *
	 * @return ZDT_PECL_Memcached_Object_Cache
	 */
	public function __construct() {
		// Set the main paths for the plugin
		$this->root_dir  = dirname( __FILE__ );
		$this->file_path = $this->root_dir . '/' . basename( __FILE__ );
		$this->url_base  = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Include dependencies
		include $this->root_dir . '/includes/checks.php';
		include $this->root_dir . '/includes/stats.php';

		if ( defined('WP_CLI') && WP_CLI ) {
			include $this->root_dir . '/wp-cli/command.php';
		}
	}
}
endif;

/**
 * Get the one instance of the ZDT_PECL_Memcached_Object_Cache.
 *
 * @since  1.0.0.
 *
 * @return ZDT_PECL_Memcached_Object_Cache
 */
function zdt_pecl_memcached_object_cache() {
	return ZDT_PECL_Memcached_Object_Cache::instance();
}

zdt_pecl_memcached_object_cache();
