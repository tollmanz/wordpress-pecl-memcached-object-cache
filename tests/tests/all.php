<?php

class MemcachedUnitTestsAll extends MemcachedUnitTests {
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