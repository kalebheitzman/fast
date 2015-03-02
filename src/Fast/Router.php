<?php
/**
 * Fast - A PHP5 API Micro Framework
 *
 * @author 		Kaleb Heitzman <kalebheitzman@gmail.com>
 * @copyright 	2015 Kaleb Heitzman
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

trait Router {
	
	/**
	 * @var array Routes
	 */
	static protected $routes;

	/**
	 * @var array Route
	 */
	static protected $route;

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
}