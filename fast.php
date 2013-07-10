<?php
/**
 * Fast - A PHP 5.3+ Router Micro Framework
 *
 * @author 		Kaleb Heitzman <jkheitzman@gmail.com>
 * @copyright 	2013 Kaleb Heitzman
 * @link 		https://github.com/kalebheitzman/fastphp
 * @license 	http://kheitzman.com/fast/license
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

// namespace Fast;

/**
 * Fast
 *
 * The Fast class handles various routing options. It will handle all 4 http
 * verb requests, GET, POST, PUT, DELETE.
 *
 * The Fast class also handles before and after filters using FAST::before()
 * and FAST::after()
 * 
 * @package Fast
 * @author  Kaleb Hheitzman <kaleblex@gmail.com>
 * @since  0.1.0
 */
class Fast {

	/**
	 * @const string The version of Fast 
	 */
	const VERSION = '0.1.0';

	/**
	 * @var array Configuration
	 */
	static protected $config;

	/**
	 * @var array Processed routes
	 */
	static protected $routes;

	/**
	 * @var array Processed filters
	 */
	static protected $filters;

	/**
	 * @var array HTTP Request
	 */
	static protected $request;

	/**
	 * @var array HTTP Response
	 */
	static protected $response;

	/**
	 * @var array Benchmarking
	 */
	static protected $benchmark;

	/**
	 * Constructor
	 */
	private function __construct()
	{

	}

	/**
	 * Initialize Fast
	 *
	 * Intialization sets up routes and filters arrays that an 
	 * end user can add routes to by using syntax like 
	 * Fast::get('entry/:id', 'filter', function($id) {});
	 * @since 0.1.0
	 */
	static public function init($config = array())
	{
		// Benchmarking
		self::$benchmark = array();
		self::$benchmark['start'] = microtime(true);

		// declare some default settings true of every app
		$defaultSettings = array(
			'server_path' => dirname(dirname(__FILE__)),
			'base_path' => $_SERVER['REQUEST_URI'],
			'environment' => 'production',
			'default_layout' => 'default',

			// parsers
			'html_parser' => false,
			'css_parser' => false,
			'js_parser' => false,

			// locations
			'styles' => 'styles',
			'scripts' => 'scripts',
			'images' => 'images',

			// other
			'benchmark' => false,
		);

		// Configuration
		self::$config = array_merge($defaultSettings, $config);

		var_dump(self::$config);
		// build the route map
		self::$routes = array();
		// build the filter map
		self::$filters = array();
	}

	/**
	 * GET Request
	 */
	static public function get()
	{
		$args = func_get_args();
		return self::mapRoute($args, "GET");
	}
	
	/**
	 * POST Request
	 */
	static public function post()
	{
		$args = func_get_args();
		return self::mapRoute($args, "POST");
	}
	
	/**
	 * PUT Request
	 */
	static public function put()
	{
		$args = func_get_args();
		return self::mapRoute($args, "PUT");
	}
	
	/**
	 * DELETE Request
	 */
	static public function delete()
	{
		$args = func_get_args();
		return self::mapRoute($args, "DELETE");
	}

	/**
	 * OPTIONS Request
	 */
	static public function options()
	{
		$args = func_get_args();
		return self::mapRoute($args, "OPTIONS");
	}

	/**
	 * BEFORE Filter
	 */
	static public function before()
	{
		$args = func_get_args();
		return self::mapFilter($args, "before");
	}

	/**
	 * AFTER Filter
	 */
	static public function after()
	{
		$args = func_get_args();
		return self::mapFilter($args, "after");
	}

