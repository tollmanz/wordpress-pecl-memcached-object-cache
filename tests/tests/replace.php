<?php

class MemcachedUnitTestsReplace extends MemcachedUnitTests {
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
}