<?php
/**
 * Fast - A PHP5 API Micro Framework
 *
 * @author 		Kaleb Heitzman <kalebheitzman@gmail.com>
 * @copyright 	2013 Kaleb Heitzman
 * @link 		https://github.com/kalebheitzman/fastphp
 * @license 	https://github.com/kalebheitzman/fastphp/blob/master/LICENSE
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
	 * @var array Configuration
	 */
	static protected $config;

	/**
	 * @var mixed Database and Model System
	 */
	static protected $modelEngine;

	/**
	 * @var array Processed routes
	 */
	static protected $routes;

	/**
	 * @var array Current processed route
	 */	
	static protected $route;

	/**
	 * @var array Middleware
	 */	
	static protected $middleware;

	/**
	 * @var array Actions
	 */
	static protected $actions;

	/**
	 * @var array Response  
	 */
	static protected $response;

	/**
	 * GET Request
	 */
	static public function get()
	{
		$args = func_get_args();
		self::mapRoute($args, "GET");
	}
	
	/**
	 * POST Request
	 */
	static public function post()
	{
		$args = func_get_args();
		self::mapRoute($args, "POST");
	}
	
	/**
	 * PUT Request
	 */
	static public function put()
	{
		$args = func_get_args();
		self::mapRoute($args, "PUT");
	}
	
	/**
	 * DELETE Request
	 */
	static public function delete()
	{
		$args = func_get_args();
		self::mapRoute($args, "DELETE");
	}

	/**
	 * OPTIONS Request
	 */
	static public function options()
	{
		$args = func_get_args();
		self::mapRoute($args, "OPTIONS");
	}

	/**
	 *	Initialize Fast
	 */
	static public function init($appConfig = array())
	{
		// Benchmarking
		self::$benchmark = array(); 
		self::$benchmark['start'] = microtime(true);
		// set actions to a blank array
		self::$actions = array();
		// set response to a blank array
		self::$response = array();
		// load $defaultSettings
		require 'config.php';
		// Configuration
		self::$config = array_merge($config, $appConfig);
	}

	/**
	 *	Initialize the ModelEngine in Fast
	 */
	static public function modelEngine($engine = null)
	{
		if (is_null($engine)) 
			die('Woah! I need some footware. Define a model engine through Fast::modelEngine($engine).');
		else
			self::$modelEngine = $engine;
	}

	/**
	 *	The Database 
	 */
	static public function db()
	{
		return self::$modelEngine;
	}

	/**
	 *	Render a JSON response  
	 */
	static public function response()
	{
		// check for active benchmarking
		if (self::$config['benchmark']) {
			$execution = microtime(true)-self::$benchmark['start'];
			$execution = substr($execution, 0, 7);
			self::$response['benchmark'] = $execution;
		}
		// render a json response
		header('Content-Type: application/json');
		echo json_encode(self::$response);
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

	static private function runMiddleware($position = null)
	{
		if (is_null($position)) return false;

		foreach(self::$route['middleware'] as $middleware) {

			$cb = self::$middleware[$middleware]['cb'];
			$position = self::$middleware[$middleware]['position'];
			
			// set the position
			if ($position == 'before') $position = -1;
			if ($position == 'after') $position = 1;

			self::buildActions($position, $cb);
		}
	}

	/**
	 *	Run the route
	 */
	static private function runRoute()
	{
		// execute any before middleware
		self::runMiddleware('before');
		// execute the route
		self::buildActions(0, self::$route['cb'], self::$route['params']);
		// run actions
		self::runActions();
	}

	/**
	 *	Set the data 
	 */
	static public function setData($data = null)
	{
		foreach ($data as $key => $value) {
			self::$response[$key] = $value;
		}
	}

	/**
	 *	Build the actions list 
	 */
	static private function buildActions($position, $cb, $args = null)
	{
		// build the action
		$action = array();
		$action['position'] = $position;
		$action['cb'] = $cb;
		$action['args'] = is_null($args) ? array() : $args;
		// push the action onto actions
		array_push(self::$actions, $action);
	}

	/**
	 *	Run the actions list 
	 */
	static private function runActions() {
		// sort the actions by position
		usort(self::$actions, function($a1, $a2) {
			return $a1['position'] - $a2['position'];
		});
		// run each action in the stack
		foreach(self::$actions as $action) {
			call_user_func_array($action['cb'], $action['args']);
		}

		self::response();
	}

	/**
	 *	Middleware
	 */
	static public function middleware($name, $cb, $position)
	{
		self::$middleware[$name]['cb'] = $cb;
		self::$middleware[$name]['position'] = $position;
	}

	/**
	 * Map route
	 */
	static private function mapRoute($args = array(), $method = null)
	{
		// the pattern to test against
		$pattern = array_shift($args);
		// the callable
	    $callback = array_pop($args);
	    // the filterrs
	    $middleware = $args;
	    // add the route to the routes var
	    self::$routes[$method][$pattern] = array(
	    	"method" => $method,
	    	"callback" => $callback, 
	    	"middleware" => $middleware
	    );
	}

	/**
	 *	Find a Route
	 */
	static private function findRoute($url = array())
	{
		// Get the route called
		$pattern = isset($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], "/") : "/";
		// Get the method
		$method = $_SERVER['REQUEST_METHOD'];
		// Setup the URL
		$url['original'] = $pattern;
		$url['path'] = explode('/', parse_url($pattern, PHP_URL_PATH));
		$url['length'] = count($url['path']);
		// Parse the patterns
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
			self::$route['middleware'] = $data['middleware'];
			self::$route['cb'] = $data['callback'];
			self::$route['params'] = $data['parameters']; 
			// return the data
			return true;
		}
		// no matches were found
		return self::error404();
	}

	static private function error404() {
		$data = array();
		$data['error'] = '404 Page not found';
		// render a json response
		return self::json($data);
	}

} /* EOF Fast.php */