<?php

class MemcachedUnitTestsFetch extends MemcachedUnitTests {
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
}