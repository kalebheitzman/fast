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
	 * @var integer HTTP Code
	 */
	static protected $httpCode;

	/**
	 * @var array Response
	 */
	static protected $response;

	/**
	 * Initialize the Response
	 * @return void
	 */
	static public function responseInit()
	{
		self::$engine->response = array();
	}

	/**
	 * Set HTTP Status codes
	 * @return string HTTP status codes
	 */
	static private function setStatus( $code = 200, $message = null ) {
		// get the method
		$method = self::$engine->request['method'];

		// status codes
		$status[200] = 'OK';											// everything is working
		$status[201] = 'Created';									// new resource has been created
		$status[204] = 'No Content';							// the resource was successfully deleted
		$status[304] = 'Not Modified';						// the client can use cached data
		$status[400] = 'Bad Request';							// request invalid, exact error should be explained
		$status[401] = 'Unauthorized';						// requires user authentication
		$status[403] = 'Forbidden';								// understood request, server refuses access
		$status[404] = 'Not Found';								// no resource behind the URI
		$status[422] = 'Unprocessable Entity';		// image cannot be formatted, mandatory fields missing
		$status[500] = 'Internal Sever Error';		// api developers should avoid this error. Stacktrace should be logged and not returned as response
		$status[503] = 'Service Unavailable';			// server is unable to handle the request

		// common error codes
		$error_codes = array( 401, 404, 500, 503 );

		// send the http status code
		self::$engine->request['httpCode'] = $code;
		// get defined message
		$message = ( ! is_null( $message ) ) ? $message : $status[$code];
		// set the http code
		self::$engine->response['status']['code'] = $code;
		// set a helpful message
		if ( in_array( $code, $error_codes ) ) {
			self::$engine->response['status']['error'] = $message;
		} else {
			self::$engine->response['status']['message'] = $message;
		}

	}

	/**
	 *	Render a JSON response
	 *	@return	void Initial Response Setup
	 */
	static public function response( $code = 200, $message = null )
	{
		// set the status code
		self::setStatus( $code, $message );

		// check for active benchmarking
		if (self::$config['benchmark']) {
			$execution = microtime(true)-self::$engine->benchmark['start'];
			$execution = substr($execution, 0, 7);
			$benchmark['start'] = self::$engine->benchmark['start'];
			$benchmark['end'] = microtime(true);
			$benchmark['execution_time'] = $execution.' seconds';

			self::$engine->response['benchmark'] = $benchmark;
			self::$engine->benchmark = $benchmark;
		}

		// render a json response
		self::sendResponse();
	}

	/**
	 * Send JSON Response to client
	 * @return void JSON response
	 */
	static private function sendResponse()
	{
		// get the http response code
		http_response_code( self::$engine->request['httpCode'] );
		// set the content tyupe
		header('Content-Type: application/json');
		// send the response
		echo json_encode( self::$engine->response );
		exit();
	}

}
