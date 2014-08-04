<?php

/**
 * Define the "mem" WP CLI command.
 *
 * @since 1.0.0.
 */
class Memcached_Command extends WP_CLI_Command {

	/**
	 * Tests if an environment can use the PECL Memcached Object Cache.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	function check() {
		/**
		 * Colorizing the results leads to the table display incorrectly rendering the right hand border. This is due to
		 * a bug in WP CLI (https://github.com/wp-cli/php-cli-tools/issues/59). Colors are pretty important to communicate
		 * a failed checked, so this is implemented with a broken table display.
		 */
		$success = \cli\Colors::colorize( "%G✓%n", true );
		$failure = \cli\Colors::colorize( "%R✖%n", true );

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
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	function install() {
		$object_cache = 'object-cache.php';

		// Set the target and link paths
		$target = trailingslashit( zdt_pecl_memcached_object_cache()->root_dir ) . $object_cache;
		$link   = trailingslashit( WP_CONTENT_DIR ) . $object_cache;

		// Create the symlink
		$cmd = 'ln -nfs ' . $target . ' ' . $link;
		exec( $cmd, $output, $return );

		// Test that the object cache is now in place
		if ( readlink( $link ) === $target ) {
			WP_CLI::success( 'The PECL Memcached Object Cache was successfully installed.' );
		} else {
			WP_CLI::error( 'The PECL Memcached Object Cache could not be installed.' );
		}
	}

	/**
	 * Get the memcached stats.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	function stats() {
		$stats = pmem_get_stats();

		foreach ( $stats as $server => $data ) {
			WP_CLI::line( "\n" . 'Stats for ' . $server );

			$row_data = array();

			// Reorganize data into array appropriate for table display
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

	/**
	 * Get an individual memcached stat.
	 *
	 * ## OPTIONS
	 *
	 * <statistic>
	 * : The statistic to get.
	 *
	 * <server>
	 * : The server to get the statistic from (optional).
	 *
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $args    Args passed to command. 1: statistic to return, 2: server to use (optional)
	 * @return void
	 */
	function stat( $args ) {
		if ( 1 === count( $args ) ) {
			list( $name ) = $args;
			$server = '';
		} elseif ( 2 === count( $args ) ) {
			list( $name, $server ) = $args;
		} else {
			return;
		}

		$stats = pmem_get_stat( $name, $server );

		// Print results for multiple servers
		if ( ! empty( $name ) && empty( $server ) && ! empty( $stats ) ) {
			// Display results
			$table = new \cli\Table();
			$table->setHeaders( array( 'Server', $name ) );
			$table->setRows( $stats );
			$table->display();

		// Print results for single server
		} elseif ( ! empty( $name ) && ! empty( $server ) && ! empty( $stats ) && isset( $stats[0][1] ) ) {
			WP_CLI::line( $stats[0][1] );
		}
	}
}

WP_CLI::add_command( 'mem', 'Memcached_Command' );