<?php

class Memcached_Command extends WP_CLI_Command {

	/**
	 * Tests if an environment can use the PECL Memcached Object Cache.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mem preflight
	 */
	function preflight( $args, $assoc_args ) {
		$is_memcached_available = $this->_test_for_memcached_extension();

		$this->_test_for_memcached_daemon_availability();
	}

	private function _test_for_memcached_extension() {
		return ( class_exists( 'Memcached' ) && extension_loaded( 'memcached' ) );
	}

	private function _test_for_memcached_daemon_availability() {
		// First attempt to see if connection can be made via the PHP interface
		if ( $this->_test_for_memcached_extension() ) {
			$m = new Memcached();
			if ( true === $m->addServer( '127.0.0.1', 11211 )  ) {
				return true;
			}
		}
	}
}

WP_CLI::add_command( 'mem', 'Memcached_Command' );