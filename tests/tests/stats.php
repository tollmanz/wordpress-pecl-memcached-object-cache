<?php

class MemcachedUnitTestsStats extends MemcachedUnitTests {
	public function test_for_stats_returning_stats() {
		$this->assertSame( array_keys( pmem_get_stats() ), array_keys( wp_cache_get_stats() ) );
	}

	public function test_for_individual_stats_returns_correct_value() {
		$stats = wp_cache_get_stats();
		$version = $stats['127.0.0.1:11211']['version'];
		$this->assertSame( array( array( '127.0.0.1:11211', $version ) ), pmem_get_stat( 'version', '127.0.0.1:11211' ) );
	}
}