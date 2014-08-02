<?php

class MemcachedUnitTestsChecks extends MemcachedUnitTests {
	public function test_the_test_for_memcached_extension() {
		$this->assertTrue( pmemd_test_for_memcached_extension() );
	}

	public function test_the_test_for_connect_to_memcached_from_php() {
		$this->assertTrue( pmemd_test_for_connect_to_memcached_from_php() );
	}

	public function test_the_test_for_memcached_daemon_via_command_line() {
		$this->assertTrue( pmemd_test_for_memcached_daemon_via_command_line() );
	}

	public function test_the_test_for_connecting_to_memcached_via_wp_config_values() {
		$this->assertTrue( pmemd_test_for_connecting_to_memcached_via_wp_config_values() );
	}

	public function test_the_test_for_storing_content() {
		$this->assertTrue( pmemd_test_for_storing_content() );
	}

	public function test_the_test_for_existence_of_object_cache() {
		$this->assertTrue( pmemd_test_for_existence_of_object_cache() );
	}

	public function test_the_test_for_wp_object_cache_storing_content() {
		$this->assertTrue( pmemd_test_for_wp_object_cache_storing_content() );
	}
}
