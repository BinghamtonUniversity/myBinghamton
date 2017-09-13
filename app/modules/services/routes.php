<?php

// Route::group(array(
//     "before" => array("session.remove")
// ), function() {
// Route::get('/get_service/{id}', 'ServiceController@fetch');

Route::get('/services', 'ServiceController@index');

// Route::group(array('before' => 'permission:adminorhigher'), function() {
	Route::resource('/services', 'ServiceController', array('only' => array('show', 'store', 'update', 'destroy')));
	Route::get('/services/export/{id}', 'ServiceController@export');
// });

Route::post('/get_service/{id}', 'ServiceController@fetch');
Route::get('/get_service/{id}', 'ServiceController@fetch');
Route::get('/get_service/{id}/{map}', 'ServiceController@getByMap');
Route::post('/get_service/{id}/{map}', 'ServiceController@getByMap');
Route::post('/post_service/{id}/{map}', 'ServiceController@postByMap');




Route::get('/terms', 'ServiceController@terms');
Route::get('/preferenceSummary/{page_id}/{widget_guid}', 'ServiceController@preferenceSummary');
Route::get('/publish/{page_id}/{widget_guid}', 'ServiceController@publish');

Route::get('/admin/services', function()
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('services::index', array('id'=>'false'));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/services/{id}/service', function($id)
{
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$script = View::make('services::service', array('id'=>$id));

		return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	