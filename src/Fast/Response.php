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
	 * Set HTTP Status codes
	 * @return string HTTP status codes
	 */
	static private function setStatus( $code = 200, $message = null ) {
		// get the method
		$method = self::$method;

		// status codes
		$status[200] = array(
			'OPTION' => 'OK',
			'GET' => 'OK',
			'PUT' => 'OK resource updated',
			'DELETE' => 'OK resource deleted'
		);
		$status[201] = 'Created';
		$status[401] = 'Access denied';
		$status[404] = 'Page not found';

		// common error codes
		$error_codes = array( 401, 404, 500 );

		// send the http status code
		self::$httpCode = $code;
		// set the response status
		if ( $code == 200 ) {
			// get message
			$message = ( ! is_null( $message ) ) ? $message : $status[$code][$method];
			// set the http code
			self::$response['status']['code'] = $code;
			// set a helpful message
			self::$response['status']['message'] = $message;
		}
		else {
			// get defined message
			$message = ( ! is_null( $message ) ) ? $message : $status[$code];
			// set the http code
			self::$response['status']['status'] = $code;
			// set a helpful message
			if ( in_array( $code, $error_codes ) ) {
				self::$response['status']['error'] = $message;
			} else {
				self::$response['status']['message'] = $message;
			}
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
