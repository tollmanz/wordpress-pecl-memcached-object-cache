<?php

/**
 * Get the PECL Memcached stats.
 *
 * @since  1.0.0.
 *
 * @return array    The array of Memcached stats.
 */
function pmem_get_stats() {
	return wp_cache_get_stats();
}

/**
 * Get an individual stat.
 *
 * @since  1.0.0.
 *
 * @param  string          $name      The name of an individual stat.
 * @param  string          $server    The server to use for the lookup.
 * @return array|string               The stat value.
 */
function pmem_get_stat( $name, $server = '' ) {
	$stats = pmem_get_stats();

	if ( ! empty( $name ) ) {
		$return = array();

		// Get stat for all servers
		if ( empty( $server ) ) {
			foreach ( $stats as $server => $data ) {
				if ( isset( $data[ $name ] ) ) {
					$return[] = array(
						$server,
						$data[ $name ]
					);
				}
			}
		} else {
			if ( isset( $stats[ $server ][ $name ] ) ) {
				$return[] = array(
					$server,
					$stats[ $server ][ $name ]
				);
			}
		}

		if ( ! empty( $return ) ) {
			return $return;
		}
	}

	return '';
}