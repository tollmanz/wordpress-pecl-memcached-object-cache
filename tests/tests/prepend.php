<?php

class MemcachedUnitTestsPrepend extends MemcachedUnitTests {
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_prepend_string_fails_with_compression_on() {
		$key = microtime();

		$value = 'jordan';
		$prepended_value = 'pippen';

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append should fail because compression is on by default
		// Note, this should throw an exception, not return false
		$this->assertFalse( $this->object_cache->prepend( $prepended_value, $key ) );
	}


	public function test_prepend_string_succeeds_with_compression_off() {
		$key = microtime();

		$value = 'jordan';
		$prepended_value = 'pippen';
		$combined = $prepended_value . $value;

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append
		$this->assertTrue( $this->object_cache->prepend( $key, $prepended_value ) );

		// Verify prepend
		$this->assertSame( $combined, $this->object_cache->get( $key ) );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_prepend_with_mixed_types() {
		$key = microtime();

		$value = 23;
		$prepended_value = 45.42;
		$combined = $prepended_value . $value;
		settype( $combined, gettype( $value ) );

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append
		$this->assertTrue( $this->object_cache->prepend( $key, $prepended_value ) );

		// Verify prepend
		$this->assertSame( $combined, $this->object_cache->get( $key ) );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_prepend_array_should_fail() {
		$key = microtime();

		$value = array( 'gretzky', 'messier' );
		$prepended_value = array( 'kuri', 'fuhr' );

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append, which should fail due to data type
		$this->assertFalse( $this->object_cache->prepend( $key, $prepended_value ) );
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_prepend_by_key_fails_with_compression_on() {
		$key = microtime();

		$value = 'jordan';
		$prepended_value = 'pippen';

		$server_key = 'bulls';

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append should fail because compression is on by default
		// Note, this should throw an exception, not return false
		$this->assertFalse( $this->object_cache->prependByKey( $server_key, $prepended_value, $key ) );
	}

	public function test_prepend_by_key_string_with_compression_off() {
		$key = microtime();

		$value = 'jordan';
		$prepended_value = 'pippen';
		$combined = $prepended_value . $value;

		$server_key = 'bulls';

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append
		$this->assertTrue( $this->object_cache->prependByKey( $server_key, $key, $prepended_value ) );

		// Verify prepend
		$this->assertSame( $combined, $this->object_cache->getByKey( $server_key, $key ) );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_prepend_by_key_with_mixed_types() {
		$key = microtime();

		$value = 23;
		$prepended_value = 45.42;
		$combined = $prepended_value . $value;
		settype( $combined, gettype( $value ) );

		$server_key = 'chicago';

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append
		$this->assertTrue( $this->object_cache->prependByKey( $server_key, $key, $prepended_value ) );

		// Verify prepend
		$this->assertSame( $combined, $this->object_cache->getByKey( $server_key, $key ) );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_prepend_array_by_key_should_fail() {
		$key = microtime();

		$value = array( 'gretzky', 'messier' );
		$prepended_value = array( 'kuri', 'fuhr' );

		$server_key = 'blackhawks';

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append, which should fail due to data type
		$this->assertFalse( $this->object_cache->prependByKey( $server_key, $key, $prepended_value ) );
	}
}