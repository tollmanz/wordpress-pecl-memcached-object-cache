<?php

class MemcachedConfigTest extends MemcachedUnitTests {
	/**
	 * Since the purpose of these tests is to test the configuration and setup of the object cache, we need to shut down
	 * the normal setUp process. We need to make sure that the object has not been created yet because the global scope
	 * will pollute the expectations of some of the tests.
	 */
	public function setUp() {}

	/**
	 * Nullify the normal tearDown behavior. Since we are removing the setUp behavior, we need to nullifuy the
	 * corresponding tearDown behavior and add our own new behavior.
	 */
	public function tearDown() {
		// Avoid pollution of globals
		unset( $GLOBALS['memcached_servers'] );
	}

	public function test_environment_variables_set_configuration_array_if_array_is_not_set() {
		putenv( 'MEMCACHED_SERVERS=127.0.0.1:11222' );
		$cache = new WP_Object_Cache();

		$expected = array(
			array(
				'127.0.0.1',
				11222,
			),
		);

		$this->assertEquals( $expected, $cache->servers );
	}

	public function test_environment_variables_set_configuration_array_if_array_is_empty() {
		global $memcached_servers;
		$memcached_servers = array();

		putenv( 'MEMCACHED_SERVERS=127.0.0.1:11222' );
		$cache = new WP_Object_Cache();

		$expected = array(
			array(
				'127.0.0.1',
				11222,
			),
		);

		$this->assertEquals( $expected, $cache->servers );
	}

	public function test_environment_variables_set_configuration_array_for_multiple_servers() {
		putenv( 'MEMCACHED_SERVERS=127.0.0.1:11222;127.0.0.2:11222' );
		$cache = new WP_Object_Cache();

		$expected = array(
			array(
				'127.0.0.1',
				11222,
			),
			array(
				'127.0.0.2',
				11222,
			),
		);

		$this->assertEquals( $expected, $cache->servers );
	}
}