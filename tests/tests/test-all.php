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

	public function test_add_server() {
		$servers = array( array( '127.0.0.1', 11211, 0 ) );

		// Add server
		$this->assertTrue( $this->object_cache->addServer( $servers[0][0], $servers[0][1], $servers[0][2] ) );
	}

	public function test_add_servers() {
		$servers = array( array( '127.0.0.1', 11211, 1 ), array( '127.0.0.1', 11212, 1 ) );

		// Add server
		$this->assertTrue( $this->object_cache->addServers( $servers ) );
	}

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

	public function test_decrement_reduces_value_by_1() {
		$key = microtime();

		$value = 99;

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify that value was properly decremented
		$this->assertSame( 98, $this->object_cache->decrement( $key ) );
	}

	public function test_decrement_reduces_value_by_x() {
		$key = microtime();

		$value = 99;
		$x = 5;

		$reduced_value = $value - $x;

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify that value was properly decremented
		$this->assertSame( $reduced_value, $this->object_cache->decrement( $key, $x ) );
	}

	public function test_decrement_reduces_value_by_1_for_no_mc_group() {
		$key = microtime();

		$value = 99;
		$group = 'counts';

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Verify that value was properly decremented
		$this->assertSame( 98, $this->object_cache->decrement( $key, 1, $group ) );
	}

	public function test_decrement_reduces_value_by_x_for_no_mc_group() {
		$key = microtime();

		$value = 99;
		$x = 5;

		$group = 'counts';

		$reduced_value = $value - $x;

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Verify that value was properly decremented
		$this->assertSame( $reduced_value, $this->object_cache->decrement( $key, $x, $group ) );
	}

	public function test_delete_key() {
		$key = microtime();

		$value = 'sasquatch';
		$value2  = 'yeti';

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify delete
		$this->assertTrue( $this->object_cache->delete( $key ) );

		// Verify that key is not gettable after delete
		$this->assertFalse( $this->object_cache->get( $key ) );

		// Verify that I can add a new value with this key
		$this->assertTrue( $this->object_cache->add( $key, $value2 ) );

		// Verify the new value
		$this->assertSame( $value2, $this->object_cache->get( $key ) );
	}

	public function test_delete_key_from_no_mc_group() {
		$key = microtime();

		$value = 'sasquatch';
		$value2  = 'yeti';

		$group = 'comment';

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Verify value is in the internal cache
		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );

		// Verify delete
		$this->assertTrue( $this->object_cache->delete( $key, $group ) );

		// Verify that key is not gettable after delete
		$this->assertFalse( $this->object_cache->get( $key, $group ) );

		// Verify that I can add a new value with this key
		$this->assertTrue( $this->object_cache->add( $key, $value2, $group ) );

		// Verify the new value
		$this->assertSame( $value2, $this->object_cache->get( $key, $group ) );
	}

	public function test_delete_by_key() {
		$key = microtime();

		$value = 'sasquatch';
		$value2  = 'yeti';

		$server_key = 'my-server1';

		// Verify set
		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Verify delete
		$this->assertTrue( $this->object_cache->deleteByKey( $server_key, $key ) );

		// Verify that key is not gettable after delete
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key ) );

		// Verify that I can add a new value with this key
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value2 ) );

		// Verify the new value
		$this->assertSame( $value2, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_delete_by_key_from_no_mc_group() {
		$key = microtime();

		$value = 'sasquatch';
		$value2  = 'yeti';

		$server_key = 'my-server1';

		$group = 'comment';

		// Verify set
		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value, $group ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );

		// Verify delete
		$this->assertTrue( $this->object_cache->deleteByKey( $server_key, $key, $group ) );

		// Verify that key is not gettable after delete
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group ) );

		// Verify that I can add a new value with this key
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value2, $group ) );

		// Verify the new value
		$this->assertSame( $value2, $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_fetch_gets_next_result() {
		$keys = array(
			microtime() . '-lemieux' => 66,
			microtime() . '-jagr' => 'sixty-eight'
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value )
			$rekeyed[$this->object_cache->buildKey( $key )] = $value;

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->set( $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
	   	$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ) ) );

		// Loop through the initial values and see if an appropriate value was returned. Note that they likely won't come back in the same order.
		foreach ( $keys as $key => $value ) {
			$next = $this->object_cache->fetch();
			$this->assertTrue( isset( $rekeyed[$next['key']] ) );
			$this->assertSame( $rekeyed[$next['key']], $next['value'] );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_fetch_all_gets_all_results() {
		$keys = array(
			microtime() . '-lemieux' => 66,
			microtime() . '-jagr' => 'sixty-eight'
		);

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->set( $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
	   	$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ) ) );

		// Build the final expect result array
		$result_array = array();
		foreach ( $keys as $key => $value ) {
			$result_array[] = array( 'key' => $this->object_cache->buildKey( $key, 'default' ), 'value' => $value );
		}

		// Make sure fetchAll returns expected array
		$this->assertTrue( $result_array === $this->object_cache->fetchAll() );

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetchAll() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_flush() {
		$key = microtime();
		$value = 'brodeur';

		// Add to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value and type is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Flush cache
   	    $this->assertTrue( $this->object_cache->flush() );

		// Make sure value is no longer available
		$this->assertFalse( $this->object_cache->get( $key ) );
	}

	public function test_get_value() {
		$key = microtime();
		$value = 'brodeur';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	public function test_get_value_twice() {
		$key = microtime();
		$value = 'brodeur';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify correct value is returned when pulled from the internal cache
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	public function test_get_value_with_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'devils';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_no_mc_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'comment';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_global_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'usermeta';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_found_indicator() {
		$key = microtime();
		$value = 'karlson';
		$group = 'senators';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertTrue( $found );
	}

	public function test_get_value_with_found_indicator_when_value_is_not_found() {
		$key = microtime();
		$value = 'neil';
		$group = 'senators';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify that the value is deleted
		$this->assertTrue( $this->object_cache->delete( $key, $group ) );

		// Verify that false is returned
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertFalse( $found );
	}

	public function test_get_value_with_found_indicator_when_retrieved_from_memcached() {
		$key = microtime();
		$value = 'holtby';
		$group = 'capitals';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Remove from internal cache and verify
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertTrue( $found );
	}

	public function test_get_value_with_found_indicator_when_retrieved_from_memcached_and_value_is_not_found() {
		$key = microtime();
		$value = 'backstrom';
		$group = 'capitals';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Remove from internal cache and verify
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Verify that the value is deleted
		$this->assertTrue( $this->object_cache->delete( $key, $group ) );

		// Verify that false is returned
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertFalse( $found );
	}

	public function test_get_value_with_callback_with_true_response() {
		$key = microtime();
		$group = 'nj-devils';

		$value = 'brodeur';

		// Verify that callback sets value correctly
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', true, 'memcached_get_callback_true' ) );

		// Doublecheck it
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_callback_with_false_response() {
		$key = microtime();
		$group = 'nhl-nj-devils';

		$value = 'brodeur';

		// Verify that callback sets value correctly
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found, '', false, 'memcached_get_callback_false' ) );

		// Doublecheck it
		$this->assertFalse( $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_callback_with_true_response_and_using_class_method() {
		$key = microtime();
		$group = 'nhl-nj-devils-team';

		$value = 'brodeur';

		// Verify that callback sets value correctly
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_true_class_method' ) ) );

		// Doublecheck it
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function memcached_get_callback_true_class_method( $m, $key, &$value ) {
		$value = 'brodeur';
		return true;
	}

	public function test_get_value_with_callback_with_false_response_and_using_class_method() {
		$key = microtime();
		$group = 'nhl-nj-devils-team-runner-up';

		// Verify that callback sets value correctly
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_false_class_method' ) ) );

		// Doublecheck it
		$this->assertFalse( $this->object_cache->get( $key, $group ) );
	}

	public function memcached_get_callback_false_class_method( $m, $key, &$value ) {
		$value = 'brodeur';
		return false;
	}

	public function test_get_value_with_callback_ignores_callback_for_no_mc_group() {
		$key = microtime();
		$group = 'comment';

		$value = 'brodeur';

		// Verify that if completely bypassed
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_true_no_mc_group' ) ) );

		// Doublecheck that no value has been set
		$this->assertFalse( $this->object_cache->get( $key, $group ) );

		// Verify that a normal set and get works when a callback is sent
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_true_no_mc_group' ) ) );
	}

	public function memcached_get_callback_true_no_mc_group( $m, $key, &$value ) {
		$value = 'parise';
		return true;
	}

	public function test_get_value_and_return_cas_token() {
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

	public function test_get_value_return_null_cas_token_with_not_found_key() {
		$key = microtime();

		// Return false with value not yet set
		$this->assertFalse( $this->object_cache->get( $key, 'default', false, $found, '', false, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_null( $cas_token ) );
	}

	public function test_get_value_with_cas_token_and_callback() {
		$key = microtime();

		$value = 'brodeur';
		$group = 'devils';

		// Set value via the callback when key is not set
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_for_cas_token_and_callback' ), $cas_token ) );

		// Double check the value
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Since not received from memcached, cas_token should be (float) 0
		$this->assertTrue( (float) 0 === $cas_token );

		// The value should now be set and this function should return the same result
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_for_cas_token_and_callback' ), $cas_token ) );

		// See if we got an acceptable CAS token
		$this->assertTrue( is_float( $cas_token ) && $cas_token > 0 );
	}

	public function memcached_get_callback_for_cas_token_and_callback( $m, $key, &$value ) {
		$value = 'brodeur';
		return true;
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_get_expect_exception_when_cache_cb_is_not_callable() {
		$key = microtime();

		$value = 'brodeur';
		$group = 'devils';

		// Set value via the callback when key is not set
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'fake_function' ) ) );

	}

	public function test_get_by_key_value() {
		$key = microtime();
		$value = 'brodeur';

		$server_key = microtime();

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_get_by_key_value_twice() {
		$key = microtime();
		$value = 'brodeur';

		$server_key = microtime();

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		// Verify correct value is returned when pulled from the internal cache
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_get_by_key_value_with_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'devils';

		$server_key = microtime();

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_no_mc_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'comment';

		$server_key = microtime();

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_global_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'usermeta';

		$server_key = microtime();

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_found_indicator() {
		$key = microtime();
		$server_key = microtime();
		$value = 'johansen';
		$group = 'senators';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertTrue( $found );
	}

	public function test_get_by_key_value_with_found_indicator_when_value_is_not_found() {
		$key = microtime();
		$server_key = microtime();
		$value = 'fisher';
		$group = 'senators';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Verify that the value is deleted
		$this->assertTrue( $this->object_cache->deleteByKey( $server_key, $key, $group ) );

		// Verify that false is returned
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertFalse( $found );
	}

	public function test_get_by_key_value_with_found_indicator_when_retrieved_from_memcached() {
		$key = microtime();
		$server_key = microtime();
		$value = 'ovechkin';
		$group = 'capitals';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Remove from internal cache and verify
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertTrue( $found );
	}

	public function test_get_by_key_value_with_found_indicator_when_retrieved_from_memcached_and_value_is_not_found() {
		$key = microtime();
		$server_key = microtime();
		$value = 'simmonds';
		$group = 'flyers';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value, $group ) );

		// Remove from internal cache and verify
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Verify that the value is deleted
		$this->assertTrue( $this->object_cache->deleteByKey( $server_key, $key, $group ) );

		// Verify that false is returned
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertFalse( $found );
	}

	public function test_get_by_key_value_with_callback_with_true_response() {
		$key = microtime();
		$group = 'nj-devils';

		$value = 'brodeur';

		$server_key = microtime();

		// Verify that callback sets value correctly
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found, 'memcached_get_callback_true' ) );

		// Doublecheck it
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_callback_with_false_response() {
		$key = microtime();
		$group = 'nhl-nj-devils';

		$server_key = microtime();

		// Verify that callback sets value correctly
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group, false, $found, 'memcached_get_callback_false' ) );

		// Doublecheck it
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_callback_with_true_response_and_using_class_method() {
		$key = microtime();
		$group = 'nhl-nj-devils-team';

		$value = 'brodeur';

		$server_key = microtime();

		// Verify that callback sets value correctly
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'memcached_get_callback_true_class_method' ) ) );

		// Doublecheck it
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_callback_with_false_response_and_using_class_method() {
		$key = microtime();
		$group = 'nhl-nj-devils-team-runner-up';

		$server_key = microtime();

		// Verify that callback sets value correctly
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'memcached_get_callback_false_class_method' ) ) );

		// Doublecheck it
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group ) );
	}

	public function test_get_by_key_value_with_callback_ignores_callback_for_no_mc_group() {
		$key = microtime();
		$group = 'comment';

		$value = 'brodeur';

		$server_key = microtime();

		// Verify that if completely bypassed
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'memcached_get_callback_true_no_mc_group' ) ) );

		// Doublecheck that no value has been set
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, $group ) );

		// Verify that a normal set and get works when a callback is sent
		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value, $group ) );
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'memcached_get_callback_true_no_mc_group' ) ) );
	}

	public function test_get_by_value_value_and_return_cas_token() {
		$key = microtime();

		$value = 'ovechkin';
		$new_value = 'crosby';

		$server_key = microtime();

		// Add value
		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		// Get CAS token
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, 'default', false, $found, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_float( $cas_token ) );

		// Add value via cas
		$this->assertTrue( $this->object_cache->casByKey( $cas_token, $server_key, $key, $new_value ) );

		// Verify the value is correct
		$this->assertSame( $new_value, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_get_by_key_value_return_null_cas_token_with_not_found_key() {
		$key = microtime();

		$server_key = microtime();

		// Return false with value not yet set
		$this->assertFalse( $this->object_cache->getByKey( $server_key, $key, 'default', false, $found, null, $cas_token ) );

		// Verify that we have a CAS token
		$this->assertTrue( is_null( $cas_token ) );
	}

	public function test_get_by_key_value_with_cas_token_and_callback() {
		$key = microtime();

		$value = 'brodeur';
		$group = 'wild';

		$server_key = 'devils';

		// Set value via the callback when key is not set
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'memcached_get_callback_for_cas_token_and_callback' ), $cas_token ) );

		// Since not received from memcached, cas_token should be (float) 0
		$this->assertTrue( (float) 0 === $cas_token );

		// Double check the value
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );

		// The value should now be set and this function should return the same result
		$result = $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'memcached_get_callback_for_cas_token_and_callback' ), $cas_token2 );
		$this->assertSame( $value, $result );

		// See if we got an acceptable CAS token
		$this->assertTrue( is_float( $cas_token2 ) );
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_get_by_key_expect_exception_when_cache_cb_is_not_callable() {
		$key = microtime();

		$value = 'brodeur';
		$group = 'devils';

		$server_key = microtime();

		// Set value via the callback when key is not set
		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group, false, $found, array( &$this, 'fake_function' ) ) );
	}

	public function test_get_delayed_returns_correct_values() {
		$keys = array(
			microtime() . '-sharp' => 10,
			microtime() . '-toews' => 'nineteen'
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value )
			$rekeyed[$this->object_cache->buildKey( $key )] = $value;

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->set( $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ) ) );

		// Loop through the initial values and see if they match what was returned
		// Loop through the initial values and see if an appropriate value was returned. Note that they likely won't come back in the same order.
		foreach ( $keys as $key => $value ) {
			$next = $this->object_cache->fetch();
			$this->assertTrue( isset( $rekeyed[$next['key']] ) );
			$this->assertSame( $rekeyed[$next['key']], $next['value'] );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_returns_correct_values_with_different_groups() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'blackhawks'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'pengiuns'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$rekeyed[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value;
			else
				$rekeyed[ $this->object_cache->buildKey( $key ) ] = $value;
		}

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		// Set each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->set( $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->set( $key, $value['value'] ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertSame( $value['value'], $this->object_cache->get( $key, $value['group'] ) );
			else
				$this->assertSame( $value['value'], $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ), $groups ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			$next = $this->object_cache->fetch();
			$this->assertTrue( isset( $rekeyed[$next['key']] ) );
			$this->assertSame( $rekeyed[$next['key']]['value'], $next['value'] );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_returns_correct_values_with_cas_tokens() {
		$keys = array(
			microtime() . '-sharp' => 10,
			microtime() . '-toews' => 'nineteen'
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value )
			$rekeyed[$this->object_cache->buildKey( $key )] = $value;

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->set( $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ), 'default', true ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			// Get next result
			$next = $this->object_cache->fetch();

			// Test that correct value was returned
			$this->assertTrue( isset( $rekeyed[$next['key']] ) );
			$this->assertSame( $rekeyed[$next['key']], $next['value'] );

			$this->assertTrue( is_float( $next['cas'] ) && $next['cas'] > 0 );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_returns_correct_values_with_different_groups_and_cas_tokens() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'blackhawks'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'pengiuns'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$rekeyed[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value;
			else
				$rekeyed[ $this->object_cache->buildKey( $key ) ] = $value;
		}

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		// Set each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->set( $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->set( $key, $value['value'] ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertSame( $value['value'], $this->object_cache->get( $key, $value['group'] ) );
			else
				$this->assertSame( $value['value'], $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ), $groups, true ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			$next = $this->object_cache->fetch();
			$this->assertTrue( isset( $rekeyed[$next['key']] ) );
			$this->assertSame( $rekeyed[$next['key']]['value'], $next['value'] );

			// Verify appropriate cas token
			$this->assertTrue( is_float( $next['cas'] ) && $next['cas'] > 0 );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_by_key_returns_correct_values_with_fetch() {
		$keys = array(
			microtime() . '-sharp' => 10,
			microtime() . '-toews' => 'nineteen'
		);

		$server_key = microtime();

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ) ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key, 'default' ), 'value' => $value ), $this->object_cache->fetch() );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_returns_correct_values_and_calls_callback_function() {
		$keys = $this->data_for_get_delayed_with_callbacks();

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->set( $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ), 'default', false, array( &$this, 'get_delayed_returns_correct_values_and_calls_callback_function' ) ) );

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_returns_correct_values_and_calls_callback_function_with_cas() {
		$keys = $this->data_for_get_delayed_with_callbacks();

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->set( $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->get( $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayed( array_keys( $keys ), 'default', true, array( &$this, 'get_delayed_returns_correct_values_and_calls_callback_function' ) ) );

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function get_delayed_returns_correct_values_and_calls_callback_function( $m, $item ) {
		$keys = $this->data_for_get_delayed_with_callbacks();

		$new_keys = array();
		foreach ( $keys as $key => $value )
			$new_keys[ $this->object_cache->buildKey( $key ) ] = $value;

		$this->assertSame( $new_keys[ $item['key'] ], $item['value'] );

		if ( isset( $item['cas'] ) )
			$this->assertTrue( is_float( $item['cas'] ) && $item['cas'] > 0 );

		$this->assertTrue( is_a( $m, 'Memcached' ) );
	}


	public function test_get_delayed_by_key_returns_correct_values() {
		$keys = array(
			microtime() . '-sharp' => 10,
			microtime() . '-toews' => 'nineteen'
		);

		$server_key = 'your-server';

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ) ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key, 'default' ), 'value' => $value ), $this->object_cache->fetch() );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_by_key_returns_correct_values_with_different_groups() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'blackhawks'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'pengiuns'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$server_key = 'your-server';

		// Set each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'] ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key, $value['group'] ) );
			else
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ), $groups ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key, $value['group'] ), 'value' => $value['value'] ), $this->object_cache->fetch() );
			else
				$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key ), 'value' => $value['value'] ), $this->object_cache->fetch() );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_by_key_returns_correct_values_with_cas_tokens() {
		$keys = array(
			microtime() . '-sharp' => 10,
			microtime() . '-toews' => 'nineteen'
		);

		$server_key = 'your-server';

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ), 'default', true ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			// Get next result
			$next_result = $this->object_cache->fetch();

			// Get the cas token and remove from the return array for next assertion
			$cas_token = $next_result['cas'];
			unset( $next_result['cas'] );

			// Test that correct value was returned
			$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key, 'default' ), 'value' => $value ), $next_result );
			$this->assertTrue( is_float( $cas_token ) && $cas_token > 0 );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_by_key_returns_correct_values_with_different_groups_and_cas_tokens() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'blackhawks'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'pengiuns'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$server_key = 'your-server';

		// Set each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'] ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key, $value['group'] ) );
			else
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ), $groups, true ) );

		// Loop through the initial values and see if they match what was returned
		foreach ( $keys as $key => $value ) {
			// Get next result
			$next_result = $this->object_cache->fetch();

			// Get the cas token and remove from the return array for next assertion
			$cas_token = $next_result['cas'];
			unset( $next_result['cas'] );

			if ( isset( $value['group'] ) )
				$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key, $value['group'] ), 'value' => $value['value'] ), $next_result );
			else
				$this->assertSame( array( 'key' => $this->object_cache->buildKey( $key ), 'value' => $value['value'] ), $next_result );

			// Verify appropriate cas token
			$this->assertTrue( is_float( $cas_token ) && $cas_token > 0 );
		}

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_by_key_returns_correct_values_and_calls_callback_function() {
		$keys = $this->data_for_get_delayed_with_callbacks();

		$server_key = 'your-server';

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ), 'default', false, array( &$this, 'get_delayed_returns_correct_values_and_calls_callback_function' ) ) );

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	public function test_get_delayed_by_returns_correct_values_and_calls_callback_function_with_cas() {
		$keys = $this->data_for_get_delayed_with_callbacks();

		$server_key = 'your-server';

		// Set each value
		foreach ( $keys as $key => $value ) {
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );
		}

		// Verify each value
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
		}

		// getDelayed to retrieve the objects
		$this->assertTrue( $this->object_cache->getDelayedByKey( $server_key, array_keys( $keys ), 'default', true, array( &$this, 'get_delayed_returns_correct_values_and_calls_callback_function' ) ) );

		// Doing another fetch call should result in false
		$this->assertFalse( $this->object_cache->fetch() );

		// Finally, that last operation should result in the result code of Memcached::RES_NOTFOUND
		$this->assertSame( Memcached::RES_NOTFOUND, $this->object_cache->getResultCode() );
	}

	/**
	 * Used by the main test function and the callback so data is in sync.
	 *
	 * @return array
	 */
	public function data_for_get_delayed_with_callbacks() {
		return array(
			'-sharp' => 10,
			'-toews' => 'nineteen'
		);
	}

	public function test_get_multi_gets_multiple_values_if_added_individually() {
		$values = array(
			87 => 'crosby',
			99 => 'gretzky',
			'68' => 'jagr'
		);

		$keyed_values = array();
		foreach ( $values as $key => $value )
			$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value;

		// Add each key individually
		foreach ( $values as $key => $value )
			$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMulti( array_keys( $values ) ) ) );

		// Test that return is same as input with same order
		$this->assertSame( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), 'default', '', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );
	}

	public function test_get_multi_gets_multiple_values_if_added_individually_with_different_groups() {
		$values = array(
			87 => array(
				'value' => 'crosby',
				'group' => 'penguins'
			),
			99 => array(
				'value' => 'gretzky',
				'group' => 'oilers'
			),
			'68' => array(
				'value' => 'jagr'
			)
		);

		$groups = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$keyed_values = array();
		foreach ( $values as $key => $value ) {
		    if ( isset( $value['group'] ) )
				$keyed_values[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value['value'];
			else
				$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value['value'];
		}

		// Add each key individually
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->set( $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->set( $key, $value['value'] ) );
		}

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), $groups ) ) );

		// Test for same order with flag set
		$this->assertSame( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), $groups, '', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );
	}

	public function test_get_multi_gets_multiple_values_if_added_individually_with_different_groups_including_no_mc_groups() {
		$values = array(
			87 => array(
				'value' => 'crosby',
				'group' => 'penguins'
			),
			99 => array(
				'value' => 'gretzky',
				'group' => 'oilers'
			),
			'68' => array(
				'value' => 'jagr'
			),
			// If these values are changed, be sure to change hardcoded versions below
			'the-one-with-no-name' => array(
				'value' => 'bettman',
				'group' => 'counts'
			)
		);

		$groups = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
			else
				$groups[] = 'default';
		}

		$keyed_values = array();
		foreach ( $values as $key => $value ) {
		    if ( isset( $value['group'] ) )
				$keyed_values[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value['value'];
			else
				$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value['value'];
		}

		// Add each key individually
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->set( $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->set( $key, $value['value'] ) );
		}

		// Test that return is same as input
		$this->assertSame( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), $groups ) );

		// Make sure that the no_mc_group value never made it to memcached
		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ) );

		// Verify the no_mc_group value is in the internal cache
		$this->assertSame( 'bettman', $this->object_cache->cache[ $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ] );
	}

	public function test_get_multi_gets_multiple_values_if_some_are_not_in_internal_cache() {
		$values = array(
			87 => 'crosby',
			99 => 'gretzky',
			'68' => 'jagr'
		);

		$keyed_values = array();
		foreach ( $values as $key => $value )
			$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value;

		// Add each key individually
		foreach ( $values as $key => $value )
			$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Remove a value from internal cache
		$this->assertSame( 'gretzky', $this->object_cache->cache[ $this->object_cache->buildKey( 99 ) ] );
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( 99 ) ] );
		$this->assertFalse( isset( $this->object_cache->cache[ $this->object_cache->buildKey( 99 ) ] ) );

		// Test that the right values are returned is the wrong order without the flag set
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMulti( array_keys( $values ) ) ) );

		// Test that return is same as input
		$this->assertSame( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), 'default', '', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );
	}

	public function test_get_multi_order_if_added_individually_with_different_groups_including_no_mc_groups_and_if_some_are_not_in_internal_cache() {
		$values = array(
			87 => array(
				'value' => 'crosby',
				'group' => 'penguins'
			),
			99 => array(
				'value' => 'gretzky',
				'group' => 'oilers'
			),
			'68' => array(
				'value' => 'jagr'
			),
			// If these values are changed, be sure to change hardcoded versions below
			'the-one-with-no-name' => array(
				'value' => 'bettman',
				'group' => 'counts'
			)
		);

		$groups = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
			else
				$groups[] = 'default';
		}

		$keyed_values = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$keyed_values[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value['value'];
			else
				$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value['value'];
		}

		// Add each key individually
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->set( $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->set( $key, $value['value'] ) );
		}

		// Remove a value from internal cache
		$this->assertSame( 'gretzky', $this->object_cache->cache[ $this->object_cache->buildKey( 99, 'oilers' ) ] );
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( 99, 'oilers' ) ] );
		$this->assertFalse( isset( $this->object_cache->cache[ $this->object_cache->buildKey( 99, 'oilers' ) ] ) );

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), $groups ) ) );

		// Test order when sending the 4th arg
		$this->assertSame( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), $groups, '', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );

		// Make sure that the no_mc_group value never made it to memcached
		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ) );

		// Verify the no_mc_group value is in the internal cache
		$this->assertSame( 'bettman', $this->object_cache->cache[ $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ] );
	}

	public function test_get_multi_cas_tokens() {
		$values = array(
			87 => 'crosby',
			99 => 'gretzky',
			'68' => 'jagr'
		);

		$keyed_values = array();
		foreach ( $values as $key => $value )
			$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value;

		// Add each key individually
		foreach ( $values as $key => $value )
			$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMulti( array_keys( $values ) ) ) );

		// Test that return is same as input with same order
		$this->assertSame( $keyed_values, $this->object_cache->getMulti( array_keys( $values ), 'default', '', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );

		// Verify CAS tokens
		$this->assertTrue( isset( $cas_tokens ) );

		foreach ( $cas_tokens as $token )
			$this->assertTrue( is_float( $token ) && $token > 0 );
	}

	public function test_get_multi_by_key_gets_multiple_values_if_added_individually() {
		$values = array(
			87 => 'crosby',
			99 => 'gretzky',
			'68' => 'jagr'
		);

		$server_key = 'stark';

		$keyed_values = array();
		foreach ( $values as $key => $value )
			$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value;

		// Add each key individually
		foreach ( $values as $key => $value )
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ) ) ) );

		// Test that return is same as input with same order
		$this->assertSame( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), 'default', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );
	}

	public function test_get_multi_by_key_gets_multiple_values_if_added_individually_with_different_groups() {
		$values = array(
			87 => array(
				'value' => 'crosby',
				'group' => 'penguins'
			),
			99 => array(
				'value' => 'gretzky',
				'group' => 'oilers'
			),
			'68' => array(
				'value' => 'jagr'
			)
		);

		$server_key = 'test-key';

		$groups = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$keyed_values = array();
		foreach ( $values as $key => $value ) {
		    if ( isset( $value['group'] ) )
				$keyed_values[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value['value'];
			else
				$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value['value'];
		}

		// Add each key individually
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'] ) );
		}

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), $groups ) ) );

		// Test for same order with flag set
		$this->assertSame( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), $groups, $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );
	}

	public function test_get_multi_by_key_gets_multiple_values_if_added_individually_with_different_groups_including_no_mc_groups() {
		$values = array(
			87 => array(
				'value' => 'crosby',
				'group' => 'penguins'
			),
			99 => array(
				'value' => 'gretzky',
				'group' => 'oilers'
			),
			'68' => array(
				'value' => 'jagr'
			),
			// If these values are changed, be sure to change hardcoded versions below
			'the-one-with-no-name' => array(
				'value' => 'bettman',
				'group' => 'counts'
			)
		);

		$server_key = 'a-really-nice-key';

		$groups = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
			else
				$groups[] = 'default';
		}

		$keyed_values = array();
		foreach ( $values as $key => $value ) {
		    if ( isset( $value['group'] ) )
				$keyed_values[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value['value'];
			else
				$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value['value'];
		}

		// Add each key individually
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'] ) );
		}

		// Test that return is same as input
		$this->assertSame( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), $groups ) );

		// Make sure that the no_mc_group value never made it to memcached
		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ) );

		// Verify the no_mc_group value is in the internal cache
		$this->assertSame( 'bettman', $this->object_cache->cache[ $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ] );
	}

	public function test_get_multi_by_key_gets_multiple_values_if_some_are_not_in_internal_cache() {
		$values = array(
			87 => 'crosby',
			99 => 'gretzky',
			'68' => 'jagr'
		);

		$server_key = 'a-delight';

		$keyed_values = array();
		foreach ( $values as $key => $value )
			$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value;

		// Add each key individually
		foreach ( $values as $key => $value )
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );

		// Remove a value from internal cache
		$this->assertSame( 'gretzky', $this->object_cache->cache[ $this->object_cache->buildKey( 99 ) ] );
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( 99 ) ] );
		$this->assertFalse( isset( $this->object_cache->cache[ $this->object_cache->buildKey( 99 ) ] ) );

		// Test that the right values are returned is the wrong order without the flag set
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ) ) ) );

		// Test that return is same as input
		$this->assertSame( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), 'default', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );
	}

	public function test_get_multi_by_key_order_if_added_individually_with_different_groups_including_no_mc_groups_and_if_some_are_not_in_internal_cache() {
		$values = array(
			87 => array(
				'value' => 'crosby',
				'group' => 'penguins'
			),
			99 => array(
				'value' => 'gretzky',
				'group' => 'oilers'
			),
			'68' => array(
				'value' => 'jagr'
			),
			// If these values are changed, be sure to change hardcoded versions below
			'the-one-with-no-name' => array(
				'value' => 'bettman',
				'group' => 'counts'
			)
		);

		$server_key = 'holly-jolly';

		$groups = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
			else
				$groups[] = 'default';
		}

		$keyed_values = array();
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$keyed_values[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value['value'];
			else
				$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value['value'];
		}

		// Add each key individually
		foreach ( $values as $key => $value ) {
			if ( isset( $value['group'] ) )
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'], $value['group'] ) );
			else
				$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value['value'] ) );
		}

		// Remove a value from internal cache
		$this->assertSame( 'gretzky', $this->object_cache->cache[ $this->object_cache->buildKey( 99, 'oilers' ) ] );
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( 99, 'oilers' ) ] );
		$this->assertFalse( isset( $this->object_cache->cache[ $this->object_cache->buildKey( 99, 'oilers' ) ] ) );

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), $groups ) ) );

		// Test order when sending the 4th arg
		$this->assertSame( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), $groups, $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );

		// Make sure that the no_mc_group value never made it to memcached
		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ) );

		// Verify the no_mc_group value is in the internal cache
		$this->assertSame( 'bettman', $this->object_cache->cache[ $this->object_cache->buildKey( 'the-one-with-no-name', 'counts' ) ] );
	}

	public function test_get_multi_by_key_cas_tokens() {
		$values = array(
			87 => 'crosby',
			99 => 'gretzky',
			'68' => 'jagr'
		);

		$server_key = 'songo';

		$keyed_values = array();
		foreach ( $values as $key => $value )
			$keyed_values[ $this->object_cache->buildKey( $key, 'default' ) ] = $value;

		// Add each key individually
		foreach ( $values as $key => $value )
			$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );

		// Test that return is same as input
		$this->assertEmpty( array_diff( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ) ) ) );

		// Test that return is same as input with same order
		$this->assertSame( $keyed_values, $this->object_cache->getMultiByKey( $server_key, array_keys( $values ), 'default', $cas_tokens, Memcached::GET_PRESERVE_ORDER ) );

		// Verify CAS tokens
		$this->assertTrue( isset( $cas_tokens ) );

		foreach ( $cas_tokens as $token )
			$this->assertTrue( is_float( $token ) && $token > 0 );
	}

	public function test_get_all_options() {
		$this->assertContainsOnly( 'boolean', array( $this->object_cache->getOption( Memcached::OPT_COMPRESSION ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_SERIALIZER ) ) );
		$this->assertContainsOnly( 'string', array( $this->object_cache->getOption( Memcached::OPT_PREFIX_KEY ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_HASH ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_DISTRIBUTION ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_LIBKETAMA_COMPATIBLE ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_BUFFER_WRITES ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_BINARY_PROTOCOL ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_NO_BLOCK ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_TCP_NODELAY ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_SOCKET_SEND_SIZE ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_SOCKET_RECV_SIZE ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_CONNECT_TIMEOUT ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_RETRY_TIMEOUT ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_SEND_TIMEOUT ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_POLL_TIMEOUT ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_RECV_TIMEOUT ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_SERVER_FAILURE_LIMIT ) ) );
		$this->assertContainsOnly( 'int', array( $this->object_cache->getOption( Memcached::OPT_CACHE_LOOKUPS ) ) );
	}

	public function test_get_result_code_returns_int() {
		$key = microtime();
		$value = 'test';

		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		$this->assertContainsOnly( 'int', array( $this->object_cache->getResultCode() ) );

		$this->assertSame( $value, $this->object_cache->get( $key ) );

		$this->assertContainsOnly( 'int', array( $this->object_cache->getResultCode() ) );
	}

	public function test_get_result_message_returns_string() {
		$key = microtime();
		$value = 'test';

		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		$this->assertContainsOnly( 'string', array( $this->object_cache->getResultMessage() ) );

		$this->assertSame( $value, $this->object_cache->get( $key ) );

		$this->assertContainsOnly( 'string', array( $this->object_cache->getResultMessage() ) );
	}

	public function test_get_server_by_key() {
		$key = microtime();
		$value = 'mwanga';
		$server_key = 'perkins';

		$this->assertTrue( $this->object_cache->addByKey( $server_key, $key, $value ) );

		$this->assertContainsOnly( 'array', array( $this->object_cache->getServerByKey( $server_key ) ) );
	}

	public function test_get_server_list() {
		$this->assertContainsOnly( 'array', array( $this->object_cache->getServerList() ) );
	}

	public function test_get_stats() {
		// I'm getting a weird failure to get stats whenever the Linode server is used. Account for this here.
		if ( ! array_key_exists( 'linode', $this->servers ) )
			$this->assertTrue( is_array( $this->object_cache->getStats() ) );
	}

	public function test_get_version() {
		$this->assertTrue( is_array( $this->object_cache->getVersion() ) );
	}

	public function test_increment_reduces_value_by_1() {
		$key = microtime();

		$value = 99;

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify that value was properly incremented
		$this->assertSame( 100, $this->object_cache->increment( $key ) );
	}

	public function test_increment_reduces_value_by_x() {
		$key = microtime();

		$value = 99;
		$x = 5;

		$reduced_value = $value + $x;

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify that value was properly incremented
		$this->assertSame( $reduced_value, $this->object_cache->increment( $key, $x ) );
	}

	public function test_increment_reduces_value_by_1_for_no_mc_group() {
		$key = microtime();

		$value = 99;
		$group = 'counts';

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Verify that value was properly incremented
		$this->assertSame( 100, $this->object_cache->increment( $key, 1, $group ) );
	}

	public function test_increment_reduces_value_by_x_for_no_mc_group() {
		$key = microtime();

		$value = 99;
		$x = 5;

		$group = 'counts';

		$reduced_value = $value + $x;

		// Verify set
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		// Verify value
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		// Verify that value was properly incremented
		$this->assertSame( $reduced_value, $this->object_cache->increment( $key, $x, $group ) );
	}

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

	public function test_set_value() {
		$key = microtime();

		$value = 'ck';

		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		$this->assertSame( $value, $this->object_cache->get( $key ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_set_value_with_expiration() {
		$key = microtime();

		$value = 'ck';

		$this->assertTrue( $this->object_cache->set( $key, $value, 'default', 3600 ) );

		$this->assertSame( $value, $this->object_cache->get( $key ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_set_value_overwrites_previous() {
		$key = microtime();

		$value = 'ck';
		$new_value = 'abc';

		$this->assertTrue( $this->object_cache->set( $key, $value ) );

		$this->assertSame( $value, $this->object_cache->get( $key ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );

		$this->assertTrue( $this->object_cache->set( $key, $new_value ) );

		$this->assertSame( $new_value, $this->object_cache->get( $key ) );

		$this->assertSame( $new_value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_set_value_group() {
		$key = microtime();

		$value = 'ck';
		$group = 'hola';

		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
	}

	public function test_set_value_no_mc_group() {
		$key = microtime();

		$value = 'ck';
		$group = 'counts';

		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );

		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );

		$this->assertFalse( $this->object_cache->m->get( $this->object_cache->buildKey( $key, $group ) ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
	}

	public function test_set_by_key_value() {
		$key = microtime();

		$value = 'ck';

		$server_key = 'hbo';

		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );

		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_set_by_key_value_with_expiration() {
		$key = microtime();

		$value = 'ck';

		$server_key = 'hbo';

		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value, 'default', 3600 ) );

		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_set_by_key_value_overwrites_previous() {
		$key = microtime();

		$value = 'ck';
		$new_value = 'abc';

		$server_key = 'hbo';

		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value ) );

		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );

		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $new_value ) );

		$this->assertSame( $new_value, $this->object_cache->getByKey( $server_key, $key ) );

		$this->assertSame( $new_value, $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
	}

	public function test_set_by_key_value_group() {
		$key = microtime();

		$value = 'ck';
		$group = 'hola';

		$server_key = 'hbo';

		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value, $group ) );

		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
	}

	public function test_set_by_key_value_no_mc_group() {
		$key = microtime();

		$value = 'ck';
		$group = 'counts';

		$server_key = 'hbo';

		$this->assertTrue( $this->object_cache->setByKey( $server_key, $key, $value, $group ) );

		$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key, $group ) );

		$this->assertFalse( $this->object_cache->m->getByKey( $server_key, $this->object_cache->buildKey( $key, $group ) ) );

		$this->assertSame( $value, $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
	}

	public function test_set_multi_sets_values() {
		$items = array(
			microtime() . '-tester' => 'howdy',
			microtime() . '-yoyo' => 'What is cracking',
			microtime() . '-hidely-ho' => 'ouch'
		);

		$this->assertTrue( $this->object_cache->setMulti( $items ) );

		foreach ( $items as $key => $value )
			$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	public function test_set_multi_sets_values_with_different_groups() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'blackhawks'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'pengiuns'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$rekeyed[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value;
			else
				$rekeyed[ $this->object_cache->buildKey( $key ) ] = $value;
		}

		$items = array();
		foreach ( $keys as $key => $value )
			$items[$key] = $value['value'];

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$this->assertTrue( $this->object_cache->setMulti( $items, $groups ) );

		// Verify each value in memcached and internal cache
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) ) {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
				$this->assertSame( $value['value'], $this->object_cache->get( $key, $value['group'] ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
			} else {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
				$this->assertSame( $value['value'], $this->object_cache->get( $key ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
			}
		}
	}

	public function test_set_multi_sets_values_with_different_groups_including_no_mc_groups() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'comment'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'counts'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$rekeyed = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$rekeyed[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value;
			else
				$rekeyed[ $this->object_cache->buildKey( $key ) ] = $value;
		}

		$items = array();
		foreach ( $keys as $key => $value )
			$items[$key] = $value['value'];

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$this->assertTrue( $this->object_cache->setMulti( $items, $groups ) );

		// Verify each value in memcached and internal cache
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) ) {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
				$this->assertSame( $value['value'], $this->object_cache->get( $key, $value['group'] ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
			} else {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
				$this->assertSame( $value['value'], $this->object_cache->get( $key ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
			}
		}
	}

	public function test_set_multi_by_key_sets_values() {
		$items = array(
			microtime() . '-tester' => 'howdy',
			microtime() . '-yoyo' => 'What is cracking',
			microtime() . '-hidely-ho' => 'ouch'
		);

		$server_key = 'anger-management';

		$this->assertTrue( $this->object_cache->setMultiByKey( $server_key, $items ) );

		foreach ( $items as $key => $value )
			$this->assertSame( $value, $this->object_cache->getByKey( $server_key, $key ) );
	}

	public function test_set_multi_by_key_sets_values_with_different_groups() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'blackhawks'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'pengiuns'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$server_key = 'anger-management';

		$rekeyed = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$rekeyed[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value;
			else
				$rekeyed[ $this->object_cache->buildKey( $key ) ] = $value;
		}

		$items = array();
		foreach ( $keys as $key => $value )
			$items[$key] = $value['value'];

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$this->assertTrue( $this->object_cache->setMultiByKey( $server_key, $items, $groups ) );

		// Verify each value in memcached and internal cache
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) ) {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key, $value['group'] ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
			} else {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
			}
		}
	}

	public function test_set_multi_by_key_sets_values_with_different_groups_including_no_mc_groups() {
		$keys = array(
			microtime() . '-sharp' => array(
				'value' => 10,
				'group' => 'blackhawks'
			),
			microtime() . '-toews' => array(
				'value' => 'nineteen',
				'group' => 'comment'
			),
			microtime() . '-crosby' => array(
				'value' => '87',
				'group' => 'counts'
			),
			microtime() . '-suter' => array(
				'value' => 'twenty',
				'group' => 'default'
			),
			microtime() . '-bettman' => array(
				'value' => 'commish'
			)
		);

		$server_key = 'anger-management';

		$rekeyed = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$rekeyed[ $this->object_cache->buildKey( $key, $value['group'] ) ] = $value;
			else
				$rekeyed[ $this->object_cache->buildKey( $key ) ] = $value;
		}

		$items = array();
		foreach ( $keys as $key => $value )
			$items[$key] = $value['value'];

		$groups = array();
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) )
				$groups[] = $value['group'];
		}

		$this->assertTrue( $this->object_cache->setMultiByKey( $server_key, $items, $groups ) );

		// Verify each value in memcached and internal cache
		foreach ( $keys as $key => $value ) {
			if ( isset( $value['group'] ) ) {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key, $value['group'] ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key, $value['group'] ) ] );
			} else {
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
				$this->assertSame( $value['value'], $this->object_cache->getByKey( $server_key, $key ) );
				$this->assertSame( $value['value'], $this->object_cache->cache[ $this->object_cache->buildKey( $key ) ] );
			}
		}
	}

	public function test_set_option() {
		$value = 'widgets';

		$this->assertTrue( $this->object_cache->setOption( Memcached::OPT_PREFIX_KEY, $value ) );
		$this->assertSame( $value, $this->object_cache->getOption( Memcached::OPT_PREFIX_KEY ) );
	}

	public function test_switch_to_blog() {
		$key = 'oshie';
		$val = 'kovalchuk';
		$val2 = 'bobrovsky';

		if ( ! is_multisite() ) {
			// Single site ingnores switch_to_blog().
			$this->assertTrue( $this->object_cache->set( $key, $val ) );
			$this->assertEquals( $val, $this->object_cache->get( $key ) );
			$this->object_cache->switch_to_blog( 999 );
			$this->assertEquals( $val, $this->object_cache->get( $key ) );
			$this->assertTrue( $this->object_cache->set( $key, $val2 ) );
			$this->assertEquals( $val2, $this->object_cache->get( $key ) );
			$this->object_cache->switch_to_blog( get_current_blog_id() );
			$this->assertEquals( $val2, $this->object_cache->get( $key ) );
		} else {
			// Multisite should have separate per-blog caches
			$this->assertTrue( $this->object_cache->set( $key, $val ) );
			$this->assertEquals( $val, $this->object_cache->get( $key ) );
			$this->object_cache->switch_to_blog( 999 );
			$this->assertFalse( $this->object_cache->get( $key ) );
			$this->assertTrue( $this->object_cache->set( $key, $val2 ) );
			$this->assertEquals( $val2, $this->object_cache->get( $key ) );
			$this->object_cache->switch_to_blog( get_current_blog_id() );
			$this->assertEquals( $val, $this->object_cache->get( $key ) );
			$this->object_cache->switch_to_blog( 999 );
			$this->assertEquals( $val2, $this->object_cache->get( $key ) );
			$this->object_cache->switch_to_blog( get_current_blog_id() );
			$this->assertEquals( $val, $this->object_cache->get( $key ) );
		}

		// Global group
		$this->object_cache->add_global_groups( 'global-cache-test' );
		$this->assertTrue( $this->object_cache->set( $key, $val, 'global-cache-test' ) );
		$this->assertEquals( $val, $this->object_cache->get( $key, 'global-cache-test' ) );
		$this->object_cache->switch_to_blog( 999 );
		$this->assertEquals( $val, $this->object_cache->get( $key, 'global-cache-test' ) );
		$this->assertTrue( $this->object_cache->set( $key, $val2, 'global-cache-test' ) );
		$this->assertEquals( $val2, $this->object_cache->get( $key, 'global-cache-test' ) );
		$this->object_cache->switch_to_blog( get_current_blog_id() );
		$this->assertEquals( $val2, $this->object_cache->get( $key, 'global-cache-test' ) );
	}
}

function memcached_get_callback_true( $m, $key, &$value ) {
	$value = 'brodeur';
	return true;
}

function memcached_get_callback_false( $m, $key, &$value ) {
	$value = 'brodeur';
	return false;
}