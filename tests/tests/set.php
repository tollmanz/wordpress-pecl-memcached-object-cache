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
}