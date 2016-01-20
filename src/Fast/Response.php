<?php
/**
 * Fast - A PHP5.4+ API Micro Framework
 *
 * @author 		Kaleb Heitzman <kalebheitzman@gmail.com>
 * @copyright 2015 Kaleb Heitzman
 * @link 			https://github.com/kalebheitzman/fast
 * @license 	https://github.com/kalebheitzman/fast/blob/master/LICENSE
 * @version 	0.1.0
 * @package  	Fast
 *
 * See: https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
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

trait Response {

	/**
	 * @var array Routes
	 */
	static protected $response;

	/**
	 * @var integer HTTP Code
	 */
	static protected $httpCode;

	/**
	 *	Render a JSON response
	 *	@return	void Initial Response Setup
	 */
	static public function response()
	{
		// check for active benchmarking
		if (self::$config['benchmark']) {
			$execution = microtime(true)-self::$benchmark['start'];
			$execution = substr($execution, 0, 7);
			$benchmark['start'] = self::$benchmark['start'];
			$benchmark['end'] = microtime(true);
			$benchmark['execution_time'] = $execution.' seconds';

			self::$response['benchmark'] = $benchmark;
		}

		// render a json response
		self::sendResponse();
	}

	/**
	 * 404 Page
	 * @return void 404 JSON response
	 */
	static private function error404() {
		// set the response status
		self::$response['status'] = '404 not found';
		self::$response['error'] = 'route not found';
		// set the http status code
		self::$httpCode = 404;
		// render a json response
		self::sendResponse();
	}

	/**
	 * Set HTTP Status codes
	 * @return string HTTP status codes
	 */
	static private function setStatus() {
		// get the method
		$method = self::$method;

		// status codes

		// OPTION
		$status['OPTION']['status'] = 'OK';
		$status['OPTION']['code'] = 200;
		// GET
		$status['GET']['status'] = 'OK';
		$status['GET']['code'] = 200;
		// POST
		$status['POST']['status'] = 'Created';
		$status['POST']['code'] = 201;
		// PUT
		$status['PUT']['status'] = 'OK resource updated';
		$status['PUT']['code'] = 200;
		// DELETE
		$status['DELETE']['status'] = 'OK resource deleted';
		$status['DELETE']['code'] = 200;

		// get the response status
		$response_status = $status[$method]['code'] . " " . $status[$method]['status'];

		// send the response status
		self::$httpCode = $status[$method]['code'];
		self::$response['status'] = $response_status;
	}

	/**
	 * Send JSON Response to client
	 * @return void JSON response
	 */
	static private function sendResponse()
	{
		// get the http response code
		http_response_code( self::$httpCode );
		// set the content tyupe
		header('Content-Type: application/json');
		// send the response
		echo json_encode( self::$response );
		exit();
	}

}
