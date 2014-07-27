<?php
/**
 * Plugin Name:    WordPress Memcached Backend
 * Plugin URI:     https://github.com/tollmanz/wordpress-memcached-backend
 * Description:    Object cache drop using the Memcached PECL extension to interface with Memcached.
 * Version:        1.0.0.
 * Author:         Zack Tollman
 * Author URI:     http://tollmanz.com
 * License:        GPLv2 or later
 * License URI:    http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( defined('WP_CLI') && WP_CLI ) {
	include __DIR__ . '/wp-cli/command.php';
}