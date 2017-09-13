<?php

// Route::group(array(
//     "before" => array("session.remove")
// ), function() {
// Route::get('/get_microapp/{id}', 'MicroappController@fetch');

Route::get('/microapps', 'MicroappController@index');

// Route::group(array('before' => 'permission:adminorhigher'), function() {
Route::resource('/microapps', 'MicroappController', array('only' => array('show', 'store', 'update', 'destroy')));
Route::get('/microapps/export/{id}', 'MicroappController@export');
Route::get('/microapp/used/{id}', 'MicroappController@used');
// });

Route::post('/get_microapp/{id}', 'MicroappController@fetch');
Route::get('/get_microapp/{id}', 'MicroappController@fetch');
Route::get('/get_microapp/{id}/{map}', 'MicroappController@getByMap');

Route::post('/get_microapp/{id}/{map}', 'MicroappController@getByMap');
Route::post('/post_microapp/{id}/{map}', 'MicroappController@postByMap');

Route::post('/put_microapp/{id}/{map}', 'MicroappController@putByMap');
Route::post('/delete_microapp/{id}/{map}', 'MicroappController@deleteByMap');




Route::get('/terms', 'MicroappController@terms');
Route::get('/preferenceSummary/{page_id}/{widget_guid}', 'MicroappController@preferenceSummary');
Route::get('/publish/{page_id}/{widget_guid}', 'MicroappController@publish');


Route::get('/admin/microapps', function()
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('microapps::index', array('id'=>'false'));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});

Route::get('/admin/groups/{id}/microapps', function($id)
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('microapps::index', array('id'=>$id));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/microapps/{id}/app', function($id)
{
		$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
		$script = View::make('microapps::app', array('id'=>$id));
		// $microapp = Microapp::find($id);
		// if(validate::hasPermission(true, $microapp->group_id, false, false) ){
		// 	$microapp->sources = json_decode($microapp->sources);
		// 	$microapp->options = json_decode($microapp->options);
		// 	return $microapp;
		// }else{	
		// 	return Response::make(View::make('unauthorized'), 401);
		// }


		return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	
// Route::get('/microapps/{id}', 'MicroappController@byGroup');
