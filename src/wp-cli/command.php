<?php

class Memcached_Command extends WP_CLI_Command {

	/**
	 * Tests if an environment can use the PECL Memcached Object Cache.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mem check
	 */
	function check( $args, $assoc_args ) {
		$success = '✓';
		$failure = '✖';

		// Organize the results
		$data = array(
			array(
				'Memcached PECL extension',
				( $this->_test_for_memcached_extension() ) ? $success : $failure,
			),
			array(
				'Connect to Memcached via PHP',
				( $this->_test_for_connect_to_memcached_from_php() ) ? $success : $failure,
			),
			array(
				'Memcached available via CLI',
				( $this->_test_for_memcached_daemon_via_command_line() ) ? $success : $failure,
			),
			array(
				'Memcached available via `wp-config.php` config',
				( $this->_test_for_connecting_to_memcached_via_wp_config_values() ) ? $success : $failure,
			),
			array(
				'Memcached stores content',
				( $this->_test_for_storing_content() ) ? $success : $failure,
			),
			array(
				'`object-cache.php` exists',
				( $this->_test_for_existence_of_object_cache() ) ? $success : $failure,
			),
			array(
				'Object cache stores content',
				( $this->_test_for_wp_object_cache_storing_content() ) ? $success : $failure,
			),
		);

		// Display results
		$table = new \cli\Table();
		$table->setHeaders( array( 'Check', 'Result' ) );
		$table->setRows( $data );
		$table->display();
	}

	/**
	 * Install the object cache by symlinking object-cache.php into place.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mem install
	 */
	function install( $args, $assoc_args ) {
		$object_cache = 'object-cache.php';

		// Set the target and link paths
		$target = trailingslashit( zdt_pecl_memcached_object_cache()->root_dir ) . $object_cache;
		$link   = trailingslashit( WP_CONTENT_DIR ) . $object_cache;

		// Create the symlink
		$cmd = 'ln -nfs ' . $target . ' ' . $link;
		exec( $cmd, $output, $return );

		if ( readlink( $link ) === $target ) {
			WP_CLI::success( 'The PECL Memcached Object Cache was successfully installed.' );
		} else {
			WP_CLI::error( 'The PECL Memcached Object Cache could not be installed.' );
		}
	}

	private function _test_for_memcached_extension() {
		return ( class_exists( 'Memcached' ) && extension_loaded( 'memcached' ) );
	}

	private function _test_for_connect_to_memcached_from_php() {
		if ( $this->_test_for_memcached_extension() ) {
			$m = new Memcached();
			if ( true === $m->addServer( '127.0.0.1', 11211 )  ) {
				return true;
			}
		}

		return false;
	}

	private function _test_for_memcached_daemon_via_command_line() {
		$cmd = 'memcached -h';
		exec( $cmd, $output, $return );

		if ( 0 === $return ) {
			if ( isset( $output[0] ) && 0 === strpos( $output[0], 'memcached' ) ) {
				return true;
			}
		}

		return false;
	}

	private function _test_for_connecting_to_memcached_via_wp_config_values() {
		global $memcached_servers;
		$m = new Memcached();
		return ( ! empty( $memcached_servers ) && $m->addServers( $memcached_servers ) );
	}

	private function _test_for_storing_content() {
		if ( true === $this->_test_for_connect_to_memcached_from_php() ) {
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

	private function _test_for_existence_of_object_cache() {
		return file_exists( trailingslashit( WP_CONTENT_DIR ) . 'object-cache.php' );
	}

	private function _test_for_wp_object_cache_storing_content() {
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
}

WP_CLI::add_command( 'mem', 'Memcached_Command' );