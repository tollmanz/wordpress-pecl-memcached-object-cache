<?php

class MemcachedUnitTestsAppend extends MemcachedUnitTests {
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_append_string_fails_with_compression_on() {
		$key = microtime();

		$value = 'jordan';
		$appended_value = 'pippen';

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append should fail because compression is on by default
		// Note, this should throw an exception, not return false
		$this->assertFalse( $this->object_cache->append( $appended_value, $key ) );
	}


	public function test_append_string_succeeds_with_compression_off() {
		$key = microtime();

		$value = 'jordan';
		$appended_value = 'pippen';
		$combined = $value . $appended_value;

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append
		$this->assertTrue( $this->object_cache->append( $key, $appended_value ) );

		// Verify append
		$this->assertSame( $combined, $this->object_cache->get( $key ) );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_append_int_is_casted_to_string() {
		$key = microtime();

		$value = 23;
		$appended_value = 45.42;
		$combined = (int) $value . (int) $appended_value;
		settype( $combined, 'integer' );

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append
		$this->assertTrue( $this->object_cache->append( $key, $appended_value ) );

		// Verify append
		$this->assertSame( $combined, $this->object_cache->get( $key ) );
		$this->assertSame( $combined, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_append_array_should_fail() {
		$key = microtime();

		$value = array( 'gretzky', 'messier' );
		$appended_value = array( 'kuri', 'fuhr' );

		// Add value
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Append, which should fail due to data type
		$this->assertFalse( $this->object_cache->append( $key, $appended_value ) );
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_append_by_key_fails_with_compression_on() {
		$key = microtime();

		$value = 'jordan';
		$appended_value = 'pippen';

		$server_key = 'bulls';

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $key ) );

		// Append should fail because compression is on by default
		// Note, this should throw an exception, not return false
		$this->assertFalse( $this->object_cache->appendByKey( $server_key, $appended_value, $key ) );
	}

	public function test_append_by_key_string_with_compression_off() {
		$key = microtime();

		$value = 'jordan';
		$appended_value = 'pippen';
		$combined = $value . $appended_value;

		$server_key = 'bulls';

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append
		$this->assertTrue( $this->object_cache->appendByKey( $server_key, $key, $appended_value ) );

		// Verify append
		$this->assertSame( $combined, $this->object_cache->getByKey( $server_key, $key ) );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_append_by_key_int_is_casted_to_string() {
		$key = microtime();

		$value = 23;
		$appended_value = 45.42;
		$combined = (int) $value . (int) $appended_value;
		settype( $combined, 'integer' );

		$server_key = 'chicago';

		// Turn compression off
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, false ) );

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append
		$this->assertTrue( $this->object_cache->appendByKey( $server_key, $key, $appended_value ) );

		// Verify append
		$this->assertSame( $combined, $this->object_cache->getByKey( $server_key, $key ) );
		$this->assertSame( $combined, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );

		// Turn compression back on
		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_COMPRESSION, true ) );
	}

	public function test_append_array_by_key_should_fail() {
		$key = microtime();

		$value = array( 'gretzky', 'messier' );
		$appended_value = array( 'kuri', 'fuhr' );

		$server_key = 'blackhawks';

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify add
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Append, which should fail due to data type
		$this->assertFalse( $this->object_cache->appendByKey( $server_key, $key, $appended_value ) );
	}
}