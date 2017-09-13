<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => $_SERVER['PORTAL_DEBUG'],
	's3' => true,
	's3_bucket' => $_SERVER['S3_BUCKET'],
	'PRIMARY_DOMAIN_LOCATION' => $_SERVER['PORTAL_DOMAIN_LOCATION'],
	'key' => $_SERVER['ENCRYPTION_KEY'],
);
