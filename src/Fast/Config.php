<?php

// Server information
$config['server_name'] = 'fast api server';
$config['server_description'] = 'provides fast api based json responses';
$config['server_version'] = '0.1.0';
$config['server_info'] = true;

// Setup the environment
$config['server_path'] = dirname(dirname(__FILE__));
$config['base_path'] = $_SERVER['REQUEST_URI'];
$config['environment'] = 'development';

// enable benchmark
$config['benchmark'] = false;

// database settings
$config['db'] = array();