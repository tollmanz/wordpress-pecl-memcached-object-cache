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
}