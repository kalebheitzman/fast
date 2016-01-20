# PHP 5.4 API Micro Framework

Fast is a PHP 5.4+ API Micro Framework for building APIs. Use the Fast::get(), Fast::post(), etc methods to build your api. Use your own databse libraries of choice and etc. We get you to the party and then it's your job to shine.

## Installation

Fast is available via a composer package. The first thing you should do is install composer if you haven't already.

Drop the following into your composer.json file and then run composer install from the command line.

	"require": {
		"kheitzman/fast": "dev-master"
	}

## Usage

Assuming that you're using index.php file to route requests, add this to the top of your index file.

	require 'vendor/autoload.php';
	use FastPHP\Fast as Fast;

This will use composers autoload feature and alias Fast as Fast for easier use.

Fast builds a stack based on middleware and routes that you specify. Middleware can be placed at different positions in the stack. By default all middleware is placed at 0. When a route is found, it is placed at position 20 in the stack. To run middleware before your route code runs, specify a position less than 20. To make middleware run after your route, specify a position greater than 20. You can change your default route position using an $appConfig.

## GET Examples

	Fast::get('/', function() {
		// by default returns server information
	});

	Fast::get('entries', function() {
		$data['entries'] = Entry::get_all();
		return $data;
	});

	Fast::get('entry/:id', function($id) {
		$data['entry'] = array(
			'title' => 'Lorem Ipsum ' . $id ,
			'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
			Mauris lorem ante, semper et lacus non, viverra iaculis velit. Fusce id
			lorem massa. Pellentesque placerat ligula eu faucibus suscipit. Proin mi
			erat, aliquet et mi in, maximus varius augue. Nullam elementum mauris justo,
			eget tempor purus tristique quis. Aenean vel turpis quis orci ultrices
			iaculis. Maecenas lacinia consequat massa blandit rutrum. Phasellus non
			libero tempor nunc gravida eleifend. Nam et nulla nunc. Curabitur eget nulla
			aliquet justo interdum pretium id ac tortor.',
			'author' => 'Kaleb Heitzman'
		);
		return $data;
	});

## POST Examples

	Fast::post('entry', function() {
		$data['success'] = 'Entry saved successfully.';
		return $data;
	});

## PUT Examples

	Fast::put('entry/:id', function($id) {
		$data['success'] = 'Entry updated successfully.';
		return $data;
	});

## DELETE examples

	Fast::delete('entry/:id', function($id) {
		$data['success'] = 'Entry deleted successfully.';
		return $data;
	});

## OPTIONS examples

	Fast::options('entries', function() {
		$data['allow'] = 'HEAD,GET,POST,PUT,DELETE,OPTIONS';
		return $data;
	});

## Middleware

You can add middleware before and after each response and request. The default position of middleware is 0. You can change this by specifying a position in the last argument of the closure. You can see we specified notify with a position of 50 in the stack.

	Fast::middleware('authenticate', function() {
		$data['auth'] = array(
			'authenticated' = true,
			'token' = 1234567890
		);
		return $data;
	}, 0);

	Fast::middleware('notify', function() {
		$data['notify'] array(
			'mailchimp_synced' = true,
		);
		return $data;
	}, 50);

	Fast::post('article', 'authenticate', function() {
		// ex., code to update an article in your db.
	});

	Fast::get('cron', 'notify', function() {
		// ex., code to notify someone that cron has run.
	});
