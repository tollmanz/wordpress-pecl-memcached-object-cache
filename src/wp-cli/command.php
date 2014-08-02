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
				( pmemd_test_for_memcached_extension() ) ? $success : $failure,
			),
			array(
				'Connect to Memcached via PHP',
				( pmemd_test_for_connect_to_memcached_from_php() ) ? $success : $failure,
			),
			array(
				'Memcached available via CLI',
				( pmemd_test_for_memcached_daemon_via_command_line() ) ? $success : $failure,
			),
			array(
				'Memcached available via `wp-config.php` config',
				( pmemd_test_for_connecting_to_memcached_via_wp_config_values() ) ? $success : $failure,
			),
			array(
				'Memcached stores content',
				( pmemd_test_for_storing_content() ) ? $success : $failure,
			),
			array(
				'`object-cache.php` exists',
				( pmemd_test_for_existence_of_object_cache() ) ? $success : $failure,
			),
			array(
				'Object cache stores content',
				( pmemd_test_for_wp_object_cache_storing_content() ) ? $success : $failure,
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

	/**
	 * Get the memcached stats.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mem stats
	 */
	function stats( $args, $assoc_args ) {
		$stats = wp_cache_get_stats();

		foreach ( $stats as $server => $data ) {
			WP_CLI::line( "\n" . 'Stats for ' . $server );

			$row_data = array();

			foreach ( $data as $key => $value ) {
				$row_data[] = array( $key, $value );
			}

			// Display results
			$table = new \cli\Table();
			$table->setHeaders( array( 'Statistic', 'Value' ) );
			$table->setRows( $row_data );
			$table->display();
		}
	}
}

WP_CLI::add_command( 'mem', 'Memcached_Command' );