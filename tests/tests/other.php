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

	public function test_sanitize_expiration_leaves_value_untouched_if_less_than_thirty_days() {
		$time = 5;
		$this->assertEquals( $time, $this->object_cache->sanitize_expiration( $time ) );
	}

	public function test_sanitize_expiration_leaves_value_untouched_if_exactly_thirty_days() {
		$time = 60 * 60 * 24 * 30;
		$this->assertEquals( $time, $this->object_cache->sanitize_expiration( $time ) );
	}

	public function test_sanitize_expiration_should_adjust_expiration_if_later_than_now() {
		$time = 60 * 60 * 24 * 31;
		$now = time();

		// We need to manually set the internal timer to make sure we get the right value in testing
		$this->object_cache->now = $now;

		$this->assertEquals( $time + $now, $this->object_cache->sanitize_expiration( $time ) );
	}
}