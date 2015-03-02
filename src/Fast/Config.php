<?php

// Setup the environment
$config['server_path'] = dirname(dirname(__FILE__));
$config['base_path'] = $_SERVER['REQUEST_URI'];
$config['environment'] = 'development';

// enable benchmark
$config['benchmark'] = false;

// database settings
$config['db'] = array();