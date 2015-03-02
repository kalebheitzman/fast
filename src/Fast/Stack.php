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

trait Stack {
	
	/**
	 * @var array Stack
	 */
	static protected $stack;

	/**
	 *	Build the actions list 
	 */
	static private function buildActions($position, $cb, $args = array())
	{
		// build the action
		$action = array();
		$action['position'] = $position;
		$action['cb'] = $cb;
		$action['args'] = $args;
		// push the action onto actions
		array_push(self::$stack, $action);
	}

	/**
	 *	Run the actions list 
	 */
	static private function runActions() {
		// sort the actions by position
		usort(self::$stack, function($a1, $a2) {
			return $a1['position'] - $a2['position'];
		});
		// run each action in the stack
		foreach(self::$stack as $action) {
			call_user_func_array($action['cb'], $action['args']);
		}

		self::response();
	}
	
}