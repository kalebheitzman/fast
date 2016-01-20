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

trait Token {

  /**
   * JWT Secret Key
   * @var string
   */
  static protected $key;

  /**
   * Initialize Token Class
   */
  static public function tokenInit()
  {
    self::$key = self::$config['jwt']['key'];
  }

  /**
   * Create a JSON Web Token
   * @return string JWT
   */
	static public function encodeToken()
	{
    // check for email
    if ( ! isset($_GET['email']) ) {
      self::response( 400, 'Email not provided in request' );
    }
    // create/get user based on email address
    $email = $_GET['email'];
    $user_id = self::upsertUserByEmail( $email );
    // get the time
    $time = time();
    // create the token to encode
    $token = array(
      "iat" => $time, // issued at
      // "jti" => null,
      "iss" => self::$config['server']['path'], // issuer
      "exp" => $time + self::$config['jwt']['time_valid'], // expires
      "sub" => $user_id // subject
    );
    // encode the token
    $jwt = \JWT::encode( $token, self::$key );
    // return the data
    $data['token'] = $jwt;
    return $data;
	}

  /**
   * Decode token
   * @return array Decoded JWT
   */
  static public function decodeToken()
  {
    // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJGYXN0IEFQSSBTZXJ2ZXIiLCJpYXQiOjE0NTMyODIxMDJ9.zVkjnss29YLfpALFBCC93tqDqwvzFQ2kgnh7UhRkWS4
    $token = isset( $_GET['token'] ) ? $_GET['token'] : null;
    if ( $token == null ) {
      // access denied
  		self::response( 401, 'You must provide a token.' );
    }
    try {
      // get the decoded token
      $decoded = \JWT::decode( $token , self::$key );
      return $decoded;
    }
    catch ( \Exception $e ) {
      self::response( 401, 'token: ' . $e->getMessage() );
    }
  }

}
