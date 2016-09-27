<?php

class MemcachedUnitTestsAllOptions extends MemcachedUnitTests {
	public function test_getting_all_options_keys() {
		$this->object_cache->set( 'alloptions', array( 'siteurl' => 'http://examples.com/' ), 'options' );
		$keys = $this->object_cache->get( 'alloptionskeys', 'options' );

		$this->assertEquals( array( 'siteurl' => true ), $keys );
	}

	public function test_getting_all_options() {
		$this->object_cache->set( 'alloptions', array( 'siteurl' => 'http://examples.com/' ), 'options' );

		$options = $this->object_cache->get( 'alloptions', 'options' );
		$this->assertEquals( array( 'http://examples.com/' ), array_values( $options ) );
	}

	public function test_add_option_updates_alloptions_keys() {
		add_option( 'jordan', 'parise' );
		$this->object_cache->set( 'alloptions', array( 'siteurl' => 'http://examples.com/' ), 'options' );
		$keys = $this->object_cache->get( 'alloptionskeys', 'options' );

		$this->assertTrue( array_key_exists( 'jordan', $keys ) );
	}
}
