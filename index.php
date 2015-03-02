<?php

require 'core/fast.php';

/**
 * Initialize Fast
 */
Fast::init(array(
	'environment' => 'production',
	'default_layout' => 'default',
	'benchmark' => true
));

/**
 * Fast Routes
 */

Fast::get('/', function() {
	$data = array(
		'server' => 'fast api server',
		'version' => '0.0.1'
	);
	Fast::json($data);
});

Fast::get('entries', function() {
	echo "All Entries";
});

foreach (range(0, 500) as $number) {
	Fast::get('entry' . $number . '/:id', function($id) {
		echo $id;
	});
};

Fast::get('entry/:id', function($id) {
	echo "Entry with id " . $id;
});

Fast::post('entry', function() {
	echo "Inserting a new entry";
});

Fast::put('entry/:id', function($id) {
	echo "Updating an existing entry";
});

Fast::delete('entry/:id', function($id) {
	echo "Deleting an entry";
});

Fast::options('entries', function() {
	echo "Getting the HTTP OPTIONS VERB about entries";
});

Fast::get('article', 'authenticate', function() {
	echo "Authenticate before seeing articles";
});

Fast::get('feed', 'listen', function() {
	echo "Registering a listening agent";
});

Fast::before('authenticate', function() {
	echo "Authenticating";
});

Fast::after('listen', function() {
	echo "listen";
});

/**
 * Run Fast
 */
Fast::run();