<?php

class MemcachedUnitTestsCAS extends MemcachedUnitTests {
	public function test_cas_sets_value_correctly() {
		$key = microtime();

		$value = 'ovechkin';
		$new_value = 'crosby';

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Get CAS token
		$this->assertSame( $value, $this->object_cache->get( $key, 'default', false, $found, '', false, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_float( $cas_token ) );

		// Add value via cas
		$this->assertTrue( $this->object_cache->cas( $cas_token, $key, $new_value ) );

		// Verify the value is correct
		$this->assertSame( $new_value, $this->object_cache->get( $key ) );
	}

	public function test_cas_sets_internal_cache() {
		$key = microtime();

		$value = 'ovechkin';
		$new_value = 'crosby';

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Get CAS token
		$cas_token = '';
		$this->assertSame( $value, $this->object_cache->get( $key, 'default', false, $found, '', false, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_float( $cas_token ) );

		// Add value via cas
		$this->assertTrue( $this->object_cache->cas( $cas_token, $key, $new_value ) );

		// Verify the value is correct
		$this->assertSame( $new_value, $this->object_cache->get( $key ) );

		// Verify the internal cache is correctly set
		$this->assertSame( $new_value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_cas_by_key_sets_value_correctly() {
		$key = microtime();

		$value = 'ovechkin';
		$new_value = 'crosby';

		$server_key = 'my-server1';

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Get CAS token
		$cas_token = '';
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, 'default', false, $found, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_float( $cas_token ) );

		// Add value via cas
		$this->assertTrue( $this->object_cache->casByKey( $cas_token, $server_key, $key, $new_value ) );

		// Verify the value is correct
		$this->assertSame( $new_value, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_cas_by_key_sets_internal_cache() {
		$key = microtime();

		$value = 'ovechkin';
		$new_value = 'crosby';

		$server_key = 'my-server1';

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Get CAS token
		$cas_token = '';
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, 'default', false, $found, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_float( $cas_token ) );

		// Add value via cas
		$this->assertTrue( $this->object_cache->casByKey( $cas_token, $server_key, $key, $new_value ) );

		// Verify the value is correct
		$this->assertSame( $new_value, $this->object_cache->getByKey( $server_key, $key ) );

		// Verify the internal cache is correctly set
		$this->assertSame( $new_value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_cas_with_expiration_of_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$value2 = 'dutch';
		$built_key = $this->object_cache->buildKey( $key, $group );
		$found = false;
		$cas_token = '';

		// 30 days
		$expiration = 60 * 60 * 24 * 30;

		$this->assertTrue( $this->object_cache->set( $key, $value, $group, $expiration ) );
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, null, $cas_token ) );

		// Set a new value with the CAS method
		$this->assertTrue( $this->object_cache->cas( $cas_token, $key, $value2, $group, $expiration ) );

		// Verify that the value is in cache by accessing memcached directly
		$this->assertEquals( $value2, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value2, $this->object_cache->get( $key, $group ) );
	}

	public function test_cas_with_expiration_longer_than_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$value2 = 'dutch';
		$built_key = $this->object_cache->buildKey( $key, $group );
		$found = false;
		$cas_token = '';

		// 30 days and 1 second; if interpreted as timestamp, becomes "Sat, 31 Jan 1970 00:00:01 GMT"
		$expiration = 60 * 60 * 24 * 30 + 1;

		$this->assertTrue( $this->object_cache->set( $key, $value, $group, $expiration ) );
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, null, $cas_token ) );

		// Set a new value with the CAS method
		$this->assertTrue( $this->object_cache->cas( $cas_token, $key, $value2, $group, $expiration ) );

		// Verify that the value is in cache by accessing memcached directly
		$this->assertEquals( $value2, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value2, $this->object_cache->get( $key, $group ) );
	}
}