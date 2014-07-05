<?php

class MemcachedUnitTests extends WP_UnitTestCase {
	public $plugin_slug = 'memcached-unit-tests';

	public $object_cache;

	public $servers;

	public $test_cache;

	public function setUp() {
		global $memcached_servers;

		if ( ! is_array( $memcached_servers ) ) {
			$memcached_servers = array(
				'default' => array( '127.0.0.1', 11211 ),
			);
		}

		// Instantiate the core cache tests and use that setup routine
		$this->test_cache = new Tests_Cache();
		$this->test_cache->setUp();

		$this->object_cache = $this->test_cache->cache;
		$this->servers = $this->object_cache->servers;
	}

	public function tearDown() {
		$this->test_cache->tearDown();
	}
}