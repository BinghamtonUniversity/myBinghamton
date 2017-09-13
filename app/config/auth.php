<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Authentication Driver
	|--------------------------------------------------------------------------
	|
	| This option controls the authentication driver that will be utilized.
	| This driver manages the retrieval and authentication of the users
	| attempting to get access to protected areas of your application.
	|
	| Supported: "database", "eloquent"
	|
	*/

	'driver' => 'eloquent',

	/*
	|--------------------------------------------------------------------------
	| Authentication Model
	|--------------------------------------------------------------------------
	|
	| When using the "Eloquent" authentication driver, we need to know which
	| Eloquent model should be used to retrieve your users. Of course, it
	| is often just the "User" model but you may use whatever you like.
	|
	*/

	'model' => 'User',

	/*
	|--------------------------------------------------------------------------
	| Authentication Table
	|--------------------------------------------------------------------------
	|
	| When using the "Database" authentication driver, we need to know which
	| table should be used to retrieve your users. We have chosen a basic
	| default value but you may easily change it to any table you like.
	|
	*/

	'table' => 'users',

	/*
	|--------------------------------------------------------------------------
	| Password Reminder Settings
	|--------------------------------------------------------------------------
	|
	| Here you may set the settings for password reminders, including a view
	| that should be used as your password reminder e-mail. You will also
	| be able to set the name of the table that holds the reset tokens.
	|
	| The "expire" time is the number of minutes that the reminder should be
	| considered valid. This security feature keeps tokens short-lived so
	| they have less time to be guessed. You may change this as needed.
	|
	*/

	'reminder' => array(

		'email' => 'emails.auth.reminder',

		'table' => 'password_reminders',

		'expire' => 60,

	),


		/*
		|--------------------------------------------------------------------------
		| PHPCas Hostname
		|--------------------------------------------------------------------------
		|
		| Exemple: 'cas.myuniv.edu'.
		|
		*/

		'cas_hostname' => '',

		/*
		|--------------------------------------------------------------------------
		| Use as Cas proxy ?
		|--------------------------------------------------------------------------
		*/

		'cas_proxy' => false,

		/*
		|--------------------------------------------------------------------------
		| Enable service to be proxied
		|--------------------------------------------------------------------------
		|
		| Example:
		| phpCAS::allowProxyChain(new CAS_ProxyChain(array(
		|                                 '/^https:\/\/app[0-9]\.example\.com\/rest\//',
		|                                 'http://client.example.com/'
		|                         )));
		| For the exemple above:
		|	'cas_service' => array('/^https:\/\/app[0-9]\.example\.com\/rest\//','http://client.example.com/'),
		*/

		'cas_service' => array(),

		/*
		|--------------------------------------------------------------------------
		| Cas Port
		|--------------------------------------------------------------------------
		|
		| Usually 443 is default
		|
		*/

		'cas_port' => 443,

		/*
		|--------------------------------------------------------------------------
		| CAS URI
		|--------------------------------------------------------------------------
		|
		| Sometimes is /cas
		|
		*/

		'cas_uri' => '',

		/*
		|--------------------------------------------------------------------------
		| CAS Validation
		|--------------------------------------------------------------------------
		|
		| CAS server SSL validation: 'self' for self-signed certificate, 'ca' for
		| certificate from a CA, empty for no SSL validation.
		|
		*/

		'cas_validation' => '',
		
		/*
		|--------------------------------------------------------------------------
		| CAS Certificate
		|--------------------------------------------------------------------------
		|
		| Path to the CAS certificate file
		|
		*/
		
		'cas_cert' => '/path/to/cert/file',
		
		/*
		|--------------------------------------------------------------------------
		| CAS Login URI
		|--------------------------------------------------------------------------
		|
		| Empty is fine
		|
		*/
		
		'cas_login_url' => '',
		
		/*
		|--------------------------------------------------------------------------
		| CAS Logout URI
		|--------------------------------------------------------------------------
		*/
		
		'cas_logout_url' => '',
	

);
