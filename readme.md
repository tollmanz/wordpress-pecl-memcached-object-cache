[![Build Status](https://travis-ci.org/tollmanz/wordpress-memcached-backend.png?branch=master)](https://travis-ci.org/tollmanz/wordpress-memcached-backend)

## Overview

This project is a WordPress object cache backend that implements all available methods in the [Memcached PECL extension](http://www.php.net/manual/en/class.memcached.php). For a detailed account of how this differs from a Memcache PECL backend (note the inclusion/exclusion of the "d"), read the [article I wrote on the topic](http://tollmanz.com/wordpress-memcached-object-cache/).

## Installation

1. Install the Memcached daemon. Memcached should be available via your favorite package manager in your Linux distribution of choice. 

	For Ubuntu and Debian:

	```bash
apt-get install memcached
	```
	For CentOS:

	```bash
yum install memcached
	```

	Note that you will likely want to review the Memcached configuration [directives](http://serverfault.com/questions/347621/memcache-basic-configuration). To get the best results from Memcached, you will need to configure it for your system and use case.

1. Start the Memcached daemon:

	```bash
service memcached restart
	```

1. Verify that Memcached is installed and running. 

	1. From your server, `telnet` into the Memcached server

		```bash
telnet localhost 11211
		```
		
		You should see output like:

		```bash
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
		```

	1. Type `version` into the Telnet prompt. If Memcached is installed and running, you should see something like:

		```bash
VERSION 1.4.14 (Ubuntu)
		```

	1. Exit Telnet by typing `ctrl` + `]`, hitting `enter`, then typing `quit` and pressing `enter`.

1. Install the Memcached PECL extension on your server. Note that there are two different PHP interfaces for Memcached; one is named PECL Memcache and the other, PECL Memcached. The "d" at the end of Memcached is extremely important in this case. You should be able to install PECL Memcached from your package manager for your Linux distro. 

	For Ubuntu and Debian:

	```bash
apt-get install php5-memcached
	```

	For CentOS:

	```bash
yum install php-pecl-memcached
	```

	Note that if you have a more custom installation of PHP, you might need to take some extra steps to link the PECL Memcached extension to PHP. If you are setting this up using your package manager's version of PHP and PECL Memcached, this should not be necessary. For example, many `yum.conf` files will exclude packages that begin with `php`. You may be able to modify the configuration file as necessary to install this package.

1. Verify that the Memcached daemon is running and accessible via the PECL Memcached extension in PHP. The easiest way to test this is to run a simple PHP script that connects to Memcached and tries to set and retrieve a value.

	1. Enter PHP's interactive shell:

		```bash
php -a
		```

	1. Type the following in the interactive shell:

		```bash
php > $m = new Memcached();
php > $m->addServer( '127.0.0.1', 11211 );
php > $m->set( 'foo', 100 );
php > echo $m->get( 'foo' ) . "\n";
		```

	1. If that last command returns `100`, everything is working! If you get blank output, something is amiss.

		Note that if your PHP configuration does not support the interactive shell, you can use the code above to create a script to execute in the browser. The interactive shell is easier for the verification step as it does not require a functioning web server.

1. Now that the server dependencies are resolved, the rest of the configuration is in the WordPress application. Take the **object-cache.php** file and place it in your **wp-content** folder. For instance, if the root of your WordPress installation is at **/srv/www/wordpress**, the location of **object-cache.php** would be **/srv/www/wordpress/wp-content/object-cache.php**. Please note that **object-cache.php** is a [WordPress drop-in](http://hakre.wordpress.com/2010/05/01/must-use-and-drop-ins-plugins/). It is not a regular plugin or an MU plugin. Drop-ins are located directly in the **wp-content** folder.

1. Add the following to your **wp-config.php** file:

	```php
	global $memcached_servers;
	$memcached_servers = array(
	    array(
	        '127.0.0.1', // Memcached server IP address
	        11211        // Memcached server port
	    )
	);
	```

	If your Memcached server is located on a different server or port, adjust those values as needed. If you have multiple Memcached instances, add additional servers to the array:

	```php
	global $memcached_servers;
	$memcached_servers = array(
	    array(
	        '1.2.3.4',
	        11211
	    ),
	    array(
	        '1.2.3.5',
	        11211
	    )
	);
	```

1. To test the WordPress object cache setup, add the following code as an MU plugin:

	```php
	<?php
	$key   = 'dummy';
	$value = '100';

	$dummy_value = wp_cache_get( $key );

	if ( $value !== $dummy_value ) {
		echo "The dummy value is not in cache. Adding the value now.";
		wp_cache_set( $key, $value );
	} else {
		echo "Value is " . $dummy_value . ". The WordPress Memcached Backend is working!";
	}
	```

	After adding the code, reload your WordPress site twice. On the second load, you should see a success message printed at the top of the page. Remove the MU plugin after you've verified the setup.

## Authors

* Zack Tollman
* 10up
