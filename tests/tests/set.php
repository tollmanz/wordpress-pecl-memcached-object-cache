<?php

class MemcachedUnitTestsSet extends MemcachedUnitTests {
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

	public function test_set_with_expiration_of_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$built_key = $this->object_cache->buildKey( $key, $group );

		// 30 days
		$expiration = 60 * 60 * 24 * 30;

		$this->assertTrue( $this->object_cache->set( $key, $value, $group, $expiration ) );

		// Verify that the value is in cache by accessing memcached directly
		$this->assertEquals( $value, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_set_with_expiration_longer_than_30_days() {
		$key = 'usa';
		$value = 'merica';
		$group = 'july';
		$built_key = $this->object_cache->buildKey( $key, $group );

		// 30 days and 1 second; if interpreted as timestamp, becomes "Sat, 31 Jan 1970 00:00:01 GMT"
		$expiration = 60 * 60 * 24 * 30 + 1;

		$this->assertTrue( $this->object_cache->set( $key, $value, $group, $expiration ) );

		// Verify that the value is in cache
		$this->assertEquals( $value, $this->object_cache->m->get( $built_key ) );

		// Remove the value from internal cache to force a lookup
		unset( $this->object_cache->cache[ $built_key ] );

		// Verify that the value is no longer in the internal cache
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Do the lookup with the API to verify that we get the value
		$this->assertEquals( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_set_multi_with_expiration_of_30_days() {
		$values = array(
			'header' => 'footer',
			'goal'   => 'save',
			'fast'   => 'slow',
		);

		$group = 'CRC';

		// 30 days
		$expiration = 60 * 60 * 24 * 30;

		$this->assertTrue( $this->object_cache->setMulti( $values, $group, $expiration ) );

		// Verify values independently
		foreach ( $values as $key => $value ) {
			$built_key = $this->object_cache->buildKey( $key, $group );

			// Verify that the value is in cache by accessing memcached directly
			$this->assertSame( $value, $this->object_cache->m->get( $built_key ) );

			// Remove the value from internal cache to force a lookup
			unset( $this->object_cache->cache[ $built_key ] );

			// Verify that the value is no longer in the internal cache
			$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

			// Do the lookup with the API to verify that we get the value
			$this->assertEquals( $value, $this->object_cache->get( $key, $group ) );
		}
	}

	public function test_set_multi_with_expiration_longer_than_30_days() {
		$values = array(
			'header' => 'footer',
			'goal'   => 'save',
			'fast'   => 'slow',
		);

		$group = 'CRC';

		// 30 days and 1 second; if interpreted as timestamp, becomes "Sat, 31 Jan 1970 00:00:01 GMT"
		$expiration = 60 * 60 * 24 * 30 + 1;

		$this->assertTrue( $this->object_cache->setMulti( $values, $group, $expiration ) );

		// Verify values independently
		foreach ( $values as $key => $value ) {
			$built_key = $this->object_cache->buildKey( $key, $group );

			// Verify that the value is in cache by accessing memcached directly
			$this->assertSame( $value, $this->object_cache->m->get( $built_key ) );

			// Remove the value from internal cache to force a lookup
			unset( $this->object_cache->cache[ $built_key ] );

			// Verify that the value is no longer in the internal cache
			$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

			// Do the lookup with the API to verify that we get the value
			$this->assertEquals( $value, $this->object_cache->get( $key, $group ) );
		}
	}
}