## Overview

A WordPress object cache backend that implements all available methods in the Memcached PECL extension (http://www.php.net/manual/en/class.memcached.php)

## Authors 

* Zack Tollman
* 10up

## Installation
1. Make sure you have installed libmemcached and PECL/memcached on your server. Note that you need to have installed PECL/memcached (note the "d"), not simply PECL/memcache (no "d").
2. Add object-cache.php to the wp-content directory. It is a drop-in file, not a plugin, so it belongs in the wp-content directory, not the plugins directory.
3. By default, the script will connect to memcached at 127.0.0.1:11211. If you want to define custom memcached locations, add the following to wp-config.php and note that this array is different than the one you define to use the original WP Memcached Object Cache:

```php
global $memcached_servers;
$memcached_servers = array(
    array(
        '127.0.0.1',
        11211
    ),
    array(
        'domain.com',
        11211
    )
);```
