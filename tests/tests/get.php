<?php

class MemcachedUnitTestsGet extends MemcachedUnitTests {
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
}

function memcached_get_callback_true( $m, $key, &$value ) {
	$value = 'brodeur';
	return true;
}

function memcached_get_callback_false( $m, $key, &$value ) {
	$value = 'brodeur';
	return false;
}