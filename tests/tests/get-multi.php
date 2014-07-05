<?php

class MemcachedUnitTestsGetMulti extends MemcachedUnitTests {

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
}