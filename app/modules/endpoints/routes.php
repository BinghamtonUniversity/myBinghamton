<?php


Route::group(array('before' => 'permission:adminorhigher'), function() {
	Route::get('/endpoints/plus/{group_id}', function($group_id){
		return array_merge(array('None', array('name' => 'Proxy', 'value' => 'Proxy', 'target' => Config::get('proxy.location')), array('name' => 'XML/RSS', 'value' => 'xml'), 'JSON', 'Internal', 'HTML', 'CSV'),Endpoint::where('group_id', '=', $group_id)->orWhere('group_id', '=', Config::get('app.global_group'))->select('name','id', 'target')->get()->toArray());
	});

	Route::get('/endpoints/list/{group_id}', function($group_id){
		return array_merge(array(array('name'=>'Internal (local)', 'value'=>'Internal', 'target'=>Config::get('app.PRIMARY_DOMAIN_LOCATION'))),Endpoint::where('group_id', '=', $group_id)->orWhere('group_id', '=', Config::get('app.global_group'))->select('name','id', 'target')->get()->toArray());
	});

});

Route::group(array('before' => 'permission:anyorhigher'), function() {

	Route::resource('/endpoints', 'EndpointController');


});


Route::get('/admin/endpoints', function()
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('endpoints::index', array('id'=>'false'));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	

Route::get('/admin/groups/{id}/endpoints', function($id)
{
	$menu = View::make('menu' , array("items" => Config::get('menu'), 'user'=>Auth::user()));
	$script = View::make('endpoints::index', array('id'=>$id));

	return View::make('admin_new' , array('menu' => $menu, 'content'=>'', 'script' => $script));
});	