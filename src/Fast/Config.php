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

// Server information
$config['server']['name'] = 'Fast API Server';
$config['server']['description'] = 'Provides JSON data responses.';
$config['server']['version'] = '1.0';
$config['server_info'] = true;

// Setup the environment
$config['server_path'] = dirname(dirname(__FILE__));
$config['base_path'] = $_SERVER['REQUEST_URI'];
$config['environment'] = 'development';

// enable benchmark
$config['benchmark'] = false;

// default route position
$config['route_position'] = 20;

// database settings
$config['mongo']['host'] = 'localhost';
$config['mongo']['port'] = 27017;
$config['mongo']['name'] = 'fast';

// jwt key
$config['jwt_key'] = null;
