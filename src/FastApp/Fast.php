<?php
/**
 * Fast - A PHP5 View-Model Micro Framework
 *
 * @author 		Kaleb Heitzman <jkheitzman@gmail.com>
 * @copyright 	2013 Kaleb Heitzman
 * @link 		https://github.com/kalebheitzman/fastphp
 * @license 	https://github.com/kalebheitzman/fastphp/blob/master/LICENSE
 * @version 	0.1.0
 * @package  	FastPHP
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

namespace FastApp;

/**
 * FastApp
 *
 * FastApp is a View-Model Framework with a RESTful HTTP router.
 * 
 * @package Fast
 * @author  Kaleb Heitzman <jkheitzman@gmail.com>
 * @since  0.1.0
 */
class Fast {

	static protected $viewEngine;

	static protected $modelEngine;

	static protected $routes;

	static protected $route;

	static protected $middleware;

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
	 *	Initialize the ViewEngine in Fast
	 */
	static public function viewEngine($engine = null)
	{
		if (is_null($engine)) 
			die('Woah! I need some eyeware. Define a view engine through Fast::viewEngine($engine).');
	}

	/**
	 *	Initialize the ModelEngine in Fast
	 */
	static public function modelEngine($engine = null)
	{
		if (is_null($engine)) 
			die('Woah! I need some footware. Define a model engine through Fast::modelEngine($engine).');
	}

	/**
	 *	Render a template using the viewEngine
	 */
	static public function render($view, $data = null)
	{
		echo "Things are working :)";
		//return self::$viewEngine->render($view, $data);
	}

	/**
	 *	The Database 
	 */
	static public function db()
	{
		return self::$modelEngine;
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
		// execute any before filters
		self::runMiddleware('before');
		// execute the closure
		self::runRoute();
		// execute after filters
		self::runMiddleware('after');
	}

	static private function runMiddleware($position = null)
	{
		if (is_null($position)) return false;
		foreach(self::$route['filters'] as $filter) {
			if (array_key_exists($filter, self::$middleware[$position])) {
				$callback = self::$middleware[$position][$filter];
				call_user_func($callback);
			}
		}
	}

	static private function runRoute()
	{
		call_user_func_array(self::$route['cb'], self::$route['params']);
	}

	/**
	 *	Middleware
	 */
	static public function middleware($name, $cb, $position)
	{
		self::$middleware[$position][$name] = $cb;
	}

	/**
	 * Map route
	 */
	static private function mapRoute($args = array(), $method = null)
	{
		// the pattern to test against
		$pattern = array_shift($args);
		// the callable
	    $callback = array_shift($args);
	    // the filterrs
	    $filter = array_shift($args);
	    // add the route to the routes var
	    self::$routes[$method][$pattern] = array(
	    	"method" => $method, 
	    	"callback" => $callback, 
	    	"filters" => $filter
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
			self::$route['filters'] = $data['filters'];
			self::$route['cb'] = $data['callback'];
			self::$route['params'] = $data['parameters']; 
			// return the data
			return true;
		}
		// no matches were found
		return self::error404();
	}

} /* EOF Fast.php */