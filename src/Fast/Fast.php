<?php
/**
 * Fast - A PHP5.4+ API Micro Framework
 *
 * @author 		Kaleb Heitzman <kalebheitzman@gmail.com>
 * @copyright 	2015 Kaleb Heitzman
 * @link 		https://github.com/kalebheitzman/fast
 * @license 	https://github.com/kalebheitzman/fast/blob/master/LICENSE
 * @version 	0.1.0
 * @package  	Fast
 *
 * MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Fast;

/**
 * Fast
 *
 * Fast is an API Framework with a RESTful HTTP router.
 * 
 * @package Fast
 * @author  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @since  0.1.0
 */

require 'Middleware.php';
require 'Response.php';
require 'Router.php';
require 'Stack.php';

class Fast {

	/**
	 * @const string The version of Fast 
	 */
	const VERSION = '0.1.0';

	/**
	 * @var array Benchmarking
	 */
	static protected $benchmark;

	/**
	 * @var Array Configuration
	 */
	static protected $config;

	/**
	 *	Middleware trait 
	 */
	use Middleware;

	/**
	 * Mongo trait
	 */
	use Mongo;

	/**
	 *	Response trait 
	 */
	use Response;

	/**
	 *	Router trait 
	 */
	use Router;

	/**
	 * Stack trait
	 */
	use Stack;

	/**
	 * Getters trait
	 */
	use Getters;

	/**
	 * Setters trait
	 */
	use Setters;

	/**
	 *	Initialize Fast
	 */
	static public function init($appConfig = array())
	{
		// var_dump(self::$config);
		// Benchmarking
		self::$benchmark = array(); 
		self::$benchmark['start'] = microtime(true);
		// initialize the stack
		self::$stack = array();
		// load $defaultSettings
		require 'Config.php';
		// Configuration
		self::$config = array_merge($config, $appConfig);
		// set server information
		if (self::$config['server_info']) {
			self::setServerInfo();
		}
		// initialize the db
		self::mongoInit();
	}

	/**
	 *	Go Run Fast!
	 *
	 *	Finds a route to display and runs view and model logic based on the
	 *	route pattern. The view engine and model engine must be declared for
	 *	run() to work.
	 */
	static public function run()
	{
		// Find a matching route or buzz out.
		self::findRoute();
		// execute the closure
		self::runRoute();
	}

} /* EOF Fast.php */