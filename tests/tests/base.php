<?php

class MemcachedUnitTests extends WP_UnitTestCase {
	public $plugin_slug = 'memcached-unit-tests';

	public $object_cache;

	public $servers;

	public function setUp() {
		parent::setUp();
		global $memcached_servers;

		if ( ! is_array( $memcached_servers ) ) {
			$memcached_servers = array(
				'default' => array( '127.0.0.1', 11211 ),
			);
		}

		$this->object_cache = new WP_Object_Cache();
		$this->object_cache->flush();
		$this->servers = $this->object_cache->servers;
	}
}