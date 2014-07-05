<?php

class MemcachedUnitTestsReplace extends MemcachedUnitTests {
	public function test_replace_value_with_another_value() {
		$key = microtime();

		$value1 = 'parise';
		$value2 = 'kovalchuk';

		$this->assertTrue( $this->object_cache->add( $key, $value1 ) );

		$this->assertSame( $value1, $this->object_cache->get( $key ) );

		$this->assertTrue( $this->object_cache->replace( $key, $value2 ) );

		$this->assertSame( $value2, $this->object_cache->get( $key ) );
	}

	public function test_replace_value_when_key_is_not_set() {
		$key = microtime();

		$value = 'parise';

		$this->assertFalse( $this->object_cache->replace( $key, $value ) );

		$this->assertFalse( $this->object_cache->get( $key ) );
	}

	public function test_replace_by_key_value_with_another_value() {
		$key = microtime();

		$value1 = 'parise';
		$value2 = 'kovalchuk';

		$server_key = 'my-server';

		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value1 ) );

		$this->assertSame( $value1, $this->object_cache->getByKey( $server_key, $key ) );

		$this->assertTrue( $this->object_cache->replaceByKey( $server_key, $key, $value2 ) );

		$this->assertSame( $value2, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_replace_by_key_value_when_key_is_not_set() {
		$key = microtime();

		$value = 'parise';

		$server_key = 'my-server';

		$this->assertFalse( $this->object_cache->replaceByKey( $server_key, $key, $value ) );

		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_replace_with_expiration_of_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$built_key = $this->object_cache->buildKey( $key, $group );

		$value2 = 'belgium';

		// 30 days
		$expiration = 60 * 60 * 24 * 30;

		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );
		$this->assertTrue( $this->object_cache->replace( $key, $value2, $group, $expiration ) );

		// Verify that the value is in cache by accessing memcached directly
		$this->assertEquals( $value2, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value2, $this->object_cache->get( $key, $group ) );
	}

	public function test_replace_with_expiration_longer_than_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$built_key = $this->object_cache->buildKey( $key, $group );

		$value2 = 'belgium';

		// 30 days and 1 second; if interpreted as timestamp, becomes "Sat, 31 Jan 1970 00:00:01 GMT"
		$expiration = 60 * 60 * 24 * 30 + 1;

		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );
		$this->assertTrue( $this->object_cache->replace( $key, $value2, $group, $expiration ) );

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