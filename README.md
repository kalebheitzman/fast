# PHP 5.4 API Micro Framework

Fast is a PHP 5.4+ API Micro Framework for building APIs. Use the Fast::get(), Fast::post(), etc methods to build your api. Use your own databse libraries of choice and etc. We get you to the party and then it's your job to shine.

## Installation

Fast is available via a composer package. The first thing you should do is install composer if you haven't already.

Drop the following into your composer.json file and then run composer install from the command line.

	"require": {
		"kheitzman/fast": "dev-master"
	}

## Usage

Assuming that you're using index.php and an .htaccess file to route requests, add this to the top of your index file.

	require 'vendor/autoload.php';
	use FastPHP\Fast as Fast;

This will use composers autoload feature and alias Fast as Fast for easier use.

## GET Examples

	Fast::get('/', function() {
		Fast::render('index');
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

## POST Examples

	Fast::post('entry', function() {
		echo "Inserting a new entry";
	});

## PUT Examples

	Fast::put('entry/:id', function($id) {
		echo "Updating an existing entry";
	});

## DELETE examples

	Fast::delete('entry/:id', function($id) {
		echo "Deleting an entry";
	});

## OPTIONS examples

	Fast::options('entries', function() {
		echo "Getting the HTTP OPTIONS VERB about entries";
	});

## Middleware

You can add middleware before and after each response and request.

	Fast::before('authenticate', function() {
		echo "Authenticating";
	});

	Fast::after('listen', function() {
		echo "listen";
	});

	Fast::get('article', 'authenticate', function() {
		echo "Authenticate before seeing articles";
	});

	Fast::get('feed', 'listen', function() {
		echo "Registering a listening agent";
	});
