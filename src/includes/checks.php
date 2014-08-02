<?php

/**
 * Checks to see if the PECL Memcached extension is installed.
 *
 * @since  1.0.0.
 *
 * @return bool    True if PECL Memcached is available; False if it is not.
 */
function pmemd_test_for_memcached_extension() {
	return ( class_exists( 'Memcached' ) && extension_loaded( 'memcached' ) );
}

/**
 * Checks to see if a connection to Memcached can be made from PHP.
 *
 * @since  1.0.0.
 *
 * @return bool    True if the connection is possible; False if it is not.
 */
function pmemd_test_for_connect_to_memcached_from_php() {
	if ( pmemd_test_for_memcached_extension() ) {
		$m = new Memcached();
		if ( true === $m->addServer( '127.0.0.1', 11211 )  ) {
			return true;
		}
	}

	return false;
}

/**
 * Checks to see if Memcached server software is installed.
 *
 * @since  1.0.0.
 *
 * @return bool    True if Memcached server software is installed; False if it is not.
 */
function pmemd_test_for_memcached_daemon_via_command_line() {
	$cmd = 'memcached -h';
	exec( $cmd, $output, $return );

	if ( 0 === $return ) {
		if ( isset( $output[0] ) && 0 === strpos( $output[0], 'memcached' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Checks to see if a connection can be made to Memcached using the wp-config.php details.
 *
 * @since  1.0.0.
 *
 * @return bool    True if the connection is possible; False if it is not.
 */
function pmemd_test_for_connecting_to_memcached_via_wp_config_values() {
	global $memcached_servers;
	$m = new Memcached();
	return ( ! empty( $memcached_servers ) && $m->addServers( $memcached_servers ) );
}

/**
 * Checks to see if Memcached can store data properly.
 *
 * @since  1.0.0.
 *
 * @return bool    True if the store procedure works; False if it does not.
 */
function pmemd_test_for_storing_content() {
	if ( true === pmemd_test_for_connect_to_memcached_from_php() ) {
		$m = new Memcached();
		$m->addServer( '127.0.0.1', 11211 );
		$m->add( 'memtest', 9 );

		if ( 9 === $m->get( 'memtest' ) ) {
			$m->delete( 'memtest' );
			return true;
		}
	}

	return false;
}

/**
 * Checks to see if the object-cache.php file is properly installed.
 *
 * @since  1.0.0.
 *
 * @return bool    True if object-cache.php is installed; False if it is not.
 */
function pmemd_test_for_existence_of_object_cache() {
	$file_path = trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php';
	return file_exists( $file_path ) || is_link( $file_path );
}

/**
 * Checks to see if the WP_Object_Cache methods can store and retrieve cached objects.
 *
 * @since  1.0.0.
 *
 * @return bool    True if the store procedure works; False if it does not.
 */
function pmemd_test_for_wp_object_cache_storing_content() {
	global $wp_object_cache;

	if ( ! empty( $wp_object_cache ) && isset( $wp_object_cache->m ) && is_a( $wp_object_cache->m, 'Memcached' ) && function_exists( 'wp_cache_add' ) && function_exists( 'wp_cache_get' ) && function_exists( 'wp_cache_delete' ) ) {
		if ( true === wp_cache_add( 'memtest', 9 ) ) {
			if ( 9 === wp_cache_get( 'memtest' ) ) {
				wp_cache_delete( 'memtest' );
				return true;
			}
		}
	}

	return false;
}