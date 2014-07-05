<?php

class MemcachedUnitTestsGetDelayed extends MemcachedUnitTests {

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
}