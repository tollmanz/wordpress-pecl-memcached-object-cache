<?php

class MemcachedUnitTestsAdd extends MemcachedUnitTests {
	/**
	 * Verify "add" method with string as value
	 */
	public function test_add_string() {
		$key = microtime();
		$value = 'brodeur';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	/**
	 * Verify "add" method with int as value
	 */
	public function test_add_int() {
		$key = microtime();
		$value = 42;

		// Add int to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value and type is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	/**
	 * Verify "add" method with array as value
	 */
	public function test_add_array() {
		$key = microtime();
		$value = array( 5, 'quick' );

		// Add array to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value and type is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	/**
	 * Verify "add" method values when adding second object with existing key
	 */
	public function test_add_fails_if_key_exists() {
		$key = microtime();
		$value1 = 'parise';
		$value2 = 'king';

		// Verify that one value is added to cache
		$this->assertTrue( $this->object_cache->add( $key, $value1 ) );

		// Make sure second value with same key fails
		$this->assertFalse( $this->object_cache->add( $key, $value2 ) );

		// Make sure the value of the key is still correct
		$this->assertSame( $value1, $this->object_cache->get( $key ) );
	}

	/**
	 * Verify "add" method stores a no_mc_group in
	 */
	public function test_add_avoid_memcached_if_no_mc_group() {
		$key = microtime();
		$group = 'comment';
		$value = 'brown';

		// Verify that the data is added
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify that the data is in the runtime cache
		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );

		// Verify that the data is accessible by the get method
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Verify that the data is not in memcached by making a direct request
		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( $key, $group ) ) );
	}

	/**
	 * Verify "addByKey" method with string as value
	 */
	public function test_add_by_key_string() {
		$key = microtime();
		$value = 'kovalchuk';
		$server_key_real = 'doughty';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key_real, $key, $value ) );

		// Verify correct value/type is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key_real, $key ) );
	}

	/**
	 * Verify "addByKey" method with int as value
	 */
	public function test_add_by_key_int() {
		$key = microtime();
		$value = 42;
		$server_key_real = 'doughty';

		// Add int to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key_real, $key, $value ) );

		// Verify correct value/type is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key_real, $key ) );
	}

	/**
	 * Verify "addByKey" method with array as value
	 */
	public function test_add_by_key_array() {
		$key = microtime();
		$value = array( 5, 'value' );
		$server_key_real = 'doughty';

		// Add array to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key_real, $key, $value ) );

		// Verify correct value/type is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key_real, $key ) );
	}

	/**
	 * Verify "addByKey" method values when adding second object with existing key
	 */
	public function test_add_by_key_fails_if_key_exists() {
		$key = microtime();

		$value1 = 'stevens';
		$value2 = 'kuri';

		$server_key_real = 'doughty';
		$server_key_fake = 'peppers';

		// Verify that one value is added to cache
		$this->assertTrue( $this->object_cache->addByKey( $server_key_real, $key, $value1 ) );

		// Make sure second value with same key fails
		$this->assertFalse( $this->object_cache->addByKey( $server_key_real, $key, $value2 ) );

		// Make sure second value with different server key fails
		$this->assertFalse( $this->object_cache->addByKey( $server_key_fake, $key, $value2 ) );

		// Make sure the value of the key is still correct
		$this->assertSame( $value1, $this->object_cache->getByKey( $server_key_real, $key ) );
	}

	/**
	 * Verify "addByKey" method stores a no_mc_group in
	 */
	public function test_add_by_key_avoid_memcached_if_no_mc_group() {
		$key = microtime();

		$value1 = 'stevens';

		$server_key_real = 'doughty';

		$group = 'comment';

		// Verify that the data is added
		$this->assertTrue( $this->object_cache->addByKey( $server_key_real, $key, $value1, $group ) );

		// Verify that the data is in the runtime cache
		$this->assertSame( $value1, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );

		// Verify that the data is accessible by the get method
		$this->assertSame( $value1, $this->object_cache->getByKey( $server_key_real, $key, $group ) );

		// Verify that the data is not in memcached by making a direct request
		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( $key, $group ) ) );
	}

	/**
	 * Verify that wp_suspend_cache_addition() stops items from being added to cache
	 */
	public function test_add_suspended_by_wp_cache_suspend_addition_string() {
		$key = microtime();
		$value = 'crawford';

		// Suspend the cache
		wp_suspend_cache_addition( true );

		// Attempt to add string to cache
		$this->assertFalse( $this->object_cache->add( $key, $value ) );

		// Verify that the value does not exist in cache
		$this->object_cache->get( $key );
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	/**
	 * Verify that wp_suspend_cache_addition() stops items from being added to cache, but allows additions after re-enabled
	 */
	public function test_add_enabled_by_wp_cache_un_suspend_addition_string() {
		$key = microtime();
		$value = 'miller';

		// Suspend the cache
		wp_suspend_cache_addition( true );

		// Attempt to add string to cache
		$this->assertFalse( $this->object_cache->add( $key, $value ) );

		// Verify that the value does not exist in cache
		$this->object_cache->get( $key );
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );

		$key = microtime();
		$value = 'carruth';

		// Re-enable the cache
		wp_suspend_cache_addition( false );

		// Add the string to the cache
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify that the value is in the cache
		$this->assertSame( $value, $this->object_cache->get( $key ));
	}

	public function test_add_with_expiration_of_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$built_key = $this->object_cache->buildKey( $key, $group );

		// 30 days
		$expiration = 60 * 60 * 24 * 30;

		$this->assertTrue( $this->object_cache->add( $key, $value, $group, $expiration ) );

		// Verify that the value is in cache by accessing memcached directly
		$this->assertEquals( $value, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_add_with_expiration_longer_than_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$built_key = $this->object_cache->buildKey( $key, $group );

		// 30 days and 1 second; if interpreted as timestamp, becomes "Sat, 31 Jan 1970 00:00:01 GMT"
		$expiration = 60 * 60 * 24 * 30 + 1;

		$this->assertTrue( $this->object_cache->add( $key, $value, $group, $expiration ) );

		// Verify that the value is in cache
		$this->assertEquals( $value, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value, $this->object_cache->get( $key, $group ) );
	}
}