	/**
	 * Fast Router
	 *
	 * Determines correct route to use. If none are found it returns 404.
	 */
	static private function router($url = null, $method = null)
	{
		/**
		 * Setup the URL
		 */
		$url = array(
			'original' => $url,
			'path' => explode('/', parse_url($url, PHP_URL_PATH))
		);
		$url['length'] = count($url['path']);

		/**
		 * Setup the patterns
		 */
		foreach (self::$routes[$method] as $pattern => $data) {

			$parameters = array();

			// get pattern info
			$pattern = array (
				'original' => $pattern,
				'path' => explode('/', $pattern)
			);
			$pattern['length'] = count($pattern['path']);

			// this pattern is irrelevant 
			if ($url['length'] <> $pattern['length']) {
				continue;
			}

			// pattern matching
			foreach($pattern['path'] as $i => $key) 
			{
				if (strpos($key, ':') === 0)
				{
					$parameters[substr($key, 1)] = $url['path'][$i];
				}
				// this filter is irrelevant
				else if($key != $url['path'][$i])
				{
					continue 2;
				}
			}

			// check for parameters key		
			if ( ! array_key_exists('parameters', $data))
			{
				$data['parameters'] = array();
			}
			// add the parameters
			$data['parameters'] = array_merge($data['parameters'], $parameters);
			// return the data
			return $data;
		}
		// no matches were found
		return false;
	}

	/**
	 * Check for an existing filter
	 */
	static private function filter($name = null) {
		if (array_key_exists($name, self::$filters)) {
			return self::$filters[$name];
		}
		return false;
	}

	/**
	 * Map route
	 */
	static private function mapRoute($args = array(), $method = null)
	{
		// the pattern to test against
		$pattern = array_shift($args);
		// the callable afterwards
    $callback = array_pop($args);
    // filter
    $filter = array_shift($args);
    // add the route to the routes var
    self::$routes[$method][$pattern] = array(
    	"method" => $method, 
    	"callback" => $callback, 
    	"filter" => $filter
    );
	}

	/**
	 * Map filter
	 */
	static private function mapFilter($args = array(), $position = null) {
		// get the name
		$filter = array_shift($args);
		// get the callable afterwards
		$callback = array_pop($args);
		// register the filter
		self::$filters[$filter] = array(
			'callback' => $callback,
			'position' => $position
		);
	}

	/**
	 * Run Fast
	 *
	 * This will find the route called and apply pattern and filter matching. If
	 * a valid route is found, we'll hand off the request to the associated
	 * callable with the correct HTTP Headers and info.
	 */
	static public function run()
	{
		// Get the route called
		$pattern = isset($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], "/") : "/";
		// Get the method
		$method = $_SERVER['REQUEST_METHOD'];
		// Get the route and its data
		$route = self::router($pattern, $method);
		// check for a 404
		if ($route == false) {
			// trigger a 404 error

		}

		// Set the default filter
		$filter = null;
		
		// check for filters
		if ( ! is_null($route['filter'])) {
			$filter = self::filter($route['filter']);
		}
		
		// Before filter
		if ($filter['position'] == "before") {
			call_user_func($filter['callback']);
		}

		// The route callback
		call_user_func_array($route['callback'], $route['parameters']);
		
		// After filter
		if ($filter['position'] == "after") {
			call_user_func($filter['callback']);
		}

		// check for active benchmarking
		if (self::$config['benchmark']) {
				$execution = microtime(true)-self::$benchmark['start'];
				$execution = substr($execution, 0, 7);
				echo "<p><code> Script executed in " . $execution . " seconds</code></p>";
		}
	}

	/**
	 * Fast Renderer
	 *
	 * Renders basic php views
	 */
	static public function render($view = null, $data = array())
	{
		// get the viewFile
		$viewFile = self::$config['server_path'] . '/views/' . $view . '.php';
		// get the template
		$template = self::$config['server_path'] . '/views/templates/' . self::$config['default_layout'] . '.php'; 

		// check for the view
		if (is_null($view) || ! file_exists($viewFile)) {
			die($view . ' view not found.');
		}

		// get the content var
		$content = file_get_contents($viewFile);
		// check for content
		if ( ! $content) {
			die('Something went wrong.');
		}

		ob_start();
			require_once($template);
			$rendered = ob_get_contents();
		ob_end_clean();

		echo $rendered;
	}


